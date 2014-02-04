<?php

require_once('class.phpmailer.php');

class LostPassword extends TPage
{
	public function onLoad($param)
    {
    	$this->Page->Title = Prado::localize('LostPassword');
    	$this->Send->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/send.gif';
    	if($this->User->UserDB->type_user_id == 4){
        	$this->Send->Visible = false;
        }else{
        	$this->Send->Visible = true;
        }
    }
    
    public function checkEmail($sender,$param)
	{
		// set $param->IsValid to false if the username is already taken
		$userRecord=TblUsers::finder()->findBy_email($this->Email->Text);

		if($userRecord == null)
			$param->IsValid = false;
	}
    

	public function sendMail($sender,$param)
	{
		if($this->IsValid) {
			$userRecord=TblUsers::finder()->findBy_email($this->Email->Text);
			$number = mt_rand(1000000000,9999999999);
			$userRecord->password = md5($number);
			$userRecord->save();
			
			$this->SendNewPWD($userRecord, $number);
			
			$url=$this->Service->constructUrl('Home');
        	$this->Response->redirect($url);
			
		}
	}
	
	public function SendNewPWD($userRecord, $number){
		$mail = new PHPMailer();
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; 
		$mail->Host = $this->Application->Parameters['SMTP_HOST'];
		$mail->Port = $this->Application->Parameters['SMTP_PORT'];
		$mail->Username = $this->Application->Parameters['SMTP_USERNAME'];
		$mail->Password = $this->Application->Parameters['SMTP_PASSWORD'];
		$mail->SetFrom($this->Application->Parameters['EMAIL_ASSISTENZE'], "StringTools");
		$mail->Subject = "Send new password for StringTools";
		
		$body = "Dear " . $userRecord->name . " " . $userRecord->surname . "<br>";
		$body .= "Your new password is: ".$number;
		$body .= "<br><br>";	
		$body .= "If you are still having problems signing up please contact a member of our support staff at ".$this->Application->Parameters['EMAIL_ASSISTENZE'];
		$body .= "<br><br>";
		$body .= "All the best,";
		$body .= "http://stools.it";
		$mail->IsHTML(true);
		$mail->Body =$body;
		$mail->AddAddress($userRecord->email);
		if(!$mail->Send()) {
			$error = 'Mail error: '.$mail->ErrorInfo;
			return false;
		} else {
			$error = 'Message sent!';
			return true;
		}
	}
}

