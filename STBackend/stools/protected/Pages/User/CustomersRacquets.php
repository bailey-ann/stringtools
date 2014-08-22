<?php

class CustomersRacquets extends TPage
{
	private $_data=null;
	private $_data_user_racquets=null;
	private $_data_add_racquets=null;
	private $userSelect;
	private $racquetSelect;
	private $userRacquetSelect;
	private $sort;
	
	/*** inizio zona lista customers ***/
	
	protected function getData()
    {
        if($this->_data===null)
            $this->loadData();
        return $this->_data;
    }
    
    protected function CreateArray($name = '', $surname = '', $order = ''){
	    $sqlmap = $this->Application->Modules['sqlmap']->Database;
		$sqlmap->Active = true;
		$sql = "SELECT tbl_users.* FROM tbl_users INNER JOIN rel_stringer_customer ON rel_stringer_customer.id_customer = tbl_users.id where rel_stringer_customer.id_stringer = " . $this->User->UserDB->id;
		$stringCriteria = " ";
		if($name)
			$stringCriteria .= " AND " . $name;
		if($surname != ''){ 		
    		$stringCriteria .= " AND " . $surname;
    	}
    	$sql .= $stringCriteria;
    	if($order == 'name')
    		$sql .= " order by name";
    	else if($order == 'surname')
    		$sql .= " order by surname";
    	$command = $sqlmap->createCommand($sql);
    	$this->_data = $command->query()->readAll();
    }
    
    protected function loadData()
    {
        if(($this->_data=$this->getViewState('Data',null))===null)
        {			
	    	$this->CreateArray();
            $this->saveData();
        }
    }
    
    protected function RefreshData()
    {
        $sqlmap = Prado::getApplication()->Modules['sqlmap']->Client;
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
    	
    	
    	$this->Page->Title = Prado::localize('ManageCustomersRacquets');
    	$this->Search->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->Cancel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->Search_1->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->Cancel_1->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->Search_2->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->Cancel_2->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->btnAddRacquet->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/new-racquet.gif';
		$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
		$this->Cancel_3->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->Help->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/help.gif';
    	$this->btnCancelSelect->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
        if(!$this->IsPostBack)
        {
        	$this->ResetAll();
            $this->DataGridCustomers->DataSource=$this->Data;
            $this->DataGridCustomers->dataBind();
            
            $idUser = null;
	    	$idUser = (int)$this->Request['idUser'];
	    	
	    	if($idUser != null){
	    		$i = 0;
	    		foreach($this->Data as $row){
	    			if($row['id'] == $idUser){
	    				$this->DataGridCustomers->SelectedItemIndex = $i;
	    				$this->selectBackCustomer($idUser);
	    			}
	    				
	    			$i++;
	    		}
	    	}
        }
        
    }
    
	public function itemCreated($sender, $param) {
		$item = $param->Item;
		if($this->User->UserDB->type_user_id == 4)
			$param->Item->Cells['5']->Visible = false;
		else
			$param->Item->Cells['5']->Visible = true;
    }
    
    public function selectBackCustomer($id){	
		
		$this->zone_list_racquets_customer->Visible = true;
		$this->zone_list_add_racquets_customer->Visible = false;
		$this->btnAddRacquet->Visible = true;
		$this->btnCancelSelect->Visible = true;
		$this->userSelect = TblUsers::finder()->findBy_id($id);
		$this->setViewState('userSelect',$this->userSelect);
		
		$this->LBL_LIST_RACQUETS_USER->Text = Prado::localize('List racquets insert for the customer') . " " .$this->userSelect->name . " " . $this->userSelect->surname;
		
		$this->loadDataUserRacquets();
		$this->DataGridUserRacquets->DataSource=$this->DataUserRacquets;
        $this->DataGridUserRacquets->dataBind();
	}
    
