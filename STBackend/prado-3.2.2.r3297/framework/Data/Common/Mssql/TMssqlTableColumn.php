<?php
/**
 * TMssqlTableColumn class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2013 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: TMssqlTableColumn.php,v 1.1 2013/11/13 15:32:30 napalm Exp $
 * @package System.Data.Common.Mssql
 */

/**
 * Load common TDbTableCommon class.
 */
Prado::using('System.Data.Common.TDbTableColumn');

/**
 * Describes the column metadata of the schema for a Mssql database table.
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id: TMssqlTableColumn.php,v 1.1 2013/11/13 15:32:30 napalm Exp $
 * @package System.Data.Common.Mssql
 * @since 3.1
 */
class TMssqlTableColumn extends TDbTableColumn
{
	private static $types = array();

	/**
	 * Overrides parent implementation, returns PHP type from the db type.
	 * @return boolean derived PHP primitive type from the column db type.
	 */
	public function getPHPType()
	{

		return 'string';
	}

	/**
	 * @return boolean true if the column has identity (auto-increment)
	 */
	public function getAutoIncrement()
	{
		return $this->getInfo('AutoIncrement',false);
	}

	/**
	 * @return boolean true if auto increments.
	 */
	public function hasSequence()
	{
		return $this->getAutoIncrement();
	}

	/**
	 * @return boolean true if db type is 'timestamp'.
	 */
	public function getIsExcluded()
	{
		return strtolower($this->getDbType())==='timestamp';
	}
}

