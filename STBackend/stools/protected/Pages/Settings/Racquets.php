<?php

class Racquets extends TPage
{
	private $_data = null;
	private $racquet =null;
	
	protected function getData()
    {
        if($this->_data===null)
            $this->loadData();
        return $this->_data;
    }
    
    protected function CreateArray($brand = '', $model = ''){
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
        $this->_data= TblRacquets::finder()->findAll($criteria);
        $brand_name = array();
        $model = array();
        $i = 0;
        foreach($this->_data as $row){           	
        	$brand = TblBrands::finder()->findBy_id($row->tbl_brands_id);
        	$row->brand_name = $brand->description;
        	$brand_name[$i] = $row->brand_name;
		    $model[$i] = $row->model;
		    $i++;
		    
		    $pattern = TblRacquetsPattern::finder()->findBy_id($row->tbl_racquets_pattern_id);
		    $row->pattern = $pattern->description;
        }
    	array_multisort($brand_name,SORT_ASC,SORT_STRING, $model,SORT_ASC,SORT_STRING, $this->_data);
    }
    
    protected function loadData()
    {
        //if(($this->_data=$this->getViewState('Data',null))===null)
       // {
        	$this->CreateArray($this->FilterCollection_brand->getCondition(), $this->FilterCollection_model->getCondition());
            $this->saveData();
       // }
       
    	if($this->User->UserDB->type_user_id == 4){
        	$this->Save->Visible = false;
        }else{
        	$this->Save->Visible = true;
        }
    }
    
    protected function RefreshData()
    {
        $this->CreateArray($this->FilterCollection_brand->getCondition(), $this->FilterCollection_model->getCondition());
        $this->saveData();
    }
 
