<?php

require_once('class.phpmailer.php');

class NewCustomer extends TPage
{
	
	public function onLoad($param)
    {
    	$this->Page->Title = Prado::localize('NewCustomer');
    	$this->content->Visible = true;
    	$this->reg_ok->Visible = false;
    	$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
    	$this->btnCancelSelect->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
    	if(isset($_GET["status"])){
    		$this->content->Visible = false;
    		$this->reg_ok->Visible = true;
    	}
    }
    
	public function checkUsername($sender,$param)
	{
		// set $param->IsValid to false if the username is already taken
		$userRecord=TblUsers::finder()->findBy_username($this->Username->Text);

		if($userRecord != null)
			$param->IsValid = false;
	}
	
	public function checkEmail($sender,$param)
	{
		// set $param->IsValid to false if the username is already taken
		$userRecord=TblUsers::finder()->findBy_email($this->Email->Text);

		if($userRecord != null)
			$param->IsValid = false;
	}

	public function createUser($sender,$param)
	{
		if($this->IsValid)
		{
			$userRecord = new TblUsers();
			$userRecord->type_user_id = 3;
			$userRecord->username = $this->Username->Text;
			$userRecord->password = md5($this->Password->Text);
			$userRecord->active = 1;
			$userRecord->confirm_code = mt_rand(1000000000,9999999999); 
			$userRecord->name = $this->Name->Text;
			$userRecord->surname = $this->Surname->Text;
			$userRecord->email = $this->Email->Text;
			$userRecord->telephone = $this->Telephone->Text;
			$userRecord->mobile_telephone = $this->MobileTelephone->Text;
			$userRecord->fax = $this->Fax->Text;
			$userRecord->cost = $this->Cost->Text;
			$userRecord->date_insert = date("c");
			$userRecord->piva = $this->Piva->Text;
			$userRecord->Save();
			
			$relSC = new RelStringerCustomer();
			$relSC->id_stringer = $this->User->UserDB->id;
			$relSC->id_customer = $userRecord->id;
			$relSC->Save();
			
			$url=$this->Service->constructUrl('User.Customers');
        	$this->Response->redirect($url);
			
		}
	}
	
	public function SendMail($userRecord){
		$mail = new PHPMailer();
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; 
		$mail->Host = $this->Application->Parameters['SMTP_HOST'];
		$mail->Port = $this->Application->Parameters['SMTP_PORT'];
		$mail->Username = $this->Application->Parameters['SMTP_USERNAME'];
		$mail->Password = $this->Application->Parameters['SMTP_PASSWORD'];
		$mail->SetFrom("admin@stools.it", "StringTools");
		$mail->Subject = "Thank you for registering with StringTools";
		
		$body = "Dear " . $userRecord->name . " " . $userRecord->surname . "<br>";
		$body .= "Thank you for registering at the StringTools. Before we can activate your account one last step must be taken to complete your registration.";
		$body .= "<br><br>";
		$body .= "Please note - you must complete this last step to become a registered member. You will only need to visit this URL once to activate your account.";
		$body .= "<br><br>";
		$body .= "To complete your registration, please visit this URL:";
		$body .= "<br>";
		$body .= $this->Application->Parameters['SITE_LINK']."/index.php?page=User.Confirm&id=".$userRecord->id."&code=".$userRecord->confirm_code;
		$body .= "<br><br>";
		$body .= "**** Does The Above URL Not Work? ****";
		$body .= "If the above URL does not work, please use your Web browser to go to:";
		$body .= "http://stools.it";
		$body .= "<br><br>";
		$body .= "Please be sure not to add extra spaces. You will need to type in your username and activation number on the page that appears when you visit the URL.";
		$body .= "<br><br>";
		$body .= "Your Username is: ".$userRecord->username;
		$body .= "<br>";
		$body .= "Your Activation ID is: ".$userRecord->confirm_code;
		$body .= "<br><br>";	
		$body .= "If you are still having problems signing up please contact a member of our support staff at ".$this->Application->Parameters['EMAIL_ADMIN'];
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
	
	public function cancelSelect(){
		$url=$this->Service->constructUrl('User.Customers');
        	$this->Response->redirect($url);
	}
}

