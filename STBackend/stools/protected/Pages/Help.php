<?php

/*echo "<script type=\"text/javascript\">
window.onresize = function() 
{
    window.resizeTo(500,500);
}
window.onclick = function() 
{
    window.resizeTo(500,500);
}
</script>";*/ //OK



class Help extends TPage
{
	
	public function onLoad($param)
    {
    	$this->Close->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/close.gif';
		$type = $this->Request['type'];
		if($type == 'strings'){
			$this->Type->Text = Prado::localize('ExplanationString');
		}else if($type == 'cr'){
			$this->Type->Text = Prado::localize('ExplanationUserRacquet');
		}else if($type == 'HelpCustomers'){
			$this->Type->Text = Prado::localize('HelpCustomers');
		}else if($type == 'HelpStringMains'){
			$this->Type->Text = Prado::localize('HelpStringMains');
		}
    }
    
    public function onClose(){
    	echo "<script>window.close();</script>";
    }
    
	
}