    protected function saveData()
    {
        $this->setViewState('Data',$this->_data);
    }
	 
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	$this->Page->Title = Prado::localize('ManageRacquets');
    	$this->editable->Visible = false;
    	$this->Search->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->Cancel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->New->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/new-racquet.gif';
		$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
		$this->Cancel_2->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
        if(!$this->IsPostBack)
        {
        	$this->DataGridRacquets->SelectedItemIndex=-1;
            $this->DataGridRacquets->DataSource=$this->Data;
            $this->DataGridRacquets->dataBind();
        }
    }
    
    /* funzione che crea la gui per editare l'oggetto */
    public function selectRacquet($sender,$param)
    {
    	$this->editable->Visible = true;
    	$this->TypeEdit->Text = Prado::localize('Edit racquet');
    	$this->racquet = TblRacquets::finder()->findBy_id($param->Item->IDColumn->Text);
    	
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $brands = TblBrands::finder()->findAll($criteria);
        $this->DDLBrands->DataSource=$brands;
        $this->DDLBrands->dataBind();
        $this->DDLBrands->SelectedValue = $this->racquet->tbl_brands_id;
              
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $patterns = TblRacquetsPattern::finder()->findAll($criteria);
        $this->DDLPatterns->DataSource=$patterns;
        $this->DDLPatterns->dataBind();
        $this->DDLPatterns->SelectedValue = $this->racquet->tbl_racquets_pattern_id;
        
        $this->Model->Text = $this->racquet->model;
        $this->HeadSize->Text = $this->racquet->head_size;
    	$this->Length->Text = $this->racquet->length;
    	$this->WeightUnstrung->Text = $this->racquet->weight_unstrung;
		$this->WeightStrung->Text = $this->racquet->weight_strung;
		$this->Balance->Text = $this->racquet->balance;
		$this->Swingweight->Text = $this->racquet->swingweight;
		$this->Stiffness->Text = $this->racquet->stiffness;
		$this->BeamWidth->Text = $this->racquet->beam_width;
		$this->Note->Text = $this->racquet->note;
        
        $this->setViewState('racquet',$this->racquet);
    }
    
    /* funzione che nasconde la gui per editare/creare l'oggetto */
    public function cancelClicked()
    {
        $this->DataGridRacquets->SelectedItemIndex=-1;
        $this->DataGridRacquets->DataSource=$this->Data;
        $this->DataGridRacquets->dataBind();
    }
    
    /* funzione che crea la gui per creare l'oggetto */
    public function createClicked()
    {   	    
    	$this->setViewState('racquet',null);    
    	$this->DataGridRacquets->SelectedItemIndex=-1;	
    	$this->editable->Visible = true;
    	$this->TypeEdit->Text = Prado::localize('Insert new racquet');
    	
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $brands = TblBrands::finder()->findAll($criteria);
        $this->DDLBrands->DataSource=$brands;
        $this->DDLBrands->dataBind();
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $patterns = TblRacquetsPattern::finder()->findAll($criteria);
        $this->DDLPatterns->DataSource=$patterns;
        $this->DDLPatterns->dataBind();
        
        $this->Model->Text = "";
        $this->HeadSize->Text = "0";
    	$this->Length->Text = "0";
    	$this->WeightUnstrung->Text = "0";
		$this->WeightStrung->Text = "0";
		$this->Balance->Text = "0";
		$this->Swingweight->Text = "0";
		$this->Stiffness->Text = "0";
		$this->BeamWidth->Text = "";
		$this->Note->Text = "";
        

    }
    
    /* funzione che edita/salva i dati l'oggetto */
    public function saveClicked()
    {   	
    	
    	if($this->getViewState('racquet',null) == null)
    		$this->racquet = new TblRacquets();
    	else
    		$this->racquet=$this->getViewState('racquet',null);
    		
    	$this->racquet->tbl_brands_id = $this->DDLBrands->SelectedValue;
    	$this->racquet->tbl_racquets_pattern_id = $this->DDLPatterns->SelectedValue;
    	$this->racquet->model = $this->Model->Text;
    	$this->racquet->head_size = $this->HeadSize->Text;
    	$this->racquet->length = $this->Length->Text;
    	$this->racquet->weight_unstrung = $this->WeightUnstrung->Text;
		$this->racquet->weight_strung = $this->WeightStrung->Text;
		$this->racquet->balance = $this->Balance->Text;
		$this->racquet->swingweight = $this->Swingweight->Text;
		$this->racquet->stiffness = $this->Stiffness->Text;
		$this->racquet->beam_width = $this->BeamWidth->Text;
		$this->racquet->note = $this->Note->Text;
		$this->racquet->date_modify = date('c');
    	$this->racquet->save();
    	
    	$this->editable->Visible = false;
    	$this->RefreshData();
		
		
        $this->DataGridRacquets->SelectedItemIndex=-1;
        $this->DataGridRacquets->DataSource=$this->Data;
        $this->DataGridRacquets->dataBind();
    	
    }
    
    public function changePage($sender,$param)
    {
    	$this->DataGridRacquets->SelectedItemIndex=-1;
        $this->DataGridRacquets->CurrentPageIndex=$param->NewPageIndex;
        $this->DataGridRacquets->DataSource=$this->Data;
        $this->DataGridRacquets->dataBind();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,Prado::localize('Page').': ');
    }
    
    public function onSearch($param){
		$this->DataGridRacquets->SelectedItemIndex=-1;
		$this->CreateArray($this->FilterCollection_brand->getCondition(), $this->FilterCollection_model->getCondition() );
		$this->editable->Visible = false;
		$this->DataGridRacquets->DataSource=$this->Data;
        $this->DataGridRacquets->dataBind();
	}
	
	public function onClear($param){
		$this->DataGridRacquets->SelectedItemIndex=-1;
		$this->FilterCollection_brand->clear();
		$this->FilterCollection_model->clear();
		$this->editable->Visible = false;
		$this->CreateArray('','');
		$this->DataGridRacquets->DataSource=$this->Data;
        $this->DataGridRacquets->dataBind();
	}
	
	public function onItemCommand($sender,$param)
	{
		switch ($param->getCommandName())
		{
			case "clonazione":
				$this->Clona($sender,$param);
				break;
		}
	}
	
	public function Clona($sender,$param)
	{
		$item = $param->Item;
		
		$this->setViewState('racquet',null);
		$this->DataGridRacquets->SelectedItemIndex=-1;
		$this->editable->Visible = true;
		$this->TypeEdit->Text = Prado::localize('Insert new racquet');
		
		$racquetClone = TblRacquets::finder()->findBy_id($param->Item->IDColumn->Text);
		 
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
		$brands = TblBrands::finder()->findAll($criteria);
		$this->DDLBrands->DataSource=$brands;
		$this->DDLBrands->dataBind();
		$this->DDLBrands->SelectedValue = $racquetClone->tbl_brands_id;
		
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
		$patterns = TblRacquetsPattern::finder()->findAll($criteria);
		$this->DDLPatterns->DataSource=$patterns;
		$this->DDLPatterns->dataBind();
		$this->DDLPatterns->SelectedValue = $racquetClone->tbl_racquets_pattern_id;
		
		$this->Model->Text = $racquetClone->model;
		$this->HeadSize->Text = $racquetClone->head_size;
		$this->Length->Text = $racquetClone->length;
		$this->WeightUnstrung->Text = $racquetClone->weight_unstrung;
		$this->WeightStrung->Text = $racquetClone->weight_strung;
		$this->Balance->Text = $racquetClone->balance;
		$this->Swingweight->Text = $racquetClone->swingweight;
		$this->Stiffness->Text = $racquetClone->stiffness;
		$this->BeamWidth->Text = $racquetClone->beam_width;
		$this->Note->Text = $racquetClone->note;
	}
}

