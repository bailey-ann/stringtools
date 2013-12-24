<?php

require_once('class.phpmailer.php');

class ChangePassword extends TPage
{
    
	public function checkOldPassword($sender,$param)
	{
		$user = $this->User->UserDB;
		
		if($user->password != md5($this->Password_old->Text))
			$param->IsValid = false;	
	}

	public function changePassword($sender,$param)
	{
		if($this->IsValid)
		{
			$user = $this->User->UserDB;
			$user->password = md5($this->Password->Text);
			$user->save();
			
			$url=$this->Service->constructUrl('Home');
        	$this->Response->redirect($url);
			
		}
	}
}

