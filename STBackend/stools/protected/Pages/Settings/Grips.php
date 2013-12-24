<?php

class Grips extends TPage
{
	private $_data = null;
	private $grip =null;
	
	protected function getData()
    {
        if($this->_data===null)
            $this->loadData();
        return $this->_data;
    }
    
    protected function CreateArray(){
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['model'] = 'asc';
        $this->_data= TblGrips::finder()->findAll($criteria);
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
    	$this->Page->Title = Prado::localize('ManageGrips');
    	$this->editable->Visible = false;
		$this->New->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/new-grip.gif';
		$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
		$this->Cancel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
        if(!$this->IsPostBack)
        {
        	$this->DataGridGrips->SelectedItemIndex=-1;
            $this->DataGridGrips->DataSource=$this->Data;
            $this->DataGridGrips->dataBind();
        }
    }
    
    /* funzione che crea la gui per editare l'oggetto */
    public function selectGrip($sender,$param)
    {
    	$this->editable->Visible = true;
    	$this->TypeEdit->Text = Prado::localize('Edit Grip');
    	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['description'] = 'asc';
        $brands = TblBrands::finder()->findAll($criteria);
        $this->DDLBrands->DataSource=$brands;
        $this->DDLBrands->dataBind();
        $this->grip = TblGrips::finder()->findBy_id($param->Item->IDColumn->Text);
        $this->DDLBrands->SelectedValue = $this->grip->tbl_brands_id;
        $this->Model->Text = $this->grip->model;
        $this->Price->Text = $this->grip->price;
        $this->Note->Text = $this->grip->note;
        $this->setViewState('grip',$this->grip);
    }
    
    /* funzione che nasconde la gui per editare/creare l'oggetto */
    public function cancelClicked()
    {
        $this->DataGridGrips->SelectedItemIndex=-1;
        $this->DataGridGrips->DataSource=$this->Data;
        $this->DataGridGrips->dataBind();
    }
    
    /* funzione che crea la gui per creare l'oggetto */
    public function createClicked()
    {   	
    	$this->setViewState('grip',null); 
    	$this->DataGridGrips->SelectedItemIndex=-1;    	
    	$this->editable->Visible = true;
    	$this->TypeEdit->Text = Prado::localize('Insert new grip');
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
    	
    	if($this->getViewState('grip',null) == null)
    		$this->grip = new TblGrips();
    	else
    		$this->grip=$this->getViewState('grip',null);
    	$this->grip->tbl_brands_id = $this->DDLBrands->SelectedValue;
    	$this->grip->model = $this->Model->Text;
    	$this->grip->price = $this->Price->Text;
    	$this->grip->note = $this->Note->Text;

    	$this->grip->save();
    	
    	$this->editable->Visible = false;
    	$this->RefreshData();
		
		
        $this->DataGridGrips->SelectedItemIndex=-1;
        $this->DataGridGrips->DataSource=$this->Data;
        $this->DataGridGrips->dataBind();
    	
    }
    
    public function changePage($sender,$param)
    {
    	$this->DataGridGrips->SelectedItemIndex=-1;
        $this->DataGridGrips->CurrentPageIndex=$param->NewPageIndex;
        $this->DataGridGrips->DataSource=$this->Data;
        $this->DataGridGrips->dataBind();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,Prado::localize('Page').': ');
    }
}

