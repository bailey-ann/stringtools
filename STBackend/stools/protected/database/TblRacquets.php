<?php
/**
 * Auto generated by prado-cli.php on 2013-11-15 10:54:10.
 */
class TblRacquets extends TActiveRecord
{
	const TABLE='tbl_racquets';

	public $id;
	public $tbl_brands_id;
	public $tbl_racquets_pattern_id;
	public $model;
	public $head_size;
	public $length;
	public $weight_unstrung;
	public $weight_strung;
	public $balance;
	public $swingweight;
	public $stiffness;
	public $beam_width;
	public $note;
	public $date_modify;
	
	public $brand_name;
	
	public $pattern;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>