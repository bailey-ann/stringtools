<?php
/**
 * Auto generated by prado-cli.php on 2013-11-14 02:53:07.
 */
class TblStringingMachines extends TActiveRecord
{
	const TABLE='tbl_stringing_machines';

	public $id;
	public $tbl_brands_id;
	public $tbl_stringing_machine_type_id;
	public $model;
	
	public $brand_name;
	public $type;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>