<?php

class ViewMaterialUsrsa extends TActiveRecord
{
	const TABLE='view_material_usrsa';

	public $material;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>