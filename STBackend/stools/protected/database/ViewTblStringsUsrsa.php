<?php

class ViewTblStringsUsrsa extends TActiveRecord
{
	const TABLE='view_tbl_strings_usrsa';

	public $id;
	public $name;
	public $material;
	public $gauge;
	public $stiffness_lbin;
	public $stiffness_nm;
	public $tension_loss_lbs;
	public $tension_loss_kg;
	public $anno;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>