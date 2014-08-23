<?php

require_once('class.phpmailer.php');
require_once 'tcpdf.php' ;

class GestioneJob extends FunctionList
{
	private $customer = null;
	private $_data_user_racquets;
	private $userRacquetSelect;
	private $_edit_job = null;
	
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	$this->Page->Title = Prado::localize('ManageJob');
    	$this->Cancel_2->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
    	$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
    	$this->CalculatePrice->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/calculate-price.gif';
    	$this->Delete->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/delete.gif';
    	$this->Pdf->ImageUrl = $this->Page->Theme->BaseUrl.'/images/pdf-64.png';
    	$this->HelpCustomers->ImageUrl = $this->Page->Theme->BaseUrl.'/images/help-24.png';
    	$this->HelpStringMains->ImageUrl = $this->Page->Theme->BaseUrl.'/images/help-24.png';
    	$this->SendMail->ImageUrl = $this->Page->Theme->BaseUrl.'/images/mail_send.png';
    	$this->Pdf->Visible = false;	
    	$this->SendMail->Visible = false;
    	$this->editable->Visible = true;
		$idJob = null;
    	$idJob = (int)$this->Request['idJob'];
    	$idCloneJob = null;
    	$idCloneJob = (int)$this->Request['idCloneJob'];
    	
    	
    	
    	if($idJob != null){
    		
    		//if(!$this->IsPostBack)
	        //{	
	        	$this->_edit_job = TblStringingJobs::finder()->findBy_id($idJob);
	        	$racquetCustomer = TblRacquetsUser::finder()->findBy_id($this->_edit_job->tbl_racquets_user_id);
	        	$this->customer = TblUsers::finder()->findBy_id($racquetCustomer->tbl_users_id);
    			$this->setViewState('customer',$this->customer);
    			$this->editable->Visible = false;	    		
	    		$this->createEditZone($this->_edit_job, false);
	    		$this->Delete->visible = true;
	    		$this->Pdf->Visible = true;	
	    		if($this->customer->email != null && $this->customer->email != "")
	    			$this->SendMail->Visible = true;
	        /*}else{
	        	$this->Pdf->Visible = true;
	        	$this->_edit_job = TblStringingJobs::finder()->findBy_id($idJob);
	        	$racquetCustomer = TblRacquetsUser::finder()->findBy_id($this->_edit_job->tbl_racquets_user_id);
	        	$this->customer = TblUsers::finder()->findBy_id($racquetCustomer->tbl_users_id);
	        	if($this->customer->email != null && $this->customer->email != "")
	        		$this->SendMail->Visible = true;
	        }*/
    	}else if($idCloneJob != null){
    		
    		if(!$this->IsPostBack)
	        {	  
	        	$this->_edit_job = TblStringingJobs::finder()->findBy_id($idCloneJob);
	        	$racquetCustomer = TblRacquetsUser::finder()->findBy_id($this->_edit_job->tbl_racquets_user_id);
	        	$this->customer = TblUsers::finder()->findBy_id($racquetCustomer->tbl_users_id);
    			$this->setViewState('customer',$this->customer);
    			$this->editable->Visible = false;
	    		$this->createEditZone($this->_edit_job, true);
	    		$this->Delete->visible = true;
	    		$this->Pdf->Visible = false;	
	        }
    	}else{
    		
    		if(!$this->IsPostBack)
	        {
	        	$this->createNewZone(); 
	        	$this->Pdf->Visible = false;	
	        }
    	}
    	
   	 	if($this->User->UserDB->type_user_id == 4){
        	$this->Save->Visible = false;
        	$this->Delete->Visible = false;
        }else{
        	$this->Save->Visible = true;
        	$this->Delete->Visible = false;
        }
        
        $this->SendMail->Visible = false;
    }
    
    
    
    
     /*** zona dati incordatura ***/
     
     public function createNewZone(){
     	
     	$this->editable->Visible = true;
     	$this->EDIT_RACQUET->Visible = false;
    	$this->DATA_JOB_TITLE->Text = Prado::localize('DataNewJob');
    	
    	//Clienti
    	$arrayClienti = array();
    	$sqlmap = $this->Application->Modules['sqlmap']->Database;
    	$sqlmap->Active = true;   	
    	$sql = "SELECT distinct tbl_users.id, tbl_users.name, tbl_users.surname FROM tbl_users
			INNER JOIN rel_stringer_customer ON rel_stringer_customer.id_customer = tbl_users.id
			INNER JOIN tbl_racquets_user ON tbl_racquets_user.tbl_users_id = tbl_users.id
			where rel_stringer_customer.id_stringer  = ".$this->User->UserDB->id . " 
			order by tbl_users.name desc, tbl_users.surname desc";    	
    	$command = $sqlmap->createCommand($sql);
    	$testArray = $command->query()->readAll();
    	$firstIdCustomer = 0;
	    foreach($testArray as $row){
	    	if($firstIdCustomer == 0)
	    		$firstIdCustomer = $row["id"];
	       	$arrayClienti[$row["id"]] = $row["name"] . " " . $row["surname"];
	    }
    	$this->DDLCustomers->DataSource=$arrayClienti;
        $this->DDLCustomers->dataBind();
        $this->DDLCustomers->Enabled = true;
        if(count($arrayClienti) == 0){
        	$this->Response->redirect($this->Service->constructUrl('User.Customers'));
        }
    	//racchette clienti
    	$this->ChangeCustomersRacquets($firstIdCustomer);
    	$this->DDLCustomerRacquets->Enabled = true;
    	
    	$this->DateStringing->setTimeStamp(strtotime("now"));
    	
     	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['model'] = 'asc';
		$striningMachineArray = array();
        $striningMachine = TblStringingMachines::finder()->findAll($criteria);
        foreach($striningMachine as $row){
        	$brand = TblBrands::finder()->findBy_id($row->tbl_brands_id);
        	$row->brand_name = $brand->description;
        	$striningMachineArray[$row->id] = $row->brand_name . " " . $row->model;
        }
        $this->DDLStriningMachine->DataSource=$striningMachineArray;
        $this->DDLStriningMachine->dataBind();
        
        //macchina di default
        $idDefault = 1;
        
        $arrayPersonalMachine = RelStringerStringingMachine::finder()->findAll('id_stringer = ?', $this->User->UserDB->id);
        if($arrayPersonalMachine != null){
        	foreach($arrayPersonalMachine as $row){
        		if($row->default == 1)
        			$idDefault = $row->id_stringing_machine;
        	}
        }
        $this->DDLStriningMachine->SelectedValue = $idDefault;
        
        $i = 0;
        $brand_name = array();
        $model = array();
        $stringArray = array();
        $string = TblStrings::finder()->findAll();
        foreach($string as $row){
        	$brand = TblBrands::finder()->findBy_id($row->tbl_brands_id);
        	$row->brand_name = $brand->description;
        	
        	$brand_name[$i] = $row->brand_name;
		    $model[$i] = $row->model;
		    $i++;
        	
        	$gauge = TblGauges::finder()->findBy_id($row->tbl_gauges_id);
		    $row->gauge_desc = $gauge->usa . " (" . $gauge->diameter.")";
		    
		    $stringArray[$row->id] = $row->brand_name . " " . $row->model . " " . $row->gauge_desc;
        }

        asort($stringArray);
        
        $stringArray = array();
        $sql = "SELECT tbl_strings.id, tbl_brands.description, tbl_strings.model,
			CONCAT(tbl_gauges.usa,\"(\",tbl_gauges.diameter,\")\") as diametro FROM
			rel_string_price
			INNER JOIN tbl_strings ON rel_string_price.id_strings = tbl_strings.id
			INNER JOIN tbl_brands ON tbl_strings.tbl_brands_id = tbl_brands.id
			INNER JOIN tbl_gauges ON tbl_strings.tbl_gauges_id = tbl_gauges.id
			where  rel_string_price.id_stringer = ".$this->User->UserDB->id . " 
			order by tbl_brands.description, tbl_strings.model";
        $command = $sqlmap->createCommand($sql);
    	$testArray = $command->query()->readAll();
     	foreach($testArray as $row){
	       	$stringArray[$row["id"]] = $row["description"] . " " . $row["model"] . " " . $row["diametro"];
	    }
        $this->DDLStringMains->DataSource=$stringArray;
        $this->DDLStringMains->dataBind();
        
        $this->WeightMains->Text = "0";     
        $this->PrestretchMain->Text = "0";        
        $this->DDLStringCross->enabled = false;
        $this->DDLStringCross->DataSource=$stringArray;
        $this->DDLStringCross->dataBind();
        $this->WeightCross->enabled = false;
        $this->WeightCross->Text = "0";
        $this->PrestretchCross->enabled = false;
        $this->PrestretchCross->Text = "0";  
        $this->ActivateStringCross->Checked = false;
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
		$stringingJobTypeArray = array();
        $stringingJobType = TblStringingJobType::finder()->findAll($criteria);
        foreach($stringingJobType as $row){
        	$stringingJobTypeArray[$row->id] = $row->description;
        }
        $this->DDLStringingType->DataSource=$stringingJobTypeArray;
        $this->DDLStringingType->dataBind();
        
        $this->DynamicTension->Text = "0";
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['model'] = 'asc';
		$gripsArray = array();
		$gripsArray[0] = Prado::localize('NoGrip');
        $grips = TblGrips::finder()->findAll($criteria);
        foreach($grips as $row){
        	$brand = TblBrands::finder()->findBy_id($row->tbl_brands_id);
        	$row->brand_name = $brand->description;
        	$gripsArray[$row->id] = $row->brand_name . " " . $row->model;
        }
        $this->DDLGrips->SelectedValue = 0;
        $this->DDLGrips->DataSource=$gripsArray;
        $this->DDLGrips->dataBind();
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['model'] = 'asc';
		$overGripsArray = array();
		$overGripsArray[0] = Prado::localize('NoOvergrip');
        $overGrips = TblOvergrips::finder()->findAll($criteria);
        foreach($overGrips as $row){
        	$brand = TblBrands::finder()->findBy_id($row->tbl_brands_id);
        	$row->brand_name = $brand->description;
        	$overGripsArray[$row->id] = $row->brand_name . " " . $row->model;
        }
        $this->DDLOvergrips->SelectedValue = 0;
        $this->DDLOvergrips->DataSource=$overGripsArray;
        $this->DDLOvergrips->dataBind();
        
        $this->TotalPrice->Text = "0";
        
        $this->Broken->Checked = false;  
        $this->Cut->Checked = false;  
        $this->DurationString->Text = "0";       
        $this->NoteCustomer->Text = "";
        
     }
     
     
     public function createEditZone($job, $clone){
     	$this->editable->Visible = true;
     	
     	if($clone){
     		$this->DATA_JOB_TITLE->Text = Prado::localize('DataNewJob');
     		$this->DateStringing->setTimeStamp(strtotime("now"));
     	}else{
     		$this->DATA_JOB_TITLE->Text = Prado::localize('EditNewJob') . " " . $this->formatJob($job->id);
     		$this->DateStringing->setTimeStamp(strtotime($job->date_stringing));
     	}
     	
     	$this->EDIT_RACQUET->Visible = true;
     	
     	
     	//Clienti
    	$arrayClienti = array();
    	$sqlmap = $this->Application->Modules['sqlmap']->Database;
    	$sqlmap->Active = true;   	
    	$sql = "SELECT distinct tbl_users.id, tbl_users.name, tbl_users.surname FROM tbl_users
			INNER JOIN rel_stringer_customer ON rel_stringer_customer.id_customer = tbl_users.id
			INNER JOIN tbl_racquets_user ON tbl_racquets_user.tbl_users_id = tbl_users.id
			where rel_stringer_customer.id_stringer  = ".$this->User->UserDB->id . " 
			order by tbl_users.name desc, tbl_users.surname desc";    	
    	$command = $sqlmap->createCommand($sql);
    	$testArray = $command->query()->readAll();
	    foreach($testArray as $row){
	       	$arrayClienti[$row["id"]] = $row["name"] . " " . $row["surname"];
	    }
    	$this->DDLCustomers->DataSource=$arrayClienti;
        $this->DDLCustomers->dataBind();
        $this->DDLCustomers->SelectedValue = $this->customer->id;
        $this->DDLCustomers->Enabled = false;
    	//racchette clienti
    	$this->ChangeCustomersRacquets($this->customer->id);
    	$this->DDLCustomerRacquets->SelectedValue = $job->tbl_racquets_user_id;
     	$this->DDLCustomerRacquets->Enabled = false;
     	
     	$racquetUser = TblRacquetsUser::finder()->findBy_id($job->tbl_racquets_user_id);
     	$racquet = TblRacquets::finder()->findBy_id($racquetUser->tbl_racquets_id);
     	$brandRacquet = TblBrands::finder()->findBy_id($racquet->tbl_brands_id);

     	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['model'] = 'asc';
		$striningMachineArray = array();
        $striningMachine = TblStringingMachines::finder()->findAll($criteria);
        foreach($striningMachine as $row){
        	$brand = TblBrands::finder()->findBy_id($row->tbl_brands_id);
        	$row->brand_name = $brand->description;
        	$striningMachineArray[$row->id] = $row->brand_name . " " . $row->model;
        }
        $this->DDLStriningMachine->DataSource=$striningMachineArray;
        $this->DDLStriningMachine->dataBind();
        $this->DDLStriningMachine->SelectedValue = $job->tbl_stringing_machines_id;
        
        $i = 0;
        $brand_name = array();
        $model = array();
        $stringArray = array();
        $string = TblStrings::finder()->findAll();
        foreach($string as $row){
        	$brand = TblBrands::finder()->findBy_id($row->tbl_brands_id);
        	$row->brand_name = $brand->description;
        	
        	$brand_name[$i] = $row->brand_name;
		    $model[$i] = $row->model;
		    $i++;
        	
        	$gauge = TblGauges::finder()->findBy_id($row->tbl_gauges_id);
		    $row->gauge_desc = $gauge->usa . " (" . $gauge->diameter.")";
		    
		    $stringArray[$row->id] = $row->brand_name . " " . $row->model . " " . $row->gauge_desc;
        }

        asort($stringArray);
        
        $stringArray = array();
        $sql = "SELECT tbl_strings.id, tbl_brands.description, tbl_strings.model,
			CONCAT(tbl_gauges.usa,\"(\",tbl_gauges.diameter,\")\") as diametro FROM
			rel_string_price
			INNER JOIN tbl_strings ON rel_string_price.id_strings = tbl_strings.id
			INNER JOIN tbl_brands ON tbl_strings.tbl_brands_id = tbl_brands.id
			INNER JOIN tbl_gauges ON tbl_strings.tbl_gauges_id = tbl_gauges.id
			where  rel_string_price.id_stringer = ".$this->User->UserDB->id . " 
			order by tbl_brands.description, tbl_strings.model";
        $command = $sqlmap->createCommand($sql);
    	$testArray = $command->query()->readAll();
     	foreach($testArray as $row){
	       	$stringArray[$row["id"]] = $row["description"] . " " . $row["model"] . " " . $row["diametro"];
	    }
        
		
        
        $this->DDLStringMains->DataSource=$stringArray;
        $this->DDLStringMains->dataBind();
        $this->DDLStringMains->SelectedValue = $job->tbl_strings_id_main;
        $this->WeightMains->Text = $job->weight_main;
        $this->PrestretchMain->Text = $job->prestretch_main;    
        
        $this->DDLStringCross->DataSource=$stringArray;
        $this->DDLStringCross->dataBind();
        $this->DDLStringCross->SelectedValue = $job->tbl_strings_id_cross;
        $this->WeightCross->Text = $job->wieght_cross;
        $this->PrestretchCross->Text = $job->prestretch_cross;    
        if($job->tbl_strings_id_main == $job->tbl_strings_id_cross &&
        	$job->weight_main == $job->wieght_cross){
        	$this->DDLStringCross->enabled = false;
        	$this->WeightCross->enabled = false;
        	$this->ActivateStringCross->Checked = false;
        }else{
        	$this->DDLStringCross->enabled = true;	
        	$this->WeightCross->enabled = true;
        	$this->ActivateStringCross->Checked = true;
        }            
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
		$stringingJobTypeArray = array();
        $stringingJobType = TblStringingJobType::finder()->findAll($criteria);
        foreach($stringingJobType as $row){
        	$stringingJobTypeArray[$row->id] = $row->description;
        }
        $this->DDLStringingType->DataSource=$stringingJobTypeArray;
        $this->DDLStringingType->dataBind();
        $this->DDLStringingType->SelectedValue = $job->tbl_stringing_type_id;
        
        $this->DynamicTension->Text = $job->dynamic_tension;
        
        $this->DDLStencyl->SelectedValue = $job->stencyl;
        $this->DDLGrommetsGuard->SelectedValue = $job->grommets_guard;
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['model'] = 'asc';
		$gripsArray = array();
		$gripsArray[0] = Prado::localize('NoGrip');
        $grips = TblGrips::finder()->findAll($criteria);
        foreach($grips as $row){
        	$brand = TblBrands::finder()->findBy_id($row->tbl_brands_id);
        	$row->brand_name = $brand->description;
        	$gripsArray[$row->id] = $row->brand_name . " " . $row->model;
        }
        $this->DDLGrips->SelectedValue = 0;
        $this->DDLGrips->DataSource=$gripsArray;
        $this->DDLGrips->dataBind();
        $this->DDLGrips->SelectedValue = $job->tbl_grip_id;
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['model'] = 'asc';
		$overGripsArray = array();
		$overGripsArray[0] = Prado::localize('NoOvergrip');
        $overGrips = TblOvergrips::finder()->findAll($criteria);
        foreach($overGrips as $row){
        	$brand = TblBrands::finder()->findBy_id($row->tbl_brands_id);
        	$row->brand_name = $brand->description;
        	$overGripsArray[$row->id] = $row->brand_name . " " . $row->model;
        }
        $this->DDLOvergrips->SelectedValue = 0;
        $this->DDLOvergrips->DataSource=$overGripsArray;
        $this->DDLOvergrips->dataBind();
        $this->DDLOvergrips->SelectedValue = $job->tbl_overgrip_id;
        
        $this->Note->Text = $job->note;
        
        $this->TotalPrice->Text = $job->total_price;
        
        if($clone){
        	$this->Broken->Checked = false;  
	        $this->Cut->Checked = false;  
	        $this->DurationString->Text = "0";       
	        $this->NoteCustomer->Text = "";
        }else{
        	$this->Broken->Checked = $job->broken;  
        	$this->Cut->Checked = $job->cut;    
        	$this->DurationString->Text = $job->duration_string;      
        	$this->NoteCustomer->Text = $job->note_customer;
        }      
     }
     
	 public function DDLCustomersChanged($sender,$param) {
	 	$this->ChangeCustomersRacquets($this->DDLCustomers->SelectedValue);
	 }
	 
	 private function ChangeCustomersRacquets($idCustomer){
	 	$arrayCustomersRacquets= array();
    	$sqlmap = $this->Application->Modules['sqlmap']->Database;
    	$sqlmap->Active = true;   	
    	$sql = "SELECT tbl_racquets_user.id, tbl_brands.description, tbl_racquets.model, tbl_racquets_user.serial
			FROM tbl_racquets_user
			INNER JOIN tbl_racquets ON tbl_racquets_user.tbl_racquets_id = tbl_racquets.id
			INNER JOIN tbl_brands ON tbl_racquets.tbl_brands_id = tbl_brands.id
			where tbl_racquets_user.tbl_users_id = " . $idCustomer . " AND tbl_racquets_user.active = 1 
			order by tbl_brands.description desc, tbl_racquets.model desc, tbl_racquets_user.serial desc";  	
    	$command = $sqlmap->createCommand($sql);
    	$testArray = $command->query()->readAll();
	    foreach($testArray as $row){
	       	$arrayCustomersRacquets[$row["id"]] = $row["description"] . " " . $row["model"] . " " . $row["serial"];
	    }
    	$this->DDLCustomerRacquets->DataSource=$arrayCustomersRacquets;
        $this->DDLCustomerRacquets->dataBind();
	 }
	     
     public function ActivateStringCrossClicked(){
     	$this->DDLStringCross->enabled = $this->ActivateStringCross->Checked;
     	$this->DDLStringCross->SelectedValue = $this->DDLStringMains->SelectedValue;
     	$this->WeightCross->enabled = $this->ActivateStringCross->Checked;
     	$this->WeightCross->Text = $this->WeightMains->Text;
     	$this->PrestretchCross->enabled = $this->ActivateStringCross->Checked;
     	$this->PrestretchCross->Text = $this->PrestretchMain->Text;
     }
     
     public function checkPrice(){
     	$totalPrice = 0;
     	
     	$cost = $this->User->UserDB->cost;
     	
     	$racquetCustomer = TblRacquetsUser::finder()->findBy_id($this->DDLCustomerRacquets->SelectedValue);
	    $this->customer = TblUsers::finder()->findBy_id($racquetCustomer->tbl_users_id);;
     	$costCustomer = $this->customer->cost;
     	
     	if($costCustomer == 0)
     		$totalPrice += $cost;
     	else
     		$totalPrice += $costCustomer;
     	
     	$mainString = TblStrings::finder()->findBy_id($this->DDLStringMains->SelectedValue);
     	$mainStringCost = RelStringPrice::finder()->find('id_strings = ? AND id_stringer = ?',$mainString->id, $this->User->UserDB->id);    	
     	$costMain = $mainStringCost->price / 2;
     	$totalPrice += $costMain;
     	
     	$costCross = 0;
     	if(!$this->ActivateStringCross->Checked){
     		$costCross = $costMain;
     	}else{
     		$crossString = TblStrings::finder()->findBy_id($this->DDLStringCross->SelectedValue);
     		$crossStringCost = RelStringPrice::finder()->find('id_strings = ? AND id_stringer = ?',$crossString->id, $this->User->UserDB->id);    	
     		$costCross = $crossStringCost->price / 2;
     	}
     	$totalPrice += $costCross;
     	
     	if($this->DDLGrips->SelectedValue != 0){
     		$totalPrice += TblGrips::finder()->findBy_id($this->DDLGrips->SelectedValue)->price;
     	}
     	
     	if($this->DDLOvergrips->SelectedValue != 0){
     		$totalPrice += TblOvergrips::finder()->findBy_id($this->DDLOvergrips->SelectedValue)->price;
     	}
     	
     	$this->TotalPrice->Text = $totalPrice;
     	
     }

     public function cancelNewClicked(){
     	$this->Response->redirect($this->Service->constructUrl('Job.EditCloneJob'));
     }
     
     public function saveClicked(){
    	$idJob = null;
    	$idJob = (int)$this->Request['idJob'];
    	$idCloneJob = null;
    	$idCloneJob = (int)$this->Request['idCloneJob'];
    	
     	$job = null;
     	
     	if($idJob != null){
     		$job = TblStringingJobs::finder()->findBy_id($idJob);
     	}
     	else if($idCloneJob != null){
     		$jobClonante = TblStringingJobs::finder()->findBy_id($idCloneJob);
     		$job = new TblStringingJobs();
     		$job->tbl_racquets_user_id = $jobClonante->tbl_racquets_user_id;
     		$job->tbl_users_id_stringer = $jobClonante->tbl_users_id_stringer;
     		$job->old_job_id = $jobClonante->id;
     	}else{
     		$job = new TblStringingJobs();
     		$job->tbl_racquets_user_id = $this->DDLCustomerRacquets->SelectedValue;
     		$job->tbl_users_id_stringer = $this->User->UserDB->id;
     	}
     		
     	$job->tbl_stringing_machines_id = $this->DDLStriningMachine->SelectedValue;
     	$job->tbl_strings_id_main = $this->DDLStringMains->SelectedValue;
     	if(!$this->ActivateStringCross->Checked)
     		$job->tbl_strings_id_cross = $this->DDLStringMains->SelectedValue;
     	else
     		$job->tbl_strings_id_cross = $this->DDLStringCross->SelectedValue;
     	$job->tbl_stringing_type_id = $this->DDLStringingType->SelectedValue;
     	$job->date_stringing = $this->DateStringing->getDataOk();
     	$job->weight_main = $this->WeightMains->Text;
     	$job->prestretch_main = $this->PrestretchMain->Text;
     	
     	if(!$this->ActivateStringCross->Checked){
     		$job->wieght_cross = $this->WeightMains->Text;
     		$job->prestretch_cross = 0;
     	}else{
     		$job->wieght_cross = $this->WeightCross->Text;
     		$job->prestretch_cross = $this->PrestretchCross->Text;
     	}
     	$job->old_job_id = 0;
     	$job->dynamic_tension = $this->DynamicTension->Text;
     	$job->stencyl = $this->DDLStencyl->SelectedValue;
     	$job->grommets_guard = $this->DDLGrommetsGuard->SelectedValue;
     	$job->tbl_grip_id = $this->DDLGrips->SelectedValue;
     	$job->tbl_overgrip_id = $this->DDLOvergrips->SelectedValue;
     	$job->note = $this->Note->Text;
     	$job->total_price = $this->TotalPrice->Text;
     	$job->paid = 0;
     	$job->broken = $this->Broken->Checked;
        $job->cut = $this->Cut->Checked;
        $job->duration_string =  $this->DurationString->Text;   
        $job->note_customer = $this->NoteCustomer->Text;
     	$job->save();
     		
     	$this->Response->redirect($this->Service->constructUrl('Job.EditCloneJob'));
     }

     public function DeleteJob(){
    	$idJob = null;
    	$idJob = (int)$this->Request['idJob'];
     	
     	$job = null;
     	if($idJob != null){
     		$job = TblStringingJobs::finder()->findBy_id($idJob);
     	}
     	$job->delete();
     	
     	$this->Response->redirect($this->Service->constructUrl('Job.EditCloneJob'));
     }
     
	public function MakePDF()
	{
		$stringJob = $this->formatJob((int)$this->Request['idJob']);
		$job = TblStringingJobs::finder()->findBy_id((int)$this->Request['idJob']);
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor("www.stringtools.it");
		$pdf->SetTitle($stringJob);
		
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		// set font
		$pdf->SetFont('times', '', 13);
		
		// add a page
		$pdf->AddPage();
		
		//immagine utente
		$urlJpg = 'themes/White/images/logo/'.$this->User->UserDB->id.".jpg";
		//$urlPng = 'themes/White/images/logo/'.$this->User->UserDB->id.".png";
		if (file_exists($urlJpg)) 
			$pdf->Image($urlJpg,10,6,40,15,'JPG','www.stringtools.it','', true, 150, '', false, false, 0);
		else
			$pdf->Image('themes/White/images/logo-st-www.jpg',10,6,40,15,'JPG','www.stringtools.it','', true, 150, '', false, false, 0);
		//$pdf->Image('themes/White/images/logo-st-www.jpg',10,6,40, '', '', 'http://www.tcpdf.org', '', false, 300);
			
		$pdf->Ln(15);
			
		$pdf->Cell(0, 0, $this->User->UserDB->surname . " " . $this->User->UserDB->name, 0, 1, 'L', 0, '', 0);
		if($this->User->UserDB->telephone != "")
			$pdf->Cell(0, 0, $this->User->UserDB->telephone, 0, 1, 'L', 0, '', 1);
		if($this->User->UserDB->mobile_telephone != "")
			$pdf->Cell(0, 0, $this->User->UserDB->mobile_telephone, 0, 1, 'L', 0, '', 1);
		if($this->User->UserDB->email != "")
			$pdf->Cell(0, 0, $this->User->UserDB->email, 0, 1, 'L', 0, '', 1);
			
		$pdf->Ln(10);
		
		$pdf->Cell(0, 0, date("d-m-Y") , 0, 1, 'R', 0, '', 1);
		
		$pdf->Ln(10);
		
		$pdf->SetFont('times', '', 16);
		$pdf->Cell(0, 0, Prado::localize('CLAIM_CHECK'), 0, 1, 'C', 0, '', 1);
		
		$pdf->Ln(10);
		
		$pdf->SetFont('times', '', 13);
		$pdf->writeHTML($this->makeHtmlJob($job), true, false, true, false, '');
		$filename = $stringJob .'.pdf';
		
		header("Content-Description: File Transfer");
		header('Content-Type: application/octet-stream');
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename='.$filename);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Cache-Control: private", false); // required for certain browsers
		header('Pragma: public');
		//header('Content-Length: ' . filesize($filename));
		
		$pdf->Output($filename, 'D');
	}
	
	public function SendMail()
	{		
		$stringJob = $this->formatJob((int)$this->Request['idJob']);
		$job = TblStringingJobs::finder()->findBy_id((int)$this->Request['idJob']);		
		$racquetCustomer = TblRacquetsUser::finder()->findBy_id($job->tbl_racquets_user_id);		
		$customer = TblUsers::finder()->findBy_id($racquetCustomer->tbl_users_id);
		
		
		$mail = new PHPMailer();
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true;
		$mail->Host = $this->Application->Parameters['SMTP_HOST'];
		$mail->Port = $this->Application->Parameters['SMTP_PORT'];
		$mail->Username = $this->Application->Parameters['SMTP_USERNAME'];
		$mail->Password = $this->Application->Parameters['SMTP_PASSWORD'];
		$mail->SetFrom($this->User->UserDB->email, $this->User->UserDB->surname . " " . $this->User->UserDB->name);
		$mail->Subject = Prado::localize("StringingMailSubject");
		
		$body = "Dear " . $customer->name . " " . $customer->surname . "<br>";
		/*$body .= "Thank you for registering at the StringTools. Before we can activate your account one last step must be taken to complete your registration.";
		$body .= "<br><br>";
		$body .= "Please note - you must complete this last step to become a registered member. You will only need to visit this URL once to activate your account.";
		$body .= "<br><br>";
		$body .= "To complete your registration, please visit this URL:";
		$body .= "<br>";
		$body .= $this->Application->Parameters['SITE_LINK']."/index.php?page=User.Confirm&id=".$userRecord->id."&code=".$userRecord->confirm_code;
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
		$body .= $this->Application->Parameters['SITE_LINK'];
		$mail->IsHTML(true);
		$mail->Body =$body;
		$mail->AddAddress($customer->email);
		if(!$mail->Send()) {
			echo "errore SendMail:".$mail->ErrorInfo;
			$error = 'Mail error: '.$mail->ErrorInfo;
			return false;
		} else {
			//echo " SendMail ok";
			$error = 'Message sent!';
			return true;
		}*/
	}
     
}

