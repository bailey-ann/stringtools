<?php
/**
 * TSelectMappedStatement class.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2013 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: TSelectMappedStatement.php,v 1.1 2013/11/13 15:32:35 napalm Exp $
 * @package System.Data.SqlMap.Statements
 */

/**
 * TSelectMappedStatment class.
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id: TSelectMappedStatement.php,v 1.1 2013/11/13 15:32:35 napalm Exp $
 * @package System.Data.SqlMap.Statements
 * @since 3.1
 */
class TSelectMappedStatement extends TMappedStatement
{
	public function executeInsert($connection, $parameter)
	{
		throw new TSqlMapExecutionException(
				'sqlmap_cannot_execute_insert', get_class($this), $this->getID());
	}

	public function executeUpdate($connection, $parameter)
	{
		throw new TSqlMapExecutionException(
				'sqlmap_cannot_execute_update', get_class($this), $this->getID());
	}

}

