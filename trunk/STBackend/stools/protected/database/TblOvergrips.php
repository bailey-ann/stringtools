<?php
/**
 * Auto generated by prado-cli.php on 2013-11-14 11:30:14.
 */
class TblOvergrips extends TActiveRecord
{
	const TABLE='tbl_overgrips';

	public $id;
	public $tbl_brands_id;
	public $model;
	public $price;
	public $note;
	
	public $brand_name;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>