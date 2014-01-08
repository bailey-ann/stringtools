<?php
/**
 * THeader6 class file
 *
 * @author Brad Anderson <javalizard@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2013 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: THeader6.php,v 1.1 2013/11/13 15:32:07 napalm Exp $
 * @package System.Web.UI.WebControls
 */

/**
 * THeader6 class
 *
 * This is a simple class to enable your application to have headers but then have your
 * theme be able to redefine the TagName
 * This is also useful for the {@link TWebControlDecorator} (used by themes).
 *
 * @author Brad Anderson <javalizard@gmail.com>
 * @version $Id: THeader6.php,v 1.1 2013/11/13 15:32:07 napalm Exp $
 * @package System.Web.UI.WebControls
 * @since 3.2
 */
 
class THeader6 extends THtmlElement {	
	
	/**
	 * @return string tag name
	 */
	public function getDefaultTagName()
	{
		return 'h6';
	}
	
}
