<?php

class StringingMachines extends TPage
{
	private $_data = null;
	private $machine =null;
	
	protected function getData()
    {
        if($this->_data===null)
            $this->loadData();
        return $this->_data;
    }
    
    protected function CreateArray(){
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['model'] = 'asc';
        $this->_data= TblStringingMachines::finder()->findAll($criteria);
        $brand_name = array();
        $model = array();
        $i = 0;
        foreach($this->_data as $row){           	
        	$brand = TblBrands::finder()->findBy_id($row->tbl_brands_id);
        	$row->brand_name = $brand->description;
        	$brand_name[$i] = $row->brand_name;
		    $model[$i] = $row->model;
		    $i++;
        }
    	array_multisort($brand_name,SORT_ASC,SORT_STRING, $model,SORT_ASC,SORT_STRING, $this->_data);
    }
    
    protected function loadData()
    {
        if(($this->_data=$this->getViewState('Data',null))===null)
        {
        	$this->CreateArray();
            $this->saveData();
        }
        
    	if($this->User->UserDB->type_user_id == 4){
        	$this->Save->Visible = false;
        }else{
        	$this->Save->Visible = true;
        }
    }
    
    protected function RefreshData()
    {
        $this->CreateArray();
        $this->saveData();
    }
 
    protected function saveData()
    {
        $this->setViewState('Data',$this->_data);
    }
	 
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	$this->Page->Title = Prado::localize('ManageStringingMachines');
    	$this->editable->Visible = false;
		$this->New->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/new-machine.gif';
		$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
		$this->Cancel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
        if(!$this->IsPostBack)
        {
        	$this->DataGridMachines->SelectedItemIndex=-1;
            $this->DataGridMachines->DataSource=$this->Data;
            $this->DataGridMachines->dataBind();
        }
    }
    
    /* funzione che crea la gui per editare l'oggetto */
    public function selectStringingMachine($sender,$param)
    {
    	$this->DefaultMachine->Checked = false;
    	$this->editable->Visible = true;
    	$this->TypeEdit->Text = Prado::localize('Edit stringing machine');
    	$this->machine = TblStringingMachines::finder()->findBy_id($param->Item->IDColumn->Text);
    	
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $brands = TblBrands::finder()->findAll($criteria);
        $this->DDLBrands->DataSource=$brands;
        $this->DDLBrands->dataBind();
        $this->DDLBrands->SelectedValue = $this->machine->tbl_brands_id;
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $type = TblStringingMachinesType::finder()->findAll($criteria);
        $this->DDLType->DataSource=$type;
        $this->DDLType->dataBind();
        $this->DDLType->SelectedValue = $this->machine->tbl_stringing_machine_type_id;
        
        $this->Model->Text = $this->machine->model;
        $this->setViewState('machine',$this->machine);
        
        $personalMachine = RelStringerStringingMachine::finder()->find('id_stringer = ? AND id_stringing_machine = ?', $this->User->UserDB->id, $this->machine->id);
        if($personalMachine != null){
        	$this->Serial->Text = $personalMachine->serial;
        	$this->Note->Text = $personalMachine->note;
        	$this->DateBuy->setTimeStamp(strtotime($personalMachine->date_buy));
        	if(strtotime($personalMachine->date_calibration) != false){
        		$this->DateCalibration->setTimeStamp(strtotime($personalMachine->date_calibration));
        		$this->DateCalibration->enabled = true;
        		$this->ActivateDateCalibration->Checked = true;
        	}else{
        		$this->DateCalibration->enabled = false;
        		$this->ActivateDateCalibration->Checked = false;
        	}
        	if($personalMachine->default == 1)
        		$this->DefaultMachine->Checked = true;
        	else 
        		$this->DefaultMachine->Checked = false;
        }else{
        	$this->Serial->Text = "";
        	$this->Note->Text = "";
        	$this->DateBuy->setTimeStamp(strtotime("now"));
        	$this->DateCalibration->setTimeStamp(strtotime("now"));
        	$this->DateCalibration->enabled = false;
        	$this->ActivateDateCalibration->Checked = false;
        }
    }
    
    /* funzione che nasconde la gui per editare/creare l'oggetto */
    public function cancelClicked()
    {
        $this->DataGridMachines->SelectedItemIndex=-1;
        $this->DataGridMachines->DataSource=$this->Data;
        $this->DataGridMachines->dataBind();
    }
    
    /* funzione che crea la gui per creare l'oggetto */
    public function createClicked()
    {   	
    	$this->setViewState('machine',null);    
    	$this->DataGridMachines->SelectedItemIndex=-1;    	
    	$this->editable->Visible = true;
    	$this->TypeEdit->Text = Prado::localize('Insert new stringing machine');
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $brands = TblBrands::finder()->findAll($criteria);
        $this->DDLBrands->DataSource=$brands;
        $this->DDLBrands->dataBind();
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $type = TblStringingMachinesType::finder()->findAll($criteria);
        $this->DDLType->DataSource=$type;
        $this->DDLType->dataBind();
        
        $this->Model->Text = "";
        $this->Serial->Text = "";
        $this->Note->Text = "";
        $this->DateBuy->setTimeStamp(strtotime("now"));
        $this->DateCalibration->setTimeStamp(strtotime("now"));
        $this->ActivateDateCalibration->Checked = false;
        $this->DateCalibration->enabled = false;
    }
    
    /* funzione che edita/salva i dati l'oggetto */
    public function saveClicked()
    {   	
    	
    	if($this->getViewState('machine',null) == null)
    		$this->machine = new TblStringingMachines();
    	else
    		$this->machine=$this->getViewState('machine',null);
    	$this->machine->tbl_brands_id = $this->DDLBrands->SelectedValue;
    	$this->machine->tbl_stringing_machine_type_id = $this->DDLType->SelectedValue;
    	$this->machine->model = $this->Model->Text;
    	$this->machine->save();
    	
    	$personalMachine = RelStringerStringingMachine::finder()->find('id_stringer = ? AND id_stringing_machine = ?', $this->User->UserDB->id, $this->machine->id);
    	if($personalMachine == null){
    		$personalMachine = new RelStringerStringingMachine();
    		$personalMachine->id_stringer = $this->User->UserDB->id;
    		$personalMachine->id_stringing_machine = $this->machine->id;
    	}
    	$personalMachine->serial = $this->Serial->Text;
    	$personalMachine->date_buy=$this->DateBuy->getDataOk();
    	if($this->ActivateDateCalibration->Checked)
    		$personalMachine->date_calibration=$this->DateCalibration->getDataOk();
    	$personalMachine->note = $this->Note->Text;
    	if($this->DefaultMachine->Checked)
    		$personalMachine->default = 1;
    	else
    		$personalMachine->default = 0;
    	$personalMachine->save();
    	
    	if($this->DefaultMachine->Checked){
    		$arrayMachine = RelStringerStringingMachine::finder()->findAll('id_stringer = ? AND id_stringing_machine != ?', $this->User->UserDB->id, $this->machine->id);
    		if($arrayMachine != null){
    			foreach($arrayMachine as $row){
    				$row->default = 0;
    				$row->save();
    			}
    		}
    	}
    	
    	
    	$this->editable->Visible = false;
    	$this->RefreshData();
		
		
        $this->DataGridMachines->SelectedItemIndex=-1;
        $this->DataGridMachines->DataSource=$this->Data;
        $this->DataGridMachines->dataBind();
        
        //$this->Data_uscita->setTimeStamp(strtotime("now"));
        //$pazienteStanzaLettoRecord->data_fine_validita = $this->Data_uscita->getDataOk();
    	
    }
    
    public function ActivateDateCalibrationClicked($sender,$param)
    {
        $this->DateCalibration->enabled = $this->ActivateDateCalibration->Checked;
        $this->editable->Visible = true;
    }
    
    public function changePage($sender,$param)
    {
    	$this->DataGridMachines->SelectedItemIndex=-1;
        $this->DataGridMachines->CurrentPageIndex=$param->NewPageIndex;
        $this->DataGridMachines->DataSource=$this->Data;
        $this->DataGridMachines->dataBind();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,Prado::localize('Page').': ');
    }
}

