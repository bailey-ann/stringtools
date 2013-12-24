<?php

class LoginPortlet extends TTemplateControl
{
	public function validateUser($sender,$param)
	{
		$authManager=$this->Application->getModule('auth');
		
		if(!$authManager->login($this->Username->Text,$this->Password->Text))
			$param->IsValid=false;
	}

	public function loginButtonClicked($sender,$param)
	{
		if($this->Page->IsValid)  // all validations succeed
        {
            // obtain the URL of the privileged page that the user wanted to visit originally
            $url=$this->Application->getModule('auth')->ReturnUrl;
            
            if(empty($url))  // the user accesses the login page directly
                $url=$this->Service->DefaultPageUrl;
            $this->Response->redirect($url);
        }
	}
	
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	$this->LoginButton->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/login.gif';
    }
}

