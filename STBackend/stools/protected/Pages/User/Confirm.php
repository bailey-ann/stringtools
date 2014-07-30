<?php

class Confirm extends TPage
{
	
	public function onLoad($param)
    {
    	$this->Page->Title = Prado::localize('Confirm');
    	$id = 0;
    	$code = "";
    	
    	if(isset($_GET["id"])){
    		$id = $_GET["id"];
    	}
    	
    	if(isset($_GET["code"])){
    		$code = $_GET["code"];
    	}
    	
    	if($id != 0 && $code != ""){
    		$user = TblUsers::finder()->findBy_id($id);
    		
    		if($user != null){
    			if($user->active == 1){
	    			$this->result->Text = Prado::localize('USER_ALREADY_ACTIVE') . " <a href='mailto:"  . $this->Application->Parameters['EMAIL_ASSISTENZE']."'>".Prado::localize('ASSISTENZE')."</a>";
	    		}else if($user->confirm_code != $code){
	    			$this->result->Text = Prado::localize('USER_WRONG_CODE') . " <a href='mailto:"  . $this->Application->Parameters['EMAIL_ASSISTENZE']."'>".Prado::localize('ASSISTENZE')."</a>";
	    		}else{
	    			$user->active = 1;
	    			$user->save();
	    			$this->result->Text = Prado::localize('ACTIVATION_OK');
	    		}
    		}else{
    			$this->result->Text = Prado::localize('USER_NOT_EXIST') . " <a href='mailto:"  . $this->Application->Parameters['EMAIL_ASSISTENZE']."'>".Prado::localize('ASSISTENZE')."</a>";
    		}
    	}else{
    		
    	}
    }
}

