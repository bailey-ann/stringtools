<?php

require_once('class.phpmailer.php');

class ManageJob extends FunctionList
{
	private $customer = null;
	private $_data_user_racquets;
	private $userRacquetSelect;
	private $_edit_job = null;
	
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	$this->Page->Title = Prado::localize('ManageJob');
    	$this->Search->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->Cancel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->Back->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/list-job.gif';
		$this->Cancel_2->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
    	$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
    	$this->CalculatePrice->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/calculate-price.gif';
    	$this->Delete->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/delete.gif';
    	
    	$idUser = null;
    	$idUser = (int)$this->Request['idUser'];
    	$idJob = null;
    	$idJob = (int)$this->Request['idJob'];
    	$idCloneJob = null;
    	$idCloneJob = (int)$this->Request['idCloneJob'];
    	if($idUser != null){
    		
    		if(!$this->IsPostBack)
	        {	        	
	        	$this->zone_list_racquets_customer->Visible = true;
	        	$this->customer = TblUsers::finder()->findBy_id($idUser);
    			$this->setViewState('customer',$this->customer);
    			$this->MANAGE_JOB_TITLE->Text = Prado::localize('NewJob') . " " . $this->customer->name . " " . $this->customer->surname;
    			$this->editable->Visible = false;
	            $this->DataGridUserRacquets->DataSource=$this->DataUserRacquets;
	            $this->DataGridUserRacquets->dataBind();
	            $this->Delete->visible = false;
	        }
    	}else if($idJob != null){
    		
    		if(!$this->IsPostBack)
	        {	
	        	$this->zone_list_racquets_customer->Visible = false;
	        	$this->_edit_job = TblStringingJobs::finder()->findBy_id($idJob);
	        	$racquetCustomer = TblRacquetsUser::finder()->findBy_id($this->_edit_job->tbl_racquets_user_id);
	        	$this->customer = TblUsers::finder()->findBy_id($racquetCustomer->tbl_users_id);
    			$this->setViewState('customer',$this->customer);
    			$this->MANAGE_JOB_TITLE->Text = Prado::localize('EditJob') . " " . $this->customer->name . " " . $this->customer->surname;
    			$this->editable->Visible = false;
	            $this->DataGridUserRacquets->DataSource=$this->DataUserRacquets;
	            $this->DataGridUserRacquets->dataBind();
	            
	            $i = 0;
	    		foreach($this->DataUserRacquets as $row){
	    			if($row->id == $racquetCustomer->id){
	    				$this->DataGridUserRacquets->SelectedItemIndex = $i;
	    			}
	    			$i++;
	    		}
	    		
	    		$this->createEditZone($this->_edit_job, false);
	    		$this->Delete->visible = true;
	        }
    	}else if($idCloneJob != null){
    		
    		if(!$this->IsPostBack)
	        {	 
	        	$this->zone_list_racquets_customer->Visible = false;   
	        	$this->_edit_job = TblStringingJobs::finder()->findBy_id($idCloneJob);
	        	$racquetCustomer = TblRacquetsUser::finder()->findBy_id($this->_edit_job->tbl_racquets_user_id);
	        	$this->customer = TblUsers::finder()->findBy_id($racquetCustomer->tbl_users_id);
    			$this->setViewState('customer',$this->customer);
    			$this->MANAGE_JOB_TITLE->Text = Prado::localize('EditJob') . " " . $this->customer->name . " " . $this->customer->surname;
    			$this->editable->Visible = false;
	            $this->DataGridUserRacquets->DataSource=$this->DataUserRacquets;
	            $this->DataGridUserRacquets->dataBind();
	            
	            $i = 0;
	    		foreach($this->DataUserRacquets as $row){
	    			if($row->id == $racquetCustomer->id){
	    				$this->DataGridUserRacquets->SelectedItemIndex = $i;
	    			}
	    			$i++;
	    		}
	    		
	    		$this->createEditZone($this->_edit_job, true);
	    		$this->Delete->visible = true;
	        }
    	}else{
    		
    		if(!$this->IsPostBack)
	        {
	        	
    			$this->editable->Visible = false;
	            $this->DataGridUserRacquets->DataSource=$this->DataUserRacquets;
	            $this->DataGridUserRacquets->dataBind();
	            
	        }
    	}
    }
    
    /*** zona lista racchette utente ***/
    protected function getDataUserRacquets()
    {
        if($this->_data_user_racquets===null)
            $this->loadDataUserRacquets();
        return $this->_data_user_racquets;
    }
    
    protected function loadDataUserRacquets()
    {
        $this->CreateArrayUserRacquets();
        $this->saveDataUserRacquets();
    }
    
    protected function saveDataUserRacquets()
    {
        $this->setViewState('DataUserRacquets',$this->_data_user_racquets);
    }
    
    protected function CreateArrayUserRacquets($brand = '', $model = '', $serial = '')
    {
    	$param = array();
	    $param['id'] = $this->getViewState('customer',null)->id;
	    $param['serial'] = "%".$serial."%";
	    $param['brand'] = "%".$brand."%";
	    $param['model'] = "%".$model."%";
    	
		$sqlmap = Prado::getApplication()->Modules['sqlmap']->Client;
	    $this->_data_user_racquets = $sqlmap->queryForList("SelectTblRacquetUser", $param);

    	foreach($this->_data_user_racquets as $row){   
    		$row->racquet = TblRacquets::finder()->findBy_id($row->tbl_racquets_id);
    		$row->grip = TblGripSize::finder()->findBy_id($row->tbl_grip_size_id);
    	}
    	
    	$brand_name = array();
        $model = array();
        $i = 0;
        foreach($this->_data_user_racquets as $row){           	
        	$brand = TblBrands::finder()->findBy_id($row->racquet->tbl_brands_id);
        	$brand_name[$i] = $brand->description;
        	$row->racquet->brand_name = $brand->description;
		    $model[$i] = $row->racquet->model;
		    $i++;
        }
    	array_multisort($brand_name,SORT_ASC,SORT_STRING, $model,SORT_ASC,SORT_STRING, $this->_data_user_racquets);

    }
    

    
    public function onSearchUserRacquets($param){
		$this->DataGridUserRacquets->SelectedItemIndex = -1;
		$this->CreateArrayUserRacquets($this->FilterCollection_user_brand_racquet->getNoFieldCondition(),
			$this->FilterCollection_user_model_racquet->getNoFieldCondition(), 
			$this->FilterCollection_serial_model_racquet->getNoFieldCondition() );
		$this->DataGridUserRacquets->DataSource=$this->DataUserRacquets;
        $this->DataGridUserRacquets->dataBind();
	}
	
	public function onClearUserRacquets($param){
		$this->DataGridUserRacquets->SelectedItemIndex = -1;
		$this->FilterCollection_user_brand_racquet->clear();
		$this->FilterCollection_user_model_racquet->clear();
		$this->FilterCollection_serial_model_racquet->clear();
		$this->CreateArrayUserRacquets('','','');
		$this->DataGridUserRacquets->DataSource=$this->DataUserRacquets;
        $this->DataGridUserRacquets->dataBind();
	}
    
    public function changePageUserRacquets($sender,$param)
    {
        $this->DataGridUserRacquets->CurrentPageIndex=$param->NewPageIndex;
        $this->DataGridUserRacquets->DataSource=$this->DataUserRacquets;
        $this->DataGridUserRacquets->dataBind();
    }
 
    public function pagerCreatedUserRacquets($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,Prado::localize('Page').': ');
    }
    
    public function selectRacquetCustomer($sender, $param){
    	$this->userRacquetSelect = TblRacquetsUser::finder()->findBy_id($param->Item->IDColumnUserRacquet->Text);
    	$this->setViewState('userRacquetSelect',$this->userRacquetSelect);
    	$this->createNewZone();
    }
    /*** zona lista racchette utente ***/
    
    
    
    
     /*** zona dati incordatura ***/
     
     public function createNewZone(){
     	
     	$this->editable->Visible = true;
     	$this->EDIT_RACQUET->Visible = false;
    	$this->DATA_JOB_TITLE->Text = Prado::localize('DataNewJob');
    	
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
     	
     	$racquetUser = TblRacquetsUser::finder()->findBy_id($job->tbl_racquets_user_id);
     	$racquet = TblRacquets::finder()->findBy_id($racquetUser->tbl_racquets_id);
     	$brandRacquet = TblBrands::finder()->findBy_id($racquet->tbl_brands_id);
    	$this->EDIT_RACQUET->Text = $brandRacquet->description . " " . 	$racquet->model . " " . $racquetUser->serial;
    	
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
     	
     	$costCustomer = $this->getViewState('customer',null)->cost;
     	
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
     
     public function backClicked(){
     	$this->Response->redirect($this->Service->constructUrl('Job.ListJobs', array('idUser'=>$this->getViewState('customer',null)->id), false));
     }
     
     public function cancelNewClicked(){
     	$idUser = null;
    	$idUser = (int)$this->Request['idUser'];
    	$idJob = null;
    	$idJob = (int)$this->Request['idJob'];
    	$idCloneJob = null;
    	$idCloneJob = (int)$this->Request['idCloneJob'];
    	
    	if($idUser != null){
     		$this->editable->Visible = false;
     		$this->DataGridUserRacquets->SelectedItemIndex = -1;
    	}else if($idJob != null){
     		$this->Response->redirect($this->Service->constructUrl('Job.ListJobs', array('idUser'=>$this->getViewState('customer',null)->id), false));
     	}else if($idCloneJob != null){
     		$this->Response->redirect($this->Service->constructUrl('Job.ListJobs', array('idUser'=>$this->getViewState('customer',null)->id), false));
     	}
    	
     }
     
     public function saveClicked(){
     	$idUser = null;
    	$idUser = (int)$this->Request['idUser'];
    	$idJob = null;
    	$idJob = (int)$this->Request['idJob'];
    	$idCloneJob = null;
    	$idCloneJob = (int)$this->Request['idCloneJob'];
    	
     	$job = null;
     	if($idUser != null){
     		$job = new TblStringingJobs();
     		$job->tbl_racquets_user_id = $this->getViewState('userRacquetSelect',null)->id;
     		$job->tbl_users_id_stringer = $this->User->UserDB->id;
     	}    		
     	else if($idJob != null){
     		$job = TblStringingJobs::finder()->findBy_id($idJob);
     	}
     	else if($idCloneJob != null){
     		$jobClonante = TblStringingJobs::finder()->findBy_id($idCloneJob);
     		$job = new TblStringingJobs();
     		$job->tbl_racquets_user_id = $jobClonante->tbl_racquets_user_id;
     		$job->tbl_users_id_stringer = $jobClonante->tbl_users_id_stringer;
     		$job->old_job_id = $jobClonante->id;
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
     	$job->save();
     		
     	$this->Response->redirect($this->Service->constructUrl('Job.ListJobs', array('idUser'=>$this->getViewState('customer',null)->id), false));
     }
     
     /*** zona dati incordatura ***/
     
     
     public function DeleteJob(){
    	$idJob = null;
    	$idJob = (int)$this->Request['idJob'];
     	
     	$job = null;
     	if($idJob != null){
     		$job = TblStringingJobs::finder()->findBy_id($idJob);
     	}
     	$job->delete();
     	
     	$this->Response->redirect($this->Service->constructUrl('Job.ListJobs', array('idUser'=>$this->getViewState('customer',null)->id), false));
     }
     
}

