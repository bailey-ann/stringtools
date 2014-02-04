<?php

class Brands extends TPage
{
	private $_data=null;
	
	protected function getData()
    {
        if($this->_data===null)
            $this->loadData();
        return $this->_data;
    }
    
	public function itemCreated($sender, $param) {
		$item = $param->Item;
		if($this->User->UserDB->type_user_id == 4)
			$param->Item->Cells['1']->Visible = false;
		else
			$param->Item->Cells['1']->Visible = true;
    }
    
    protected function loadData()
    {
        if(($this->_data=$this->getViewState('Data',null))===null)
        {
        	$criteria = new TActiveRecordCriteria;
			$criteria->OrdersBy['description'] = 'asc';
            $this->_data= TblBrands::finder()->findAll($criteria);
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
        $criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $this->_data= TblBrands::finder()->findAll($criteria);
        $this->saveData();
    }
 
    protected function saveData()
    {
        $this->setViewState('Data',$this->_data);
    }
	 
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	$this->Page->Title = Prado::localize('ManageBrand');
    	$this->editable->Visible = false;
		$this->New->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/new-brand.gif';
		$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
		$this->Cancel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
        if(!$this->IsPostBack)
        {
            $this->DataGridBrands->DataSource=$this->Data;
            $this->DataGridBrands->dataBind();
        }
    }
    
    public function editItem($sender,$param)
    {
        $this->DataGridBrands->EditItemIndex=$param->Item->ItemIndex;
        $this->DataGridBrands->DataSource=$this->Data;
        $this->DataGridBrands->dataBind();
    }
    
    public function cancelItem($sender,$param)
    {
        $this->DataGridBrands->EditItemIndex=-1;
        $this->DataGridBrands->DataSource=$this->Data;
        $this->DataGridBrands->dataBind();
    }
    
    public function saveItem($sender,$param)
    {
        $item=$param->Item;
        
        $id = $this->DataGridBrands->DataKeys[$item->ItemIndex];        
        $brand = TblBrands::finder()->findBy_id($id);
        $brand->description = $item->BrandNameColumn->TextBox->Text; 
        $brand->save();
        
        $this->RefreshData();
		
		
        $this->DataGridBrands->EditItemIndex=-1;
        $this->DataGridBrands->DataSource=$this->Data;
        $this->DataGridBrands->dataBind();
    }
    
    public function createClicked()
    {   	
    	$this->BrandName->Text = "";
    	$this->editable->Visible = true;
    }
    
    public function saveClicked()
    {   	
    	$brand = new TblBrands();
    	$brand->description = $this->BrandName->Text;
    	$brand->save();
    	
    	$this->editable->Visible = false;
    	$this->RefreshData();
		
		
        $this->DataGridBrands->EditItemIndex=-1;
        $this->DataGridBrands->DataSource=$this->Data;
        $this->DataGridBrands->dataBind();
    	
    }
    
    public function cancelClicked()
    {   	
    	
    }
    
    public function changePage($sender,$param)
    {
        $this->DataGridBrands->CurrentPageIndex=$param->NewPageIndex;
        $this->DataGridBrands->DataSource=$this->Data;
        $this->DataGridBrands->dataBind();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,Prado::localize('Page').': ');
    }
}

