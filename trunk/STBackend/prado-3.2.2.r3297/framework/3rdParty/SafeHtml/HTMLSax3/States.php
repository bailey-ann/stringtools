<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Alexander Zhukov <alex@veresk.ru> Original port from Python |
// | Authors: Harry Fuecks <hfuecks@phppatterns.com> Port to PEAR + more  |
// | Authors: Many @ Sitepointforums Advanced PHP Forums                  |
// +----------------------------------------------------------------------+
//
// $Id: States.php,v 1.1 2013/11/13 15:32:51 napalm Exp $
//
/**
* Parsing states.
* @package System.Security.SafeHtml
* @version $Id: States.php,v 1.1 2013/11/13 15:32:51 napalm Exp $
*/
/**
* Define parser states
*/
/*define('TSAX3_STATE_STOP', 0);
define('TSAX3_STATE_START', 1);
define('TSAX3_STATE_TAG', 2);
define('TSAX3_STATE_OPENING_TAG', 3);
define('TSAX3_STATE_CLOSING_TAG', 4);
define('TSAX3_STATE_ESCAPE', 6);
define('TSAX3_STATE_JASP', 7);
define('TSAX3_STATE_PI', 8);
*/
/**
* StartingState searches for the start of any XML tag
* @package System.Security.SafeHtml
* @access protected
*/
class TSax3_StartingState  {
    /**
    * @param TSax3_StateParser subclass
    * @return constant TSAX3_STATE_TAG
    * @access protected
    */
    function parse(&$context) {
        $data = $context->scanUntilString('<');
        if ($data != '') {
            $context->handler_object_data->
                {$context->handler_method_data}($context->htmlsax, $data);
        }
        $context->IgnoreCharacter();
        return TSax3_StateParser::TSAX3_STATE_TAG;
    }
}
/**
* Decides which state to move one from after StartingState
* @package System.Security.SafeHtml
* @access protected
*/
class TSax3_TagState {
    /**
    * @param TSax3_StateParser subclass
    * @return constant the next state to move into
    * @access protected
    */
    function parse(&$context) {
        switch($context->ScanCharacter()) {
        case '/':
            return TSax3_StateParser::TSAX3_STATE_CLOSING_TAG;
            break;
        case '?':
            return TSax3_StateParser::TSAX3_STATE_PI;
            break;
        case '%':
            return TSax3_StateParser::TSAX3_STATE_JASP;
            break;
        case '!':
            return TSax3_StateParser::TSAX3_STATE_ESCAPE;
            break;
        default:
            $context->unscanCharacter();
            return TSax3_StateParser::TSAX3_STATE_OPENING_TAG;
        }
    }
}
/**
* Dealing with closing XML tags
* @package System.Security.SafeHtml
* @access protected
*/
class TSax3_ClosingTagState {
    /**
    * @param TSax3_StateParser subclass
    * @return constant TSAX3_STATE_START
    * @access protected
    */
    function parse(&$context) {
        $tag = $context->scanUntilCharacters('/>');
        if ($tag != '') {
            $char = $context->scanCharacter();
            if ($char == '/') {
                $char = $context->scanCharacter();
                if ($char != '>') {
                    $context->unscanCharacter();
                }
            }
            $context->handler_object_element->
                {$context->handler_method_closing}($context->htmlsax, $tag, FALSE);
        }
        return TSax3_StateParser::TSAX3_STATE_START;
    }
}
/**
* Dealing with opening XML tags
* @package System.Security.SafeHtml
* @access protected
*/
class TSax3_OpeningTagState {
    /**
    * Handles attributes
    * @param string attribute name
    * @param string attribute value
    * @return void
    * @access protected
    * @see TSax3_AttributeStartState
    */
    function parseAttributes(&$context) {
        $Attributes = array();
    
        $context->ignoreWhitespace();
        $attributename = $context->scanUntilCharacters("=/> \n\r\t");
        while ($attributename != '') {
            $attributevalue = NULL;
            $context->ignoreWhitespace();
            $char = $context->scanCharacter();
            if ($char == '=') {
                $context->ignoreWhitespace();
                $char = $context->ScanCharacter();
                if ($char == '"') {
                    $attributevalue= $context->scanUntilString('"');
                    $context->IgnoreCharacter();
                } else if ($char == "'") {
                    $attributevalue = $context->scanUntilString("'");
                    $context->IgnoreCharacter();
                } else {
                    $context->unscanCharacter();
                    $attributevalue =
                        $context->scanUntilCharacters("> \n\r\t");
                }
            } else if ($char !== NULL) {
                $attributevalue = NULL;
                $context->unscanCharacter();
            }
            $Attributes[$attributename] = $attributevalue;
            
            $context->ignoreWhitespace();
            $attributename = $context->scanUntilCharacters("=/> \n\r\t");
        }
        return $Attributes;
    }

    /**
    * @param TSax3_StateParser subclass
    * @return constant TSAX3_STATE_START
    * @access protected
    */
    function parse(&$context) {
        $tag = $context->scanUntilCharacters("/> \n\r\t");
        if ($tag != '') {
            $this->attrs = array();
            $Attributes = $this->parseAttributes($context);
            $char = $context->scanCharacter();
            if ($char == '/') {
                $char = $context->scanCharacter();
                if ($char != '>') {
                    $context->unscanCharacter();
                }
                $context->handler_object_element->
                    {$context->handler_method_opening}($context->htmlsax, $tag, 
                    $Attributes, TRUE);
                $context->handler_object_element->
                    {$context->handler_method_closing}($context->htmlsax, $tag, 
                    TRUE);
            } else {
                $context->handler_object_element->
                    {$context->handler_method_opening}($context->htmlsax, $tag, 
                    $Attributes, FALSE);
            }
        }
        return TSax3_StateParser::TSAX3_STATE_START;
    }
}

/**
* Deals with XML escapes handling comments and CDATA correctly
* @package System.Security.SafeHtml
* @access protected
*/
class TSax3_EscapeState {
    /**
    * @param TSax3_StateParser subclass
    * @return constant TSAX3_STATE_START
    * @access protected
    */
    function parse(&$context) {
        $char = $context->ScanCharacter();
        if ($char == '-') {
            $char = $context->ScanCharacter();
            if ($char == '-') {
                $context->unscanCharacter();
                $context->unscanCharacter();
                $text = $context->scanUntilString('-->');
                $text .= $context->scanCharacter();
                $text .= $context->scanCharacter();
            } else {
                $context->unscanCharacter();
                $text = $context->scanUntilString('>');
            }
        } else if ( $char == '[') {
            $context->unscanCharacter();
            $text = $context->scanUntilString(']>');
            $text.= $context->scanCharacter();
        } else {
            $context->unscanCharacter();
            $text = $context->scanUntilString('>');
        }

        $context->IgnoreCharacter();
        if ($text != '') {
            $context->handler_object_escape->
            {$context->handler_method_escape}($context->htmlsax, $text);
        }
        return TSax3_StateParser::TSAX3_STATE_START;
    }
}
/**
* Deals with JASP/ASP markup
* @package System.Security.SafeHtml
* @access protected
*/
class TSax3_JaspState {
    /**
    * @param TSax3_StateParser subclass
    * @return constant TSAX3_STATE_START
    * @access protected
    */
    function parse(&$context) {
        $text = $context->scanUntilString('%>');
        if ($text != '') {
            $context->handler_object_jasp->
                {$context->handler_method_jasp}($context->htmlsax, $text);
        }
        $context->IgnoreCharacter();
        $context->IgnoreCharacter();
        return TSax3_StateParser::TSAX3_STATE_START;
    }
}
/**
* Deals with XML processing instructions
* @package System.Security.SafeHtml
* @access protected
*/
class TSax3_PiState {
    /**
    * @param TSax3_StateParser subclass
    * @return constant TSAX3_STATE_START
    * @access protected
    */
    function parse(&$context) {
        $target = $context->scanUntilCharacters(" \n\r\t");
        $data = $context->scanUntilString('?>');
        if ($data != '') {
            $context->handler_object_pi->
            {$context->handler_method_pi}($context->htmlsax, $target, $data);
        }
        $context->IgnoreCharacter();
        $context->IgnoreCharacter();
        return TSax3_StateParser::TSAX3_STATE_START;
    }
}