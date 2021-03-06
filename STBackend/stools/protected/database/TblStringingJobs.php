<?php
/**
 * Auto generated by prado-cli.php on 2013-11-18 02:00:47.
 */
class TblStringingJobs extends TActiveRecord
{
	const TABLE='tbl_stringing_jobs';

	public $id;
	public $tbl_racquets_user_id;
	public $tbl_users_id_stringer;
	public $tbl_stringing_machines_id;
	public $tbl_strings_id_main;
	public $tbl_strings_id_cross;
	public $tbl_stringing_type_id;
	public $date_stringing;
	public $weight_main;
	public $wieght_cross;
	public $old_job_id;
	public $dynamic_tension;
	public $stencyl;
	public $grommets_guard;
	public $tbl_grip_id;
	public $tbl_overgrip_id;
	public $note;
	public $total_price;
	public $paid;
	public $prestretch_main;
	public $prestretch_cross;
	public $broken;
	public $cut;
	public $duration_string;
	public $note_customer;
	public $sended_email;
	public $sended_sms;
	
	
	public $customer;
	public $user_racquet;
	public $stringer;
	public $stringing_machines;
	public $main_string;
	public $cross_string;
	public $stringing_type;
	public $old_job;
	public $grip;
	public $overgrip;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>