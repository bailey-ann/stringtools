<?php
/**
 * Auto generated by prado-cli.php on 2013-11-14 12:19:32.
 */
class RelStringPrice extends TActiveRecord
{
	const TABLE='rel_string_price';

	public $id_strings;
	public $id_stringer;
	public $price;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>