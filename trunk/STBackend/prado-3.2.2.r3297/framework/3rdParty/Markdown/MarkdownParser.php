<?php

#
# Markdown  -  A text-to-HTML conversion tool for web writers
#
# Copyright (c) 2004-2005 John Gruber
# <http://daringfireball.net/projects/markdown/>
#
# Copyright (c) 2004-2005 Michel Fortin - PHP Port
# <http://www.michelf.com/projects/php-markdown/>
#

/**
 * PHP5 version of the markdown parser.
 * Usage:
 * <code>
 * $markdown = new MarkdownParser;
 * echo $markdown->parse($text);
 * </code>
 */
class MarkdownParser
{
	private static $md_nested_brackets;
	private static $md_escape_table = array();
	private static $md_backslash_escape_table = array();
	private static $md_nested_brackets_depth = 6;

	protected $md_empty_element_suffix = " />";     # Change to ">" for HTML output
	protected $md_tab_width = 4;

	private $md_list_level = 0;
	private $md_urls = array();
	private $md_titles = array();
	private $md_html_blocks = array();

	public function __construct()
	{
		if(is_null(self::$md_nested_brackets))
			$this->initialize();
	}

	private function initialize()
	{
		self::$md_nested_brackets =
			str_repeat('(?>[^\[\]]+|\[', self::$md_nested_brackets_depth).
			str_repeat('\])*', self::$md_nested_brackets_depth);

		self::$md_escape_table = array(
			"\\" => md5("\\"),
			"`" => md5("`"),
			"*" => md5("*"),
			"_" => md5("_"),
			"{" => md5("{"),
			"}" => md5("}"),
			"[" => md5("["),
			"]" => md5("]"),
			"(" => md5("("),
			")" => md5(")"),
			">" => md5(">"),
			"#" => md5("#"),
			"+" => md5("+"),
			"-" => md5("-"),
			"." => md5("."),
			"!" => md5("!")
		);

		# Table of hash values for escaped characters:
		# Create an identical table but for escaped characters.
		foreach (self::$md_escape_table as $key => $char)
			self::$md_backslash_escape_table["\\$key"] = $char;
	}

	public function parse($text)
	{
	#
	# Main function. The order in which other subs are called here is
	# essential. Link and image substitutions need to happen before
	# _EscapeSpecialCharsWithinTagAttributes(), so that any *'s or _'s in the <a>
	# and <img> tags get encoded.
	#
		# Clear the hashes. If we don't clear these, you get conflicts
		# from other articles when generating a page which contains more than
		# one article (e.g. an index page that shows the N most recent
		# articles):
		$this->md_urls = array();
		$this->md_titles = array();
		$this->md_html_blocks = array();

		# Standardize line endings:
		#   DOS to Unix and Mac to Unix
		$text = str_replace(array("\r\n", "\r"), "\n", $text);

		# Make sure $text ends with a couple of newlines:
		$text .= "\n\n";

		# Convert all tabs to spaces.
		$text = $this->_Detab($text);

		# Strip any lines consisting only of spaces and tabs.
		# This makes subsequent regexen easier to write, because we can
		# match consecutive blank lines with /\n+/ instead of something
		# contorted like /[ \t]*\n+/ .
		$text = preg_replace('/^[ \t]+$/m', '', $text);

		# Turn block-level HTML blocks into hash entries
		$text = $this->_HashHTMLBlocks($text);

		# Strip link definitions, store in hashes.
		$text = $this->_StripLinkDefinitions($text);

		$text = $this->_RunBlockGamut($text);

		$text = $this->_UnescapeSpecialChars($text);

		return $text . "\n";
	}


