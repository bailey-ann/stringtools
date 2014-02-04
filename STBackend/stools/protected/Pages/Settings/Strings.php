<?php

class Strings extends TPage
{
	private $_data = null;
	private $string =null;
	
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
        $this->_data= TblStrings::finder()->findAll($criteria);
        $brand_name = array();
        $model = array();
        $i = 0;
        foreach($this->_data as $row){           	
        	$brand = TblBrands::finder()->findBy_id($row->tbl_brands_id);
        	$row->brand_name = $brand->description;
        	$brand_name[$i] = $row->brand_name;
		    $model[$i] = $row->model;
		    $i++;
		    
		    $gauge = TblGauges::finder()->findBy_id($row->tbl_gauges_id);
		    $row->gauge_desc = $gauge->usa . " (" . $gauge->diameter.")";
		    
		    $type = TblStringType::finder()->findBy_id($row->tbl_string_type_id);
		    $row->string_type = $type->description;
		    
		    $row->price = 0;
		    
		    $price = RelStringPrice::finder()->findBy_id_stringer($this->User->UserDB->id);
		    if($price != null)
		    	$row->price = $price->price;
        }
    	array_multisort($brand_name,SORT_ASC,SORT_STRING, $model,SORT_ASC,SORT_STRING, $this->_data);
    }
    
    protected function loadData()
    {
        //if(($this->_data=$this->getViewState('Data',null))===null)
        //{
        	$this->CreateArray($this->FilterCollection_brand->getCondition(), $this->FilterCollection_model->getCondition());
            $this->saveData();
        //}
        
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
    	$this->Page->Title = Prado::localize('ManageStrings');
    	$this->editable->Visible = false;
		$this->Search->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->Cancel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->New->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/new-string.gif';
		$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
		$this->Cancel_2->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->Help->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/help.gif';
        if(!$this->IsPostBack)
        {
        	$this->DataGridStrings->SelectedItemIndex=-1;
            $this->DataGridStrings->DataSource=$this->Data;
            $this->DataGridStrings->dataBind();
        }
    }
    
    /* funzione che crea la gui per editare l'oggetto */
    public function selectString($sender,$param)
    {
    	$this->editable->Visible = true;
    	$this->TypeEdit->Text =  Prado::localize('Edit string');
    	$this->string = TblStrings::finder()->findBy_id($param->Item->IDColumn->Text);
    	
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $brands = TblBrands::finder()->findAll($criteria);
        $this->DDLBrands->DataSource=$brands;
        $this->DDLBrands->dataBind();
        $this->DDLBrands->SelectedValue = $this->string->tbl_brands_id;
        
        $this->Model->Text = $this->string->model;
        
        $this->Code->Text = $this->string->code;
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['usa'] = 'asc';
        $gauges = TblGauges::finder()->findAll($criteria);
        $gaugesArray = array();
        foreach($gauges as $row){ 
        	$gaugesArray[$row->id] = $row->usa . " (" . $row->diameter.")";
        }
        $this->DDLGauges->DataSource=$gaugesArray;
        $this->DDLGauges->dataBind();
        $this->DDLGauges->SelectedValue = $this->string->tbl_gauges_id;
        
        $this->ExactGauge->Text = $this->string->exact_gauge;
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $type = TblStringType::finder()->findAll($criteria);
        $this->DDLType->DataSource=$type;
        $this->DDLType->dataBind();
        $this->DDLType->SelectedValue = $this->string->tbl_string_type_id;       
        
        $this->Price->Text = "0";
        $price = RelStringPrice::finder()->find('id_stringer = ? AND id_strings = ?', $this->User->UserDB->id, $this->string->id);
    	if($price != null){
    		$this->Price->Text = $price->price;
    	}
        
        $this->setViewState('string',$this->string);
    }
    
    /* funzione che nasconde la gui per editare/creare l'oggetto */
    public function cancelClicked()
    {
        $this->DataGridStrings->SelectedItemIndex=-1;
        $this->DataGridStrings->DataSource=$this->Data;
        $this->DataGridStrings->dataBind();
    }
    
    /* funzione che crea la gui per creare l'oggetto */
    public function createClicked()
    {   	
    	$this->setViewState('string',null);    
    	$this->DataGridStrings->SelectedItemIndex=-1;	
    	$this->editable->Visible = true;
    	$this->TypeEdit->Text = Prado::localize('Insert new string');
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $brands = TblBrands::finder()->findAll($criteria);
        $this->DDLBrands->DataSource=$brands;
        $this->DDLBrands->dataBind();
        
        $this->Model->Text = "";
        
        $this->Code->Text = "";
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['usa'] = 'asc';
        $gauges = TblGauges::finder()->findAll($criteria);
        $gaugesArray = array();
        foreach($gauges as $row){ 
        	$gaugesArray[$row->id] = $row->usa . " (" . $row->diameter.")";
        }
        $this->DDLGauges->DataSource=$gaugesArray;
        $this->DDLGauges->dataBind();
        
        $this->ExactGauge->Text = "0";
        
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $type = TblStringType::finder()->findAll($criteria);
        $this->DDLType->DataSource=$type;
        $this->DDLType->dataBind();
        
        $this->Price->Text = "0";

    }
    
    /* funzione che edita/salva i dati l'oggetto */
    public function saveClicked()
    {   	
    	
    	if($this->getViewState('string',null) == null)
    		$this->string = new TblStrings();
    	else
    		$this->string=$this->getViewState('string',null);
    		
    	$this->string->tbl_brands_id = $this->DDLBrands->SelectedValue;
    	$this->string->tbl_gauges_id = $this->DDLGauges->SelectedValue;
    	$this->string->tbl_string_type_id = $this->DDLType->SelectedValue;
    	$this->string->model = $this->Model->Text;
    	$this->string->code = $this->Code->Text;
    	$this->string->exact_gauge = $this->ExactGauge->Text;

    	$this->string->save();
    	
    	$price = RelStringPrice::finder()->find('id_stringer = ? AND id_strings = ?', $this->User->UserDB->id, $this->string->id);
    	if($price == null){
    		$price = new RelStringPrice();
    		$price->id_stringer = $this->User->UserDB->id;
    		$price->id_strings = $this->string->id;
    	}
    	$price->price = $this->Price->Text;
    	$price->save();	
    	
    	$this->editable->Visible = false;
    	$this->RefreshData();
		
		
        $this->DataGridStrings->SelectedItemIndex=-1;
        $this->DataGridStrings->DataSource=$this->Data;
        $this->DataGridStrings->dataBind();
    	
    }
    
    public function changePage($sender,$param)
    {
    	$this->DataGridStrings->SelectedItemIndex=-1;
        $this->DataGridStrings->CurrentPageIndex=$param->NewPageIndex;
        $this->DataGridStrings->DataSource=$this->Data;
        $this->DataGridStrings->dataBind();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,Prado::localize('Page').': ');
    }
    
    public function onSearch($param){
		$this->DataGridStrings->SelectedItemIndex=-1;
		$this->CreateArray($this->FilterCollection_brand->getCondition(), $this->FilterCollection_model->getCondition() );
		$this->editable->Visible = false;
		$this->DataGridStrings->DataSource=$this->Data;
        $this->DataGridStrings->dataBind();
	}
	
	public function onClear($param){
		$this->DataGridStrings->SelectedItemIndex=-1;
		$this->FilterCollection_brand->clear();
		$this->FilterCollection_model->clear();
		$this->editable->Visible = false;
		$this->CreateArray('','');
		$this->DataGridStrings->DataSource=$this->Data;
        $this->DataGridStrings->dataBind();
	}
}

