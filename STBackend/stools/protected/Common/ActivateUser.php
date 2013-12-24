<?php

require_once('class.phpmailer.php');

class ActivateUser extends TTemplateControl
{
	
	public function onLoad($param)
    {
    	$this->SendEmailButton->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/resend-code.gif';
    }


	public function sendEmailClicked($sender,$param)
	{
		
		$user = $this->User->UserDB;
		
		$mail = new PHPMailer();
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; 
		$mail->Host = $this->Application->Parameters['SMTP_HOST'];
		$mail->Port = $this->Application->Parameters['SMTP_PORT'];
		$mail->Username = $this->Application->Parameters['SMTP_USERNAME'];
		$mail->Password = $this->Application->Parameters['SMTP_PASSWORD'];
		$mail->SetFrom($this->Application->Parameters['EMAIL_ASSISTENZE'], "StringTools");
		$mail->Subject = "Thank you for registering with StringTools";
		
		$body = "Dear " . $user->name . " " . $user->surname . "<br>";
		$body .= "Thank you for registering at the StringTools. Before we can activate your account one last step must be taken to complete your registration.";
		$body .= "<br><br>";
		$body .= "Please note - you must complete this last step to become a registered member. You will only need to visit this URL once to activate your account.";
		$body .= "<br><br>";
		$body .= "To complete your registration, please visit this URL:";
		$body .= "<br>";
		$body .= $this->Application->Parameters['SITE_LINK']."/index.php?page=User.Confirm&id=".$user->id."&code=".$user->confirm_code;
		$body .= "<br><br>";
		$body .= "**** Does The Above URL Not Work? ****";
		$body .= "If the above URL does not work, please use your Web browser to go to:";
		$body .= "http://stools.it";
		$body .= "<br><br>";
		$body .= "Please be sure not to add extra spaces. You will need to type in your username and activation number on the page that appears when you visit the URL.";
		$body .= "<br><br>";
		$body .= "Your Username is: ".$user->username;
		$body .= "<br>";
		$body .= "Your Activation ID is: ".$user->confirm_code;
		$body .= "<br><br>";	
		$body .= "If you are still having problems signing up please contact a member of our support staff at ".$this->Application->Parameters['EMAIL_ADMIN'];
		$body .= "<br><br>";
		$body .= "All the best,";
		$body .= "http://stools.it";
		$mail->IsHTML(true);
		$mail->Body =$body;
		$mail->AddAddress($user->email);
		if(!$mail->Send()) {
			$error = 'Mail error: '.$mail->ErrorInfo;
			return false;
		} else {
			$error = 'Message sent!';
			return true;
		}
	}
}