    public function changePage($sender,$param)
    {
    	//$this->ResetAll();
        $this->DataGridCustomers->CurrentPageIndex=$param->NewPageIndex;
        $this->CreateArray($this->FilterCollection_name->getCondition(),
        		$this->FilterCollection_surname->getCondition() ,$this->getViewState('sort',''));
        $this->DataGridCustomers->DataSource=$this->Data;
        $this->DataGridCustomers->dataBind();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,Prado::localize('Page').': ');
    }
    
    protected function sortData($data,$key)
    {
    	$compare = create_function('$a,$b','if ($a["'.$key.'"] == $b["'.$key.'"]) {return 0;}else {return ($a["'.$key.'"] > $b["'.$key.'"]) ? 1 : -1;}');
    	//usort($data,$compare) ;
    	return $data ;
    }
    
    public function sortDataGrid($sender,$param)
    {
    	$this->sort = $param->SortExpression;
    	$this->setViewState('sort',$this->sort);
    	$this->CreateArray($this->FilterCollection_name->getCondition(), $this->FilterCollection_surname->getCondition() ,$this->sort);
    	
    	$this->DataGridCustomers->DataSource=$this->sortData($this->Data,$param->SortExpression);
    	$this->DataGridCustomers->dataBind();
    }
    
    public function onSearch($param){
		$this->ResetAll();
		$this->CreateArray($this->FilterCollection_name->getCondition(), $this->FilterCollection_surname->getCondition() );
		$this->DataGridCustomers->DataSource=$this->Data;
        $this->DataGridCustomers->dataBind();
	}
	
	public function onClear($param){
		$this->ResetAll();
		$this->FilterCollection_name->clear();
		$this->FilterCollection_surname->clear();
		$this->CreateArray('','');
		$this->DataGridCustomers->DataSource=$this->Data;
        $this->DataGridCustomers->dataBind();
	}
	
	
	public function selectCustomer($sender, $param){
		$item = $param->Item;
		$this->zone_list_racquets_customer->Visible = true;
		$this->zone_list_add_racquets_customer->Visible = false;
		$this->btnAddRacquet->Visible = true;
		$this->btnCancelSelect->Visible = true;
		$this->userSelect = TblUsers::finder()->findBy_id($param->Item->IDColumn->Text);
		$this->setViewState('userSelect',$this->userSelect);
		
		$this->LBL_LIST_RACQUETS_USER->Text = Prado::localize('List racquets insert for the customer') . " " .$this->userSelect->name . " " . $this->userSelect->surname;
		
		$this->loadDataUserRacquets();
		$this->DataGridUserRacquets->DataSource=$this->DataUserRacquets;
        $this->DataGridUserRacquets->dataBind();
	}
	
	
	public function ResetAll(){
		$this->DataGridCustomers->SelectedItemIndex = -1;
		$this->DataGridUserRacquets->SelectedItemIndex = -1;
        $this->DataGridAddRacquets->SelectedItemIndex = -1;
		$this->zone_list_racquets_customer->Visible = false;
		$this->zone_list_add_racquets_customer->Visible = false;
		$this->btnAddRacquet->Visible = false;
		$this->btnCancelSelect->Visible = false;
		$this->editable->Visible = false;
	}
	
	/*** fine zona lista customers ***/
	
	
	
	
	
	
	
	/*** inizio zona lista racchette customer ***/
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
	    $param['id'] = $this->getViewState('userSelect',null)->id;
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
		    $arraNumber = $sqlmap->queryForList("CountListJobByUserRacquet",$row->id);
		    if(count($arraNumber) > 0)
		    	$row->numberOfStringing = $arraNumber[0];
		    $i++;
        }
    	array_multisort($brand_name,SORT_ASC,SORT_STRING, $model,SORT_ASC,SORT_STRING, $this->_data_user_racquets);

    }
    
    public function onSearchUserRacquets($param){
		$this->zone_list_add_racquets_customer->Visible = false;
		$this->DataGridUserRacquets->SelectedItemIndex = -1;
        $this->DataGridAddRacquets->SelectedItemIndex = -1;
		$this->onClearAddRacquet(null);
		$this->editable->Visible = false;
		$this->btnAddRacquet->visible = false;
		$this->CreateArrayUserRacquets($this->FilterCollection_user_brand_racquet->getNoFieldCondition(),
			$this->FilterCollection_user_model_racquet->getNoFieldCondition(), 
			$this->FilterCollection_serial_model_racquet->getNoFieldCondition() );
		$this->DataGridUserRacquets->DataSource=$this->DataUserRacquets;
        $this->DataGridUserRacquets->dataBind();
	}
	
	public function onClearUserRacquets($param){
		$this->zone_list_add_racquets_customer->Visible = false;
		$this->DataGridUserRacquets->SelectedItemIndex = -1;
        $this->DataGridAddRacquets->SelectedItemIndex = -1;
		$this->onClearAddRacquet(null);
		$this->editable->Visible = false;
		$this->btnAddRacquet->visible = false;
		
		$this->FilterCollection_user_brand_racquet->clear();
		$this->FilterCollection_user_model_racquet->clear();
		$this->FilterCollection_serial_model_racquet->clear();
		$this->CreateArrayUserRacquets('','','');
		$this->DataGridUserRacquets->DataSource=$this->DataUserRacquets;
        $this->DataGridUserRacquets->dataBind();
	}
	
	public function onItemCommand($sender,$param)
	{
		switch ($param->getCommandName())
		{
			case "delete":
				$this->Delete($sender,$param);
				break;
		}
	}
	
	public function Delete($sender,$param)
	{
		$userRacquetSelect = TblRacquetsUser::finder()->findBy_id($param->Item->IDColumnUserRacquet->Text);
		$userRacquetSelect->active = 0;
		$userRacquetSelect->save();
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
        
        $this->btnAddRacquet->visible = false;
        $this->btnCancelSelect->visible = false;
        $this->zone_list_add_racquets_customer->visible = false;
        $this->editable->Visible = true;
        $item = $param->Item;
        $this->TypeEdit->Text = Prado::localize('Edit racquet customer');
        $this->userRacquetSelect = TblRacquetsUser::finder()->findBy_id($param->Item->IDColumnUserRacquet->Text);
        $this->userRacquetSelect->racquet = TblRacquets::finder()->findBy_id($this->userRacquetSelect->tbl_racquets_id);
    	$this->userRacquetSelect->grip = TblGripSize::finder()->findBy_id($this->userRacquetSelect->tbl_grip_size_id);
    	
    	
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $brands = TblBrands::finder()->findAll($criteria);
        $this->DDLBrands->DataSource=$brands;
        $this->DDLBrands->dataBind();
        $this->DDLBrands->SelectedValue = $this->userRacquetSelect->racquet->tbl_brands_id;
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $patterns = TblRacquetsPattern::finder()->findAll($criteria);
        $this->DDLPatterns->DataSource=$patterns;
        $this->DDLPatterns->dataBind();
        $this->DDLPatterns->SelectedValue = $this->userRacquetSelect->racquet->tbl_racquets_pattern_id;
        
        $this->Model->Text = $this->userRacquetSelect->racquet->model;
        $this->HeadSize->Text = $this->userRacquetSelect->racquet->head_size;
    	$this->Length->Text = $this->userRacquetSelect->racquet->length;
    	
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['europe_size'] = 'asc';
        $gripSize = TblGripSize::finder()->findAll($criteria);
        $gripSizeArray = array();
        foreach($gripSize as $row){ 
        	$gripSizeArray[$row->id] = $row->europe_size . " (" . $row->usa_size.")";
        }
        $this->DDLGripSize->DataSource=$gripSizeArray;
        $this->DDLGripSize->dataBind();
        $this->DDLGripSize->SelectedValue = $this->userRacquetSelect->tbl_grip_size_id;
        
		$this->Serial->Text = $this->userRacquetSelect->serial;
    	$this->WeightUnstrung->Text = $this->userRacquetSelect->weight_unstrung;
		$this->WeightStrung->Text = $this->userRacquetSelect->weight_strung;
		$this->Balance->Text = $this->userRacquetSelect->balance;
		$this->Swingweight->Text = $this->userRacquetSelect->swingweight;
		$this->Stiffness->Text = $this->userRacquetSelect->stiffness;
		$this->BeamWidth->Text = $this->userRacquetSelect->racquet->beam_width;
		$this->DateBuy->setTimeStamp(strtotime($this->userRacquetSelect->date_buy));
		$this->Note->Text = $this->userRacquetSelect->note;
		$this->setViewState('userRacquetSelect',$this->userRacquetSelect);
    	
	}
    
	
	/*** fine zona lista racchette customer ***/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*** inizio zona lista racchette da aggiungere ***/
	
	public function addRacquet($param){
		/*$this->LBL_LIST_RACQUETS_TO_ADD->Text = Prado::localize('List racquets to add ');
		$this->zone_list_add_racquets_customer->Visible = true;
		$this->zone_list_racquets_customer->Visible = true;
		$this->btnAddRacquet->Visible = false;
		$this->loadDataAddRacquets();
		$this->DataGridAddRacquets->DataSource=$this->_data_add_racquets;
        $this->DataGridAddRacquets->dataBind();*/
		$this->Response->redirect($this->Service->constructUrl('User.NewCustomersRacquets', array('idUser'=>$this->getViewState('userSelect',null)->id), false));
	}
	
	public function cancelSelect(){
		$this->DataGridCustomers->SelectedItemIndex = -1;
		$this->DataGridUserRacquets->SelectedItemIndex = -1;
        $this->DataGridAddRacquets->SelectedItemIndex = -1;
		$this->zone_list_racquets_customer->Visible = false;
		$this->zone_list_add_racquets_customer->Visible = false;
		$this->btnAddRacquet->Visible = false;
		$this->btnCancelSelect->Visible = false;
		$this->editable->Visible = false;
	}
	
	protected function getDataAddRacquets()
    {
        if($this->_data_add_racquets===null)
            $this->loadDataAddRacquets();
        return $this->_data_add_racquets;
    }
    
    protected function loadDataAddRacquets()
    {
        //if(($this->_data_add_racquets=$this->getViewState('DataAddRacquets',null))===null)
        //{			
	    	$this->CreateArrayAddRacquets($this->FilterCollection_brand_add_racquet->getCondition(),
				 $this->FilterCollection_model_add_racquet->getCondition() );
            $this->saveDataAddRacquets();
        //}
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
		$this->CreateArrayAddRacquets($this->FilterCollection_brand_add_racquet->getCondition(),
				 $this->FilterCollection_model_add_racquet->getCondition() );
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
    	$modify = false;
    	if($this->getViewState('userRacquetSelect',null) == null){
    		$this->userRacquetSelect = new TblRacquetsUser();
    		$this->userRacquetSelect->tbl_racquets_id = $this->getViewState('racquetSelect',null)->id;
    		$this->userRacquetSelect->tbl_users_id = $this->getViewState('userSelect',null)->id;
    	}   		
    	else{
    		$this->userRacquetSelect=$this->getViewState('userRacquetSelect',null);
    		$modify = true;
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
    	
    	$this->onClearAddRacquet(null);
    	$this->zone_list_add_racquets_customer->Visible = false;
    	$this->editable->Visible = false;
    	$this->btnAddRacquet->Visible = true;
    	$this->btnCancelSelect->Visible = true;
    	$this->zone_list_racquets_customer->Visible = true;
    	$this->loadDataUserRacquets();
		$this->DataGridUserRacquets->DataSource=$this->DataUserRacquets;
        $this->DataGridUserRacquets->dataBind();
    	
    }
    public function cancelAddClicked()
    {
    	$this->editable->Visible = false;
    	$this->btnAddRacquet->Visible = true;
    	$this->btnCancelSelect->Visible = true;
        $this->DataGridUserRacquets->SelectedItemIndex = -1;
        $this->DataGridAddRacquets->SelectedItemIndex = -1;
        $this->DataGridAddRacquets->DataSource=$this->DataAddRacquets;
        $this->DataGridAddRacquets->dataBind();
    }
	/*** fine zona lista racchette da aggiungere ***/
}