	private function _StripLinkDefinitions($text) {
	#
	# Strips link definitions from text, stores the URLs and titles in
	# hash references.
	#
		$less_than_tab = $this->md_tab_width - 1;

		# Link defs are in the form: ^[id]: url "optional title"
		$text = preg_replace_callback('{
							^[ ]{0,'.$less_than_tab.'}\[(.+)\]:	# id = $1
							  [ \t]*
							  \n?				# maybe *one* newline
							  [ \t]*
							<?(\S+?)>?			# url = $2
							  [ \t]*
							  \n?				# maybe one newline
							  [ \t]*
							(?:
								(?<=\s)			# lookbehind for whitespace
								["(]
								(.+?)			# title = $3
								[")]
								[ \t]*
							)?	# title is optional
							(?:\n+|\Z)
			}xm',
			array($this,'_StripLinkDefinitions_callback'),
			$text);
		return $text;
	}

	private function _StripLinkDefinitions_callback($matches) {
		$link_id = strtolower($matches[1]);
		$this->md_urls[$link_id] = $this->_EncodeAmpsAndAngles($matches[2]);
		if (isset($matches[3]))
			$this->md_titles[$link_id] = str_replace('"', '&quot;', $matches[3]);
		return ''; # String that will replace the block
	}


	private function _HashHTMLBlocks($text) {
		$less_than_tab = $this->md_tab_width - 1;

		# Hashify HTML blocks:
		# We only want to do this for block-level HTML tags, such as headers,
		# lists, and tables. That's because we still want to wrap <p>s around
		# "paragraphs" that are wrapped in non-block-level tags, such as anchors,
		# phrase emphasis, and spans. The list of tags we're looking for is
		# hard-coded:
		$block_tags_a = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|'.
						'script|noscript|form|fieldset|iframe|math|ins|del';
		$block_tags_b = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|'.
						'script|noscript|form|fieldset|iframe|math';

		# First, look for nested blocks, e.g.:
		# 	<div>
		# 		<div>
		# 		tags for inner block must be indented.
		# 		</div>
		# 	</div>
		#
		# The outermost tags must start at the left margin for this to match, and
		# the inner nested divs must be indented.
		# We need to do this before the next, more liberal match, because the next
		# match will start at the first `<div>` and stop at the first `</div>`.
		$text = preg_replace_callback("{
					(						# save in $1
						^					# start of line  (with /m)
						<($block_tags_a)	# start tag = $2
						\\b					# word break
						(.*\\n)*?			# any number of lines, minimally matching
						</\\2>				# the matching end tag
						[ \\t]*				# trailing spaces/tabs
						(?=\\n+|\\Z)	# followed by a newline or end of document
					)
			}xm",
			array($this,'_HashHTMLBlocks_callback'),
			$text);

		#
		# Now match more liberally, simply from `\n<tag>` to `</tag>\n`
		#
		$text = preg_replace_callback("{
					(						# save in $1
						^					# start of line  (with /m)
						<($block_tags_b)	# start tag = $2
						\\b					# word break
						(.*\\n)*?			# any number of lines, minimally matching
						.*</\\2>				# the matching end tag
						[ \\t]*				# trailing spaces/tabs
						(?=\\n+|\\Z)	# followed by a newline or end of document
					)
			}xm",
			array($this,'_HashHTMLBlocks_callback'),
			$text);

		# Special case just for <hr />. It was easier to make a special case than
		# to make the other regex more complicated.
		$text = preg_replace_callback('{
					(?:
						(?<=\n\n)		# Starting after a blank line
						|				# or
						\A\n?			# the beginning of the doc
					)
					(						# save in $1
						[ ]{0,'.$less_than_tab.'}
						<(hr)				# start tag = $2
						\b					# word break
						([^<>])*?			#
						/?>					# the matching end tag
						[ \t]*
						(?=\n{2,}|\Z)		# followed by a blank line or end of document
					)
			}x',
			array($this,'_HashHTMLBlocks_callback'),
			$text);

		# Special case for standalone HTML comments:
		$text = preg_replace_callback('{
					(?:
						(?<=\n\n)		# Starting after a blank line
						|				# or
						\A\n?			# the beginning of the doc
					)
					(						# save in $1
						[ ]{0,'.$less_than_tab.'}
						(?s:
							<!
							(--.*?--\s*)+
							>
						)
						[ \t]*
						(?=\n{2,}|\Z)		# followed by a blank line or end of document
					)
				}x',
				array($this,'_HashHTMLBlocks_callback'),
				$text);

		return $text;
	}
	private function _HashHTMLBlocks_callback($matches) {
		$text = $matches[1];
		$key = md5($text);
		$this->md_html_blocks[$key] = $text;
		return "\n\n$key\n\n"; # String that will replace the block
	}


	private function _RunBlockGamut($text) {
	#
	# These are all the transformations that form block-level
	# tags like paragraphs, headers, and list items.
	#
		$text = $this->_DoHeaders($text);

		# Do Horizontal Rules:
		$text = preg_replace(
			array('{^[ ]{0,2}([ ]?\*[ ]?){3,}[ \t]*$}mx',
				  '{^[ ]{0,2}([ ]? -[ ]?){3,}[ \t]*$}mx',
				  '{^[ ]{0,2}([ ]? _[ ]?){3,}[ \t]*$}mx'),
			"\n<hr{$this->md_empty_element_suffix}\n",
			$text);

		$text = $this->_DoLists($text);
		$text = $this->_DoCodeBlocks($text);
		$text = $this->_DoBlockQuotes($text);

		# We already ran _HashHTMLBlocks() before, in Markdown(), but that
		# was to escape raw HTML in the original Markdown source. This time,
		# we're escaping the markup we've just created, so that we don't wrap
		# <p> tags around block-level tags.
		$text = $this->_HashHTMLBlocks($text);
		$text = $this->_FormParagraphs($text);

		return $text;
	}


	private function _RunSpanGamut($text) {
	#
	# These are all the transformations that occur *within* block-level
	# tags like paragraphs, headers, and list items.
	#

		$text = $this->_DoCodeSpans($text);

		$text = $this->_EscapeSpecialChars($text);

		# Process anchor and image tags. Images must come first,
		# because ![foo][f] looks like an anchor.
		$text = $this->_DoImages($text);
		$text = $this->_DoAnchors($text);

		# Make links out of things like `<http://example.com/>`
		# Must come after _DoAnchors(), because you can use < and >
		# delimiters in inline links like [this](<url>).
		$text = $this->_DoAutoLinks($text);
		$text = $this->_EncodeAmpsAndAngles($text);
		$text = $this->_DoItalicsAndBold($text);

		# Do hard breaks:
		$text = preg_replace('/ {2,}\n/', "<br{$this->md_empty_element_suffix}\n", $text);

		return $text;
	}


	private function _EscapeSpecialChars($text) {
		$tokens = $this->_TokenizeHTML($text);

		$text = '';   # rebuild $text from the tokens
	#	$in_pre = 0;  # Keep track of when we're inside <pre> or <code> tags.
	#	$tags_to_skip = "!<(/?)(?:pre|code|kbd|script|math)[\s>]!";

		foreach ($tokens as $cur_token) {
			if ($cur_token[0] == 'tag') {
				# Within tags, encode * and _ so they don't conflict
				# with their use in Markdown for italics and strong.
				# We're replacing each such character with its
				# corresponding MD5 checksum value; this is likely
				# overkill, but it should prevent us from colliding
				# with the escape values by accident.
				$cur_token[1] = str_replace(array('*', '_'),
					array(self::$md_escape_table['*'], self::$md_escape_table['_']),
					$cur_token[1]);
				$text .= $cur_token[1];
			} else {
				$t = $cur_token[1];
				$t = $this->_EncodeBackslashEscapes($t);
				$text .= $t;
			}
		}
		return $text;
	}


	private function _DoAnchors($text) {
	#
	# Turn Markdown link shortcuts into XHTML <a> tags.
	#
		#
		# First, handle reference-style links: [link text] [id]
		#
		$bracket = self::$md_nested_brackets;
		$text = preg_replace_callback("{
			(					# wrap whole match in $1
			  \\[
				({$bracket})	# link text = $2
			  \\]

			  [ ]?				# one optional space
			  (?:\\n[ ]*)?		# one optional newline followed by spaces

			  \\[
				(.*?)		# id = $3
			  \\]
			)
			}xs",
			array($this,'_DoAnchors_reference_callback'), $text);

		#
		# Next, inline-style links: [link text](url "optional title")
		#
		$text = preg_replace_callback("{
			(				# wrap whole match in $1
			  \\[
				({$bracket})	# link text = $2
			  \\]
			  \\(			# literal paren
				[ \\t]*
				<?(.*?)>?	# href = $3
				[ \\t]*
				(			# $4
				  (['\"])	# quote char = $5
				  (.*?)		# Title = $6
				  \\5		# matching quote
				)?			# title is optional
			  \\)
			)
			}xs",
			array($this,'_DoAnchors_inline_callback'), $text);

		return $text;
	}
	private function _DoAnchors_reference_callback($matches) {
		$whole_match = $matches[1];
		$link_text   = $matches[2];
		$link_id     = strtolower($matches[3]);

		if ($link_id == "") {
			$link_id = strtolower($link_text); # for shortcut links like [this][].
		}

		if (isset($this->md_urls[$link_id])) {
			$url = $this->md_urls[$link_id];
			# We've got to encode these to avoid conflicting with italics/bold.
			$url = str_replace(array('*', '_'),
							   array(self::$md_escape_table['*'], self::$md_escape_table['_']),
							   $url);
			$result = "<a href=\"$url\"";
			if ( isset( $this->md_titles[$link_id] ) ) {
				$title = $this->md_titles[$link_id];
				$title = str_replace(array('*',     '_'),
									 array(self::$md_escape_table['*'],
										   self::$md_escape_table['_']), $title);
				$result .=  " title=\"$title\"";
			}
			$result .= ">$link_text</a>";
		}
		else {
			$result = $whole_match;
		}
		return $result;
	}
	private function _DoAnchors_inline_callback($matches) {
		$whole_match	= $matches[1];
		$link_text		= $matches[2];
		$url			= $matches[3];
		$title			=& $matches[6];

		# We've got to encode these to avoid conflicting with italics/bold.
		$url = str_replace(array('*', '_'),
						   array(self::$md_escape_table['*'], self::$md_escape_table['_']),
						   $url);
		$result = "<a href=\"$url\"";
		if (isset($title)) {
			$title = str_replace('"', '&quot;', $title);
			$title = str_replace(array('*', '_'),
								 array(self::$md_escape_table['*'], self::$md_escape_table['_']),
								 $title);
			$result .=  " title=\"$title\"";
		}

		$result .= ">$link_text</a>";

		return $result;
	}


	private function _DoImages($text) {
	#
	# Turn Markdown image shortcuts into <img> tags.
	#
		#
		# First, handle reference-style labeled images: ![alt text][id]
		#
		$text = preg_replace_callback('{
			(				# wrap whole match in $1
			  !\[
				('.self::$md_nested_brackets.')		# alt text = $2
			  \]

			  [ ]?				# one optional space
			  (?:\n[ ]*)?		# one optional newline followed by spaces

			  \[
				(.*?)		# id = $3
			  \]

			)
			}xs',
			array($this,'_DoImages_reference_callback'), $text);

		#
		# Next, handle inline images:  ![alt text](url "optional title")
		# Don't forget: encode * and _

		$text = preg_replace_callback('{
			(				# wrap whole match in $1
			  !\[
				('.self::$md_nested_brackets.')		# alt text = $2
			  \]
			  \(			# literal paren
				[ \t]*
				<?(\S+?)>?	# src url = $3
				[ \t]*
				(			# $4
				  ([\'"])	# quote char = $5
				  (.*?)		# title = $6
				  \5		# matching quote
				  [ \t]*
				)?			# title is optional
			  \)
			)
			}xs',
			array($this,'_DoImages_inline_callback'), $text);

		return $text;
	}
	private function _DoImages_reference_callback($matches) {
		$whole_match = $matches[1];
		$alt_text    = $matches[2];
		$link_id     = strtolower($matches[3]);

		if ($link_id == "") {
			$link_id = strtolower($alt_text); # for shortcut links like ![this][].
		}

		$alt_text = str_replace('"', '&quot;', $alt_text);
		if (isset($this->md_urls[$link_id])) {
			$url = $this->md_urls[$link_id];
			# We've got to encode these to avoid conflicting with italics/bold.
			$url = str_replace(array('*', '_'),
							   array(self::$md_escape_table['*'], self::$md_escape_table['_']),
							   $url);
			$result = "<img src=\"$url\" alt=\"$alt_text\"";
			if (isset($this->md_titles[$link_id])) {
				$title = $this->md_titles[$link_id];
				$title = str_replace(array('*', '_'),
									 array(self::$md_escape_table['*'],
										   self::$md_escape_table['_']), $title);
				$result .=  " title=\"$title\"";
			}
			$result .= $this->md_empty_element_suffix;
		}
		else {
			# If there's no such link ID, leave intact:
			$result = $whole_match;
		}

		return $result;
	}
	private function _DoImages_inline_callback($matches) {
		$whole_match	= $matches[1];
		$alt_text		= $matches[2];
		$url			= $matches[3];
		$title			= '';
		if (isset($matches[6])) {
			$title		= $matches[6];
		}

		$alt_text = str_replace('"', '&quot;', $alt_text);
		$title    = str_replace('"', '&quot;', $title);
		# We've got to encode these to avoid conflicting with italics/bold.
		$url = str_replace(array('*', '_'),
						   array(self::$md_escape_table['*'], self::$md_escape_table['_']),
						   $url);
		$result = "<img src=\"$url\" alt=\"$alt_text\"";
		if (isset($title)) {
			$title = str_replace(array('*', '_'),
								 array(self::$md_escape_table['*'], self::$md_escape_table['_']),
								 $title);
			$result .=  " title=\"$title\""; # $title already quoted
		}
		$result .= $this->md_empty_element_suffix;

		return $result;
	}


	private function _DoHeaders($text) {
		# Setext-style headers:
		#	  Header 1
		#	  ========
		#
		#	  Header 2
		#	  --------
		#
		$text = preg_replace(
			array('{ ^(.+)[ \t]*\n=+[ \t]*\n+ }emx',
				  '{ ^(.+)[ \t]*\n-+[ \t]*\n+ }emx'),
			array("'<h1>'.\$this->_RunSpanGamut(\$this->_UnslashQuotes('\\1')).'</h1>\n\n'",
				  "'<h2>'.\$this->_RunSpanGamut(\$this->_UnslashQuotes('\\1')).'</h2>\n\n'"),
			$text);

		# atx-style headers:
		#	# Header 1
		#	## Header 2
		#	## Header 2 with closing hashes ##
		#	...
		#	###### Header 6
		#
		$text = preg_replace("{
				^(\\#{1,6})	# $1 = string of #'s
				[ \\t]*
				(.+?)		# $2 = Header text
				[ \\t]*
				\\#*			# optional closing #'s (not counted)
				\\n+
			}xme",
			"'<h'.strlen('\\1').'>'.\$this->_RunSpanGamut(\$this->_UnslashQuotes('\\2')).'</h'.strlen('\\1').'>\n\n'",
			$text);

		return $text;
	}


	private function _DoLists($text) {
	#
	# Form HTML ordered (numbered) and unordered (bulleted) lists.
	#
		$less_than_tab = $this->md_tab_width - 1;

		# Re-usable patterns to match list item bullets and number markers:
		$marker_ul  = '[*+-]';
		$marker_ol  = '\d+[.]';
		$marker_any = "(?:$marker_ul|$marker_ol)";

		$markers = array($marker_ul, $marker_ol);

		foreach ($markers as $marker) {
			# Re-usable pattern to match any entirel ul or ol list:
			$whole_list = '
				(								# $1 = whole list
				  (								# $2
					[ ]{0,'.$less_than_tab.'}
					('.$marker.')				# $3 = first list item marker
					[ \t]+
				  )
				  (?s:.+?)
				  (								# $4
					  \z
					|
					  \n{2,}
					  (?=\S)
					  (?!						# Negative lookahead for another list item marker
						[ \t]*
						'.$marker.'[ \t]+
					  )
				  )
				)
			'; // mx

			# We use a different prefix before nested lists than top-level lists.
			# See extended comment in _ProcessListItems().

			if ($this->md_list_level) {
				$text = preg_replace_callback('{
						^
						'.$whole_list.'
					}mx',
					array($this,'_DoLists_callback_top'), $text);
			}
			else {
				$text = preg_replace_callback('{
						(?:(?<=\n\n)|\A\n?)
						'.$whole_list.'
					}mx',
					array($this,'_DoLists_callback_nested'), $text);
			}
		}

		return $text;
	}
	private function _DoLists_callback_top($matches) {
		# Re-usable patterns to match list item bullets and number markers:
		$marker_ul  = '[*+-]';
		$marker_ol  = '\d+[.]';
		$marker_any = "(?:$marker_ul|$marker_ol)";

		$list = $matches[1];
		$list_type = preg_match("/$marker_ul/", $matches[3]) ? "ul" : "ol";

		$marker_any = ( $list_type == "ul" ? $marker_ul : $marker_ol );

		# Turn double returns into triple returns, so that we can make a
		# paragraph for the last item in a list, if necessary:
		$list = preg_replace("/\n{2,}/", "\n\n\n", $list);
		$result = $this->_ProcessListItems($list, $marker_any);

		# Trim any trailing whitespace, to put the closing `</$list_type>`
		# up on the preceding line, to get it past the current stupid
		# HTML block parser. This is a hack to work around the terrible
		# hack that is the HTML block parser.
		$result = rtrim($result);
		$result = "<$list_type>" . $result . "</$list_type>\n";
		return $result;
	}
	private function _DoLists_callback_nested($matches) {
		# Re-usable patterns to match list item bullets and number markers:
		$marker_ul  = '[*+-]';
		$marker_ol  = '\d+[.]';
		$marker_any = "(?:$marker_ul|$marker_ol)";

		$list = $matches[1];
		$list_type = preg_match("/$marker_ul/", $matches[3]) ? "ul" : "ol";

		$marker_any = ( $list_type == "ul" ? $marker_ul : $marker_ol );

		# Turn double returns into triple returns, so that we can make a
		# paragraph for the last item in a list, if necessary:
		$list = preg_replace("/\n{2,}/", "\n\n\n", $list);
		$result = $this->_ProcessListItems($list, $marker_any);
		$result = "<$list_type>\n" . $result . "</$list_type>\n";
		return $result;
	}


	private function _ProcessListItems($list_str, $marker_any) {
	#
	#	Process the contents of a single ordered or unordered list, splitting it
	#	into individual list items.
	#

		# The $md_list_level keeps track of when we're inside a list.
		# Each time we enter a list, we increment it; when we leave a list,
		# we decrement. If it's zero, we're not in a list anymore.
		#
		# We do this because when we're not inside a list, we want to treat
		# something like this:
		#
		#		I recommend upgrading to version
		#		8. Oops, now this line is treated
		#		as a sub-list.
		#
		# As a single paragraph, despite the fact that the second line starts
		# with a digit-period-space sequence.
		#
		# Whereas when we're inside a list (or sub-list), that line will be
		# treated as the start of a sub-list. What a kludge, huh? This is
		# an aspect of Markdown's syntax that's hard to parse perfectly
		# without resorting to mind-reading. Perhaps the solution is to
		# change the syntax rules such that sub-lists must start with a
		# starting cardinal number; e.g. "1." or "a.".

		$this->md_list_level++;

		# trim trailing blank lines:
		$list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

		$list_str = preg_replace_callback('{
			(\n)?							# leading line = $1
			(^[ \t]*)						# leading whitespace = $2
			('.$marker_any.') [ \t]+		# list marker = $3
			((?s:.+?)						# list item text   = $4
			(\n{1,2}))
			(?= \n* (\z | \2 ('.$marker_any.') [ \t]+))
			}xm',
			array($this,'_ProcessListItems_callback'), $list_str);

		$this->md_list_level--;
		return $list_str;
	}
	private function _ProcessListItems_callback($matches) {
		$item = $matches[4];
		$leading_line =& $matches[1];
		$leading_space =& $matches[2];

		if ($leading_line || preg_match('/\n{2,}/', $item)) {
			$item = $this->_RunBlockGamut($this->_Outdent($item));
		}
		else {
			# Recursion for sub-lists:
			$item = $this->_DoLists($this->_Outdent($item));
			$item = preg_replace('/\n+$/', '', $item);
			$item = $this->_RunSpanGamut($item);
		}

		return "<li>" . $item . "</li>\n";
	}


	private function _DoCodeBlocks($text) {
	#
	#	Process Markdown `<pre><code>` blocks.
	#
		$text = preg_replace_callback('{
				(?:\n\n|\A)
				(	            # $1 = the code block -- one or more lines, starting with a space/tab
				  (?:
					(?:[ ]{'.$this->md_tab_width.'} | \t)  # Lines must start with a tab or a tab-width of spaces
					.*\n+
				  )+
				)
				((?=^[ ]{0,'.$this->md_tab_width.'}\S)|\Z)	# Lookahead for non-space at line-start, or end of doc
			}xm',
			array($this,'_DoCodeBlocks_callback'), $text);

		return $text;
	}
	private function _DoCodeBlocks_callback($matches) {
		$codeblock = $matches[1];

		$codeblock = $this->_EncodeCode($this->_Outdent($codeblock));
	//	$codeblock = _Detab($codeblock);
		# trim leading newlines and trailing whitespace
		$codeblock = preg_replace(array('/\A\n+/', '/\s+\z/'), '', $codeblock);

		$result = "\n\n<pre><code>" . $codeblock . "\n</code></pre>\n\n";

		return $result;
	}


	private function _DoCodeSpans($text) {
	#
	# 	*	Backtick quotes are used for <code></code> spans.
	#
	# 	*	You can use multiple backticks as the delimiters if you want to
	# 		include literal backticks in the code span. So, this input:
	#
	#		  Just type ``foo `bar` baz`` at the prompt.
	#
	#	  	Will translate to:
	#
	#		  <p>Just type <code>foo `bar` baz</code> at the prompt.</p>
	#
	#		There's no arbitrary limit to the number of backticks you
	#		can use as delimters. If you need three consecutive backticks
	#		in your code, use four for delimiters, etc.
	#
	#	*	You can use spaces to get literal backticks at the edges:
	#
	#		  ... type `` `bar` `` ...
	#
	#	  	Turns to:
	#
	#		  ... type <code>`bar`</code> ...
	#
		$text = preg_replace_callback('@
				(?<!\\\)	# Character before opening ` can\'t be a backslash
				(`+)		# $1 = Opening run of `
				(.+?)		# $2 = The code block
				(?<!`)
				\1			# Matching closer
				(?!`)
			@xs',
			array($this,'_DoCodeSpans_callback'), $text);

		return $text;
	}
	private function _DoCodeSpans_callback($matches) {
		$c = $matches[2];
		$c = preg_replace('/^[ \t]*/', '', $c); # leading whitespace
		$c = preg_replace('/[ \t]*$/', '', $c); # trailing whitespace
		$c = $this->_EncodeCode($c);
		return "<code>$c</code>";
	}


	private function _EncodeCode($_) {
	#
	# Encode/escape certain characters inside Markdown code runs.
	# The point is that in code, these characters are literals,
	# and lose their special Markdown meanings.
	#
		# Encode all ampersands; HTML entities are not
		# entities within a Markdown code span.
		$_ = str_replace('&', '&amp;', $_);

		# Do the angle bracket song and dance:
		$_ = str_replace(array('<',    '>'),
						 array('&lt;', '&gt;'), $_);

		# Now, escape characters that are magic in Markdown:
		$_ = str_replace(array_keys(self::$md_escape_table),
						 array_values(self::$md_escape_table), $_);

		return $_;
	}


	private function _DoItalicsAndBold($text) {
		# <strong> must go first:
		$text = preg_replace('{
				(						# $1: Marker
					(?<!\*\*) \*\* |	#     (not preceded by two chars of
					(?<!__)   __		#      the same marker)
				)
				(?=\S) 					# Not followed by whitespace
				(?!\1)					#   or two others marker chars.
				(						# $2: Content
					(?:
						[^*_]+?			# Anthing not em markers.
					|
										# Balence any regular emphasis inside.
						([*_]) (?=\S) .+? (?<=\S) \3	# $3: em char (* or _)
					|
						(?! \1 ) .		# Allow unbalenced * and _.
					)+?
				)
				(?<=\S) \1				# End mark not preceded by whitespace.
			}sx',
			'<strong>\2</strong>', $text);
		# Then <em>:
		$text = preg_replace(
			'{ ( (?<!\*)\* | (?<!_)_ ) (?=\S) (?! \1) (.+?) (?<=\S) \1 }sx',
			'<em>\2</em>', $text);

		return $text;
	}


	private function _DoBlockQuotes($text) {
		$text = preg_replace_callback('/
			  (								# Wrap whole match in $1
				(
				  ^[ \t]*>[ \t]?			# ">" at the start of a line
					.+\n					# rest of the first line
				  (.+\n)*					# subsequent consecutive lines
				  \n*						# blanks
				)+
			  )
			/xm',
			array($this,'_DoBlockQuotes_callback'), $text);

		return $text;
	}
	private function _DoBlockQuotes_callback($matches) {
		$bq = $matches[1];
		# trim one level of quoting - trim whitespace-only lines
		$bq = preg_replace(array('/^[ \t]*>[ \t]?/m', '/^[ \t]+$/m'), '', $bq);
		$bq = $this->_RunBlockGamut($bq);		# recurse

		$bq = preg_replace('/^/m', "  ", $bq);
		# These leading spaces screw with <pre> content, so we need to fix that:
		$bq = preg_replace_callback('{(\s*<pre>.+?</pre>)}sx',
									array($this,'_DoBlockQuotes_callback2'), $bq);

		return "<blockquote>\n$bq\n</blockquote>\n\n";
	}
	private function _DoBlockQuotes_callback2($matches) {
		$pre = $matches[1];
		$pre = preg_replace('/^  /m', '', $pre);
		return $pre;
	}


	private function _FormParagraphs($text) {
	#
	#	Params:
	#		$text - string to process with html <p> tags
	#
		# Strip leading and trailing lines:
		$text = preg_replace(array('/\A\n+/', '/\n+\z/'), '', $text);

		$grafs = preg_split('/\n{2,}/', $text, -1, PREG_SPLIT_NO_EMPTY);

		#
		# Wrap <p> tags.
		#
		foreach ($grafs as $key => $value) {
			if (!isset( $this->md_html_blocks[$value] )) {
				$value = $this->_RunSpanGamut($value);
				$value = preg_replace('/^([ \t]*)/', '<p>', $value);
				$value .= "</p>";
				$grafs[$key] = $value;
			}
		}

		#
		# Unhashify HTML blocks
		#
		foreach ($grafs as $key => $value) {
			if (isset( $this->md_html_blocks[$value] )) {
				$grafs[$key] = $this->md_html_blocks[$value];
			}
		}

		return implode("\n\n", $grafs);
	}


	private function _EncodeAmpsAndAngles($text) {
	# Smart processing for ampersands and angle brackets that need to be encoded.

		# Ampersand-encoding based entirely on Nat Irons's Amputator MT plugin:
		#   http://bumppo.net/projects/amputator/
		$text = preg_replace('/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/',
							 '&amp;', $text);;

		# Encode naked <'s
		$text = preg_replace('{<(?![a-z/?\$!])}i', '&lt;', $text);

		return $text;
	}


	private function _EncodeBackslashEscapes($text) {
	#
	#	Parameter:  String.
	#	Returns:    The string, with after processing the following backslash
	#				escape sequences.
	#
		# Must process escaped backslashes first.
		return str_replace(array_keys(self::$md_backslash_escape_table),
						   array_values(self::$md_backslash_escape_table), $text);
	}


	private function _DoAutoLinks($text) {
		$text = preg_replace("!<((https?|ftp):[^'\">\\s]+)>!",
							 '<a href="\1">\1</a>', $text);

		# Email addresses: <address@domain.foo>
		$text = preg_replace('{
			<
			(?:mailto:)?
			(
				[-.\w]+
				\@
				[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]+
			)
			>
			}exi',
			"\$this->_EncodeEmailAddress(\$this->_UnescapeSpecialChars(\$this->_UnslashQuotes('\\1')))",
			$text);

		return $text;
	}


	private function _EncodeEmailAddress($addr) {
	#
	#	Input: an email address, e.g. "foo@example.com"
	#
	#	Output: the email address as a mailto link, with each character
	#		of the address encoded as either a decimal or hex entity, in
	#		the hopes of foiling most address harvesting spam bots. E.g.:
	#
	#	  <a href="&#x6D;&#97;&#105;&#108;&#x74;&#111;:&#102;&#111;&#111;&#64;&#101;
	#		x&#x61;&#109;&#x70;&#108;&#x65;&#x2E;&#99;&#111;&#109;">&#102;&#111;&#111;
	#		&#64;&#101;x&#x61;&#109;&#x70;&#108;&#x65;&#x2E;&#99;&#111;&#109;</a>
	#
	#	Based by a filter by Matthew Wickline, posted to the BBEdit-Talk
	#	mailing list: <http://tinyurl.com/yu7ue>
	#
		$addr = "mailto:" . $addr;
		$length = strlen($addr);

		# leave ':' alone (to spot mailto: later)
		$addr = preg_replace_callback('/([^\:])/',
									  array($this,'_EncodeEmailAddress_callback'), $addr);

		$addr = "<a href=\"$addr\">$addr</a>";
		# strip the mailto: from the visible part
		$addr = preg_replace('/">.+?:/', '">', $addr);

		return $addr;
	}
	private function _EncodeEmailAddress_callback($matches) {
		$char = $matches[1];
		$r = rand(0, 100);
		# roughly 10% raw, 45% hex, 45% dec
		# '@' *must* be encoded. I insist.
		if ($r > 90 && $char != '@') return $char;
		if ($r < 45) return '&#x'.dechex(ord($char)).';';
		return '&#'.ord($char).';';
	}


	private function _UnescapeSpecialChars($text) {
	#
	# Swap back in all the special characters we've hidden.
	#
		return str_replace(array_values(self::$md_escape_table),
						   array_keys(self::$md_escape_table), $text);
	}


	# _TokenizeHTML is shared between PHP Markdown and PHP SmartyPants.
	# We only define it if it is not already defined.

	private function _TokenizeHTML($str) {
	#
	#   Parameter:  String containing HTML markup.
	#   Returns:    An array of the tokens comprising the input
	#               string. Each token is either a tag (possibly with nested,
	#               tags contained therein, such as <a href="<MTFoo>">, or a
	#               run of text between tags. Each element of the array is a
	#               two-element array; the first is either 'tag' or 'text';
	#               the second is the actual value.
	#
	#
	#   Regular expression derived from the _tokenize() subroutine in
	#   Brad Choate's MTRegex plugin.
	#   <http://www.bradchoate.com/past/mtregex.php>
	#
		$index = 0;
		$tokens = array();

		$match = '(?s:<!(?:--.*?--\s*)+>)|'.	# comment
				 '(?s:<\?.*?\?>)|'.				# processing instruction
												# regular tags
				 '(?:<[/!$]?[-a-zA-Z0-9:]+\b(?>[^"\'>]+|"[^"]*"|\'[^\']*\')*>)';

		$parts = preg_split("{($match)}", $str, -1, PREG_SPLIT_DELIM_CAPTURE);

		foreach ($parts as $part) {
			if (++$index % 2 && $part != '')
				$tokens[] = array('text', $part);
			else
				$tokens[] = array('tag', $part);
		}

		return $tokens;
	}

	private function _Outdent($text) {
	#
	# Remove one level of line-leading tabs or spaces
	#
		return preg_replace("/^(\\t|[ ]{1,".$this->md_tab_width."})/m", "", $text);
	}


	private function _Detab($text) {
	#
	# Replace tabs with the appropriate amount of space.
	#
		# For each line we separate the line in blocks delemited by
		# tab characters. Then we reconstruct every line by adding the
		# appropriate number of space between each blocks.

		$lines = explode("\n", $text);
		$text = "";

		foreach ($lines as $line) {
			# Split in blocks.
			$blocks = explode("\t", $line);
			# Add each blocks to the line.
			$line = $blocks[0];
			unset($blocks[0]); # Do not add first block twice.
			foreach ($blocks as $block) {
				# Calculate amount of space, insert spaces, insert block.
				$amount = $this->md_tab_width - strlen($line) % $this->md_tab_width;
				$line .= str_repeat(" ", $amount) . $block;
			}
			$text .= "$line\n";
		}
		return $text;
	}


	private function _UnslashQuotes($text) {
	#
	#	This function is useful to remove automaticaly slashed double quotes
	#	when using preg_replace and evaluating an expression.
	#	Parameter:  String.
	#	Returns:    The string with any slash-double-quote (\") sequence replaced
	#				by a single double quote.
	#
		return str_replace('\"', '"', $text);
	}
}

/*

PHP Markdown
============

Description
-----------

This is a PHP translation of the original Markdown formatter written in
Perl by John Gruber.

Markdown is a text-to-HTML filter; it translates an easy-to-read /
easy-to-write structured text format into HTML. Markdown's text format
is most similar to that of plain text email, and supports features such
as headers, *emphasis*, code blocks, blockquotes, and links.

Markdown's syntax is designed not as a generic markup language, but
specifically to serve as a front-end to (X)HTML. You can use span-level
HTML tags anywhere in a Markdown document, and you can use block level
HTML tags (like <div> and <table> as well).

For more information about Markdown's syntax, see:

<http://daringfireball.net/projects/markdown/>


Bugs
----

To file bug reports please send email to:

<michel.fortin@michelf.com>

Please include with your report: (1) the example input; (2) the output you
expected; (3) the output Markdown actually produced.


Version History
---------------

See the readme file for detailed release notes for this version.

1.0.1c - 9 Dec 2005

1.0.1b - 6 Jun 2005

1.0.1a - 15 Apr 2005

1.0.1 - 16 Dec 2004

1.0 - 21 Aug 2004


Author & Contributors
---------------------

Original Perl version by John Gruber
<http://daringfireball.net/>

PHP port and other contributions by Michel Fortin
<http://www.michelf.com/>


Copyright and License
---------------------

Copyright (c) 2004-2005 Michel Fortin
<http://www.michelf.com/>
All rights reserved.

Copyright (c) 2003-2004 John Gruber
<http://daringfireball.net/>
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are
met:

*	Redistributions of source code must retain the above copyright notice,
	this list of conditions and the following disclaimer.

*	Redistributions in binary form must reproduce the above copyright
	notice, this list of conditions and the following disclaimer in the
	documentation and/or other materials provided with the distribution.

*	Neither the name "Markdown" nor the names of its contributors may
	be used to endorse or promote products derived from this software
	without specific prior written permission.

This software is provided by the copyright holders and contributors "as
is" and any express or implied warranties, including, but not limited
to, the implied warranties of merchantability and fitness for a
particular purpose are disclaimed. In no event shall the copyright owner
or contributors be liable for any direct, indirect, incidental, special,
exemplary, or consequential damages (including, but not limited to,
procurement of substitute goods or services; loss of use, data, or
profits; or business interruption) however caused and on any theory of
liability, whether in contract, strict liability, or tort (including
negligence or otherwise) arising in any way out of the use of this
software, even if advised of the possibility of such damage.

*/