<?php

require_once('class.phpmailer.php');

class PersonalData extends TPage
{
	
	public function onLoad($param)
    {
    	$this->Page->Title = Prado::localize('PersonalData');
    	$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
    	$this->btnCancelSelect->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
    	$this->btnUpload->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/upload.gif';
    	if(!$this->IsPostBack) {
	    	$this->content->Visible = true;
	    	$user = $this->User->UserDB;
	    	$this->Email->Text = $user->email;
	    	$this->Name->Text = $user->name;
	    	$this->Surname->Text = $user->surname;
	    	$this->Telephone->Text = $user->telephone;
	    	$this->MobileTelephone->Text = $user->mobile_telephone;
	    	$this->Fax->Text = $user->fax;
	    	$this->Cost->Text = $user->cost;
	    	$criteria = new TActiveRecordCriteria;
			$criteria->OrdersBy['description'] = 'asc';
	    	$brands = TblWeightUnit::finder()->findAll($criteria);
        	$this->DDLWeightUnit->DataSource=$brands;
        	$this->DDLWeightUnit->dataBind();
	    	$this->DDLWeightUnit->SelectedValue = $user->tbl_weight_unit_id;
	    	$brands = TblCurrencyUnit::finder()->findAll($criteria);
        	$this->DDLCurrencyUnit->DataSource=$brands;
        	$this->DDLCurrencyUnit->dataBind();
			$this->DDLCurrencyUnit->SelectedValue = $user->tbl_currency_unit_id;
			$this->Piva->Text = $user->piva;

			$urlJpg = 'themes/White/images/logo/'.$this->User->UserDB->id.".jpg";
			$urlPng = 'themes/White/images/logo/'.$this->User->UserDB->id.".png";

			if (!file_exists($urlJpg))
				//if (!file_exists($urlPng))
					$this->ImageLogo->ImageUrl = $this->Page->Theme->BaseUrl.'/images/logo-st-www.jpg';
				//else 
					//$this->ImageLogo->ImageUrl = $urlPng;
			else 
				$this->ImageLogo->ImageUrl = $urlJpg;
        }
    }
    

	public function saveUser($sender,$param)
	{
		if(true)
		{
			$user = $this->User->UserDB;
			$user->email = $this->Email->Text;
	    	$user->name = $this->Name->Text;
	    	$user->surname = $this->Surname->Text;
	    	$user->telephone = $this->Telephone->Text;
	    	$user->mobile_telephone = $this->MobileTelephone->Text; 
	    	$user->fax = $this->Fax->Text;
	    	$user->cost = $this->Cost->Text;
			$user->date_insert = date("c");
			$user->tbl_weight_unit_id = $this->DDLWeightUnit->SelectedValue;
			$user->tbl_currency_unit_id = $this->DDLCurrencyUnit->SelectedValue;
			$user->piva = $this->Piva->Text;
			$user->Save();

			$this->Application->getModule('auth')->logout();
			$this->Response->redirect($this->Service->constructUrl('Home'));
		}
		
		
	}
	
	public function btnUpload_Click() 
	{
		if($this->fuTest->getHasFile())
		{
            $i_dir = 'themes/White/images/logo/';
			$ft = $this->fuTest->getFileType();				
			$fileName = $this->fuTest->FileName;			
			$pos = strrpos($fileName, '.');
			if($pos === false)
			    $ext = ""; 
			$ext = substr($fileName, $pos);
			
			$fileName = $this->User->UserDB->id .$ext;
			$fileNameJpg = $this->User->UserDB->id . ".jpg";
			$n_filename = strtolower(str_replace(" ", "_", $fileName));
			$n_fileNameJpg= strtolower(str_replace(" ", "_", $fileNameJpg));
			$this->fuTest->saveAs($i_dir . $n_filename);
			
			if($ft == 'image/png'){
				$this->png2jpg($i_dir . $n_filename, $i_dir . $n_fileNameJpg, 100);
			}
			
			$this->Response->redirect($this->Service->constructUrl('User.PersonalData'));
		}
	}
	
	function png2jpg($originalFile, $outputFile, $quality) {
		$image = imagecreatefrompng($originalFile);
		imagejpeg($image, $outputFile, $quality);
		imagedestroy($image);
	}
	
	public function cancelSelect(){
		$url=$this->Service->constructUrl('User.Customers');
        	$this->Response->redirect($url);
	}
}

