<?php

class Overgrips extends TPage
{
	private $_data = null;
	private $overgrip =null;
	
	protected function getData()
    {
        if($this->_data===null)
            $this->loadData();
        return $this->_data;
    }
    
    protected function CreateArray(){
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['model'] = 'asc';
        $this->_data= TblOvergrips::finder()->findAll($criteria);
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
    	$this->Page->Title = Prado::localize('ManageOvergrips');
    	$this->editable->Visible = false;
		$this->New->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/new-overgrip.gif';
		$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
		$this->Cancel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
        if(!$this->IsPostBack)
        {
        	$this->DataGridOvergrips->SelectedItemIndex=-1;
            $this->DataGridOvergrips->DataSource=$this->Data;
            $this->DataGridOvergrips->dataBind();
        }
    }
    
    /* funzione che crea la gui per editare l'oggetto */
    public function selectOvergrip($sender,$param)
    {
    	$this->editable->Visible = true;
    	$this->TypeEdit->Text = Prado::localize('Edit Overgrip');
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $brands = TblBrands::finder()->findAll($criteria);
        $this->DDLBrands->DataSource=$brands;
        $this->DDLBrands->dataBind();
        $this->overgrip = TblOvergrips::finder()->findBy_id($param->Item->IDColumn->Text);
        $this->DDLBrands->SelectedValue = $this->overgrip->tbl_brands_id;
        $this->Model->Text = $this->overgrip->model;
        $this->Price->Text = $this->overgrip->price;
        $this->Note->Text = $this->overgrip->note;
        $this->setViewState('overgrip',$this->overgrip);
    }
    
    /* funzione che nasconde la gui per editare/creare l'oggetto */
    public function cancelClicked()
    {
        $this->DataGridOvergrips->SelectedItemIndex=-1;
        $this->DataGridOvergrips->DataSource=$this->Data;
        $this->DataGridOvergrips->dataBind();
    }
    
    /* funzione che crea la gui per creare l'oggetto */
    public function createClicked()
    {   	    
    	$this->setViewState('overgrip',null); 
    	$this->DataGridOvergrips->SelectedItemIndex=-1;	
    	$this->editable->Visible = true;
    	$this->TypeEdit->Text = Prado::localize('Insert New Overgrip');
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $brands = TblBrands::finder()->findAll($criteria);
        $this->DDLBrands->DataSource=$brands;
        $this->DDLBrands->dataBind();
        $this->Model->Text = "";
        $this->Price->Text = "0";
        $this->Note->Text = "";
    }
    
    /* funzione che edita/salva i dati l'oggetto */
    public function saveClicked()
    {   	
    	
    	if($this->getViewState('overgrip',null) == null)
    		$this->overgrip = new TblOvergrips();
    	else
    		$this->overgrip=$this->getViewState('overgrip',null);
    	$this->overgrip->tbl_brands_id = $this->DDLBrands->SelectedValue;
    	$this->overgrip->model = $this->Model->Text;
    	$this->overgrip->price = $this->Price->Text;
    	$this->overgrip->note = $this->Note->Text;
    	$this->overgrip->save();
    	
    	$this->editable->Visible = false;
    	$this->RefreshData();
		
		
        $this->DataGridOvergrips->SelectedItemIndex=-1;
        $this->DataGridOvergrips->DataSource=$this->Data;
        $this->DataGridOvergrips->dataBind();
    	
    }
    
    public function changePage($sender,$param)
    {
    	$this->DataGridOvergrips->SelectedItemIndex=-1;
        $this->DataGridOvergrips->CurrentPageIndex=$param->NewPageIndex;
        $this->DataGridOvergrips->DataSource=$this->Data;
        $this->DataGridOvergrips->dataBind();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,Prado::localize('Page').': ');
    }
}

