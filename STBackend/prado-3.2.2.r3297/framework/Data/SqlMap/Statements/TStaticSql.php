<?php
/**
 * TStaticSql class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2013 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: TStaticSql.php,v 1.1 2013/11/13 15:32:35 napalm Exp $
 * @package System.Data.SqlMap.Statements
 */

/**
 * TStaticSql class.
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id: TStaticSql.php,v 1.1 2013/11/13 15:32:35 napalm Exp $
 * @package System.Data.SqlMap.Statements
 * @since 3.1
 */
class TStaticSql extends TComponent
{
	private $_preparedStatement;

	public function buildPreparedStatement($statement, $sqlString)
	{
		$factory = new TPreparedStatementFactory($statement, $sqlString);
		$this->_preparedStatement = $factory->prepare();
	}

	public function getPreparedStatement($parameter=null)
	{
		return $this->_preparedStatement;
	}
}

