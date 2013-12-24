<?php

class NewCustomersRacquets extends TPage
{
	private $_data_add_racquets=null;
	private $userSelect;
	private $racquetSelect;
	private $userRacquetSelect;
	

	 
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	
    	$idUser = null;
    	$idUser = (int)$this->Request['idUser'];
    	
    	
    	$this->zone_list_add_racquets_customer->Visible = true;
    	$this->editable->Visible = false;
    	$this->Page->Title = Prado::localize('ManageCustomersRacquets');
    	$this->LBL_LIST_RACQUETS_TO_ADD->Text = Prado::localize('SelectRacquet');
    	
		$this->Search_2->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->Cancel_2->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';		
		$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
		$this->Cancel_3->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->Help->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/help.gif';
    	$this->btnCancelSelect->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
    	
        if(!$this->IsPostBack)
        {
            $this->DataGridAddRacquets->DataSource=$this->DataAddRacquets;
            $this->DataGridAddRacquets->dataBind();
        }
    }

	
	/*** inizio zona lista racchette da aggiungere ***/
	
	public function addRacquet($param){
		
		$this->zone_list_add_racquets_customer->Visible = true;
		$this->zone_list_racquets_customer->Visible = true;
		$this->btnAddRacquet->Visible = false;
		$this->loadDataAddRacquets();
		$this->DataGridAddRacquets->DataSource=$this->_data_add_racquets;
        $this->DataGridAddRacquets->dataBind();
	}
	
	protected function getDataAddRacquets()
    {
        if($this->_data_add_racquets===null)
            $this->loadDataAddRacquets();
        return $this->_data_add_racquets;
    }
    
    protected function loadDataAddRacquets()
    {
        if(($this->_data_add_racquets=$this->getViewState('DataAddRacquets',null))===null)
        {			
	    	$this->CreateArrayAddRacquets();
            $this->saveDataAddRacquets();
        }
    }
    
    protected function saveDataAddRacquets()
    {
        $this->setViewState('DataAddRacquets',$this->_data_add_racquets);
    }
	
	protected function CreateArrayAddRacquets($brand = '', $model = ''){
    	$criteria = new TActiveRecordCriteria;
    	$listBrand = "";
    	if($brand != ''){
    		$findBrand = TblBrands::finder()->findAll($brand);
    		$passo = false;
    		foreach($findBrand as $row){
    			$passo = true;
    			$listBrand .= "tbl_brands_id = " . $row->id;
    		}
    		if(!$passo){
    			$listBrand = "tbl_brands_id = 0";
    		}
    	}
    	
    	$stringCriteria = "";
    	if($listBrand != '')
    		$stringCriteria .= $listBrand;
    	if($model != ''){
    		if($listBrand != '')
    			$stringCriteria .= " and ";   		
    		$stringCriteria .= $model;
    	}
    	$criteria->Condition = $stringCriteria;
		$criteria->OrdersBy['model'] = 'asc';
        $this->_data_add_racquets= TblRacquets::finder()->findAll($criteria);
        $brand_name = array();
        $model = array();
        $i = 0;
        foreach($this->_data_add_racquets as $row){           	
        	$brand = TblBrands::finder()->findBy_id($row->tbl_brands_id);
        	$row->brand_name = $brand->description;
        	$brand_name[$i] = $row->brand_name;
		    $model[$i] = $row->model;
		    $i++;
		    
		    $pattern = TblRacquetsPattern::finder()->findBy_id($row->tbl_racquets_pattern_id);
		    $row->pattern = $pattern->description;
        }
    	array_multisort($brand_name,SORT_ASC,SORT_STRING, $model,SORT_ASC,SORT_STRING, $this->_data_add_racquets);
    }
    
    public function onClearAddRacquet($param){
		$this->editable->Visible = false;
		$this->FilterCollection_brand_add_racquet->clear();
		$this->FilterCollection_model_add_racquet->clear();
		$this->CreateArrayAddRacquets('','');
		$this->DataGridAddRacquets->DataSource=$this->DataAddRacquets;
        $this->DataGridAddRacquets->dataBind();
	}
	
	 public function onSearchAddRacquet($param){
		$this->editable->Visible = false;
		$this->CreateArrayAddRacquets($this->FilterCollection_brand_add_racquet->getCondition(), $this->FilterCollection_model_add_racquet->getCondition() );
		$this->DataGridAddRacquets->DataSource=$this->DataAddRacquets;
        $this->DataGridAddRacquets->dataBind();
        
	}
	
	public function changePageAddRacquets($sender,$param)
    {
        $this->DataGridAddRacquets->CurrentPageIndex=$param->NewPageIndex;
        $this->DataGridAddRacquets->DataSource=$this->DataAddRacquets;
        $this->DataGridAddRacquets->dataBind();
    }
 
    public function pagerCreatedAddRacquets($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,Prado::localize('Page').': ');
    }
    
    public function selectAddRacquet($sender, $param){
		$this->racquetSelect = TblRacquets::finder()->findBy_id($param->Item->IDColumnRacquet->Text);
		$this->setViewState('racquetSelect',$this->racquetSelect);
		//var_dump($this->getViewState('userSelect',null));
		//var_dump($this->getViewState('racquetSelect',null));
		$this->editable->Visible = true;
		
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $brands = TblBrands::finder()->findAll($criteria);
        $this->DDLBrands->DataSource=$brands;
        $this->DDLBrands->dataBind();
        $this->DDLBrands->SelectedValue = $this->racquetSelect->tbl_brands_id;
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $patterns = TblRacquetsPattern::finder()->findAll($criteria);
        $this->DDLPatterns->DataSource=$patterns;
        $this->DDLPatterns->dataBind();
        $this->DDLPatterns->SelectedValue = $this->racquetSelect->tbl_racquets_pattern_id;
        
        $this->Model->Text = $this->racquetSelect->model;
        $this->HeadSize->Text = $this->racquetSelect->head_size;
    	$this->Length->Text = $this->racquetSelect->length;
    	
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['europe_size'] = 'asc';
        $gripSize = TblGripSize::finder()->findAll($criteria);
        $gripSizeArray = array();
        foreach($gripSize as $row){ 
        	$gripSizeArray[$row->id] = $row->europe_size . " (" . $row->usa_size.")";
        }
        $this->DDLGripSize->DataSource=$gripSizeArray;
        $this->DDLGripSize->dataBind();
		$this->Serial->Text = "";
    	$this->WeightUnstrung->Text = $this->racquetSelect->weight_unstrung;
		$this->WeightStrung->Text = $this->racquetSelect->weight_strung;
		$this->Balance->Text = $this->racquetSelect->balance;
		$this->Swingweight->Text = $this->racquetSelect->swingweight;
		$this->Stiffness->Text = $this->racquetSelect->stiffness;
		$this->BeamWidth->Text = $this->racquetSelect->beam_width;
		$this->DateBuy->setTimeStamp(strtotime("now"));
		$this->Note->Text = $this->racquetSelect->note;
		$this->setViewState('userRacquetSelect',null);
	}
	
	public function saveAddClicked()
    {   	
    	$idUser = null;
    	$idUser = (int)$this->Request['idUser'];
    	$customer = TblUsers::finder()->findBy_id($idUser);
    		
    	$modify = false;
    	if($this->getViewState('userRacquetSelect',null) == null){
    		$this->userRacquetSelect = new TblRacquetsUser();
    		
    		$this->userRacquetSelect->tbl_racquets_id = $this->getViewState('racquetSelect',null)->id;
    		$this->userRacquetSelect->tbl_users_id = $customer->id;
    	}   		
    	else{
    		//$this->userRacquetSelect=$this->getViewState('userRacquetSelect',null);
    		//$modify = true;
    	}
    		
    	$this->userRacquetSelect->tbl_grip_size_id = $this->DDLGripSize->SelectedValue;
    	$this->userRacquetSelect->serial = $this->Serial->Text;
    	$this->userRacquetSelect->weight_unstrung = $this->WeightUnstrung->Text;
		$this->userRacquetSelect->weight_strung = $this->WeightStrung->Text;
		$this->userRacquetSelect->balance = $this->Balance->Text;
		$this->userRacquetSelect->swingweight = $this->Swingweight->Text;
		$this->userRacquetSelect->stiffness = $this->Stiffness->Text;
		$this->userRacquetSelect->date_buy=$this->DateBuy->getDataOk();
		$this->userRacquetSelect->note = $this->Note->Text;
    	$this->userRacquetSelect->save();  
    	
    	if($modify){
    		$logRacquetUser = new LogRacquetUser();
    		$logRacquetUser->id_tbl_racquet_user = $this->userRacquetSelect->id;
    		$logRacquetUser->weight_unstrung = $this->WeightUnstrung->Text;
    		$logRacquetUser->weight_strung = $this->WeightStrung->Text;
    		$logRacquetUser->balance = $this->Balance->Text;
    		$logRacquetUser->swingweight = $this->Swingweight->Text;
    		$logRacquetUser->stiffness = $this->Stiffness->Text;
    		$logRacquetUser->date_modify = date('c');
    		$logRacquetUser->note = $this->Note->Text;
    		$logRacquetUser->save(); 
    	}
    	
    	$this->Response->redirect($this->Service->constructUrl('User.CustomersRacquets', array('idUser'=>$idUser), false));
    	
    }
    
    public function cancelSelect(){
    	$idUser = null;
    	$idUser = (int)$this->Request['idUser'];
		$this->Response->redirect($this->Service->constructUrl('User.CustomersRacquets', array('idUser'=>$idUser), false));
	}
    
    
    public function cancelAddClicked()
    {
    	$this->editable->Visible = false;
        $this->DataGridAddRacquets->SelectedItemIndex = -1;
        $this->DataGridAddRacquets->DataSource=$this->DataAddRacquets;
        $this->DataGridAddRacquets->dataBind();
    }
	/*** fine zona lista racchette da aggiungere ***/
}

