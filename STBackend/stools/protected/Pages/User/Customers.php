<?php

class Customers extends TPage
{
	private $_data=null;
	private $userEdit=null;
	
	protected function getData()
    {
        if($this->_data===null)
            $this->loadData();
        return $this->_data;
    }
    
    protected function CreateArray($name = '', $surname = ''){
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
	   	$this->_data = $sqlmap->queryForList("SelectCustomersByStringer",$this->User->UserDB->id);
        $this->saveData();
    }
 
    protected function saveData()
    {
        $this->setViewState('Data',$this->_data);
    }
	 
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	$this->Page->Title = Prado::localize('ManageCustomers');
    	$this->editable->Visible = false;
    	$this->Search->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->Cancel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->New->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/new-customer.gif';
		$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
		$this->Cancel_2->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->Change->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/send.gif';
        if(!$this->IsPostBack)
        {
            $this->DataGridCustomers->DataSource=$this->Data;
            $this->DataGridCustomers->dataBind();
        }
    }
    
    public function selectCustomer($sender,$param)
    {
    	
    	if($param->Item->IDColumn->Text == $this->User->UserDB->id){
    		$url=$this->Service->constructUrl('User.PersonalData');
        	$this->Response->redirect($url);
    	}else{
    		$this->userEdit = TblUsers::finder()->findBy_id($param->Item->IDColumn->Text);
    		$this->EDIT_CUSTOMER_DATA->Text = Prado::localize('Edita_Data_Customer') . " " . $this->userEdit->name . " " . $this->userEdit->surname;
    		$this->editable->Visible = true;
    		$this->Email->Text = $this->userEdit->email;
    		$this->Name->Text = $this->userEdit->name;
    		$this->Surname->Text = $this->userEdit->surname;
    		$this->Telephone->Text = $this->userEdit->telephone;
    		$this->MobileTelephone->Text = $this->userEdit->mobile_telephone;
    		$this->Fax->Text = $this->userEdit->fax;
    		$this->Cost->Text = $this->userEdit->cost;
    		$this->Piva->Text = $this->userEdit->piva;
    		$this->setViewState('userEdit',$this->userEdit);
    	}
    }
    
    public function cancelItem($sender,$param)
    {
        $this->DataGridCustomers->SelectedItemIndex=-1;
        $this->DataGridCustomers->DataSource=$this->Data;
        $this->DataGridCustomers->dataBind();
    }
    
    public function createClicked()
    {   	
    	$url=$this->Service->constructUrl('User.NewCustomer');
        	$this->Response->redirect($url);
    }
    
    public function saveClicked()
    {   	
    	$this->userEdit=$this->getViewState('userEdit',null);
		$this->userEdit->email = $this->Email->Text;
		$this->userEdit->name = $this->Name->Text;
		$this->userEdit->surname = $this->Surname->Text;
		$this->userEdit->telephone = $this->Telephone->Text;
		$this->userEdit->mobile_telephone = $this->MobileTelephone->Text;
		$this->userEdit->fax = $this->Fax->Text;
		$this->userEdit->cost = $this->Cost->Text;
		$this->userEdit->date_insert = date('c');
		$this->userEdit->piva = $this->Piva->Text;
		$this->userEdit->save();
		
    	$this->RefreshData();
		
		
        $this->DataGridCustomers->SelectedItemIndex=-1;
        $this->DataGridCustomers->DataSource=$this->Data;
        $this->DataGridCustomers->dataBind();
    	
    }
    
    /* funzione che nasconde la gui per editare/creare l'oggetto */
    public function cancelClicked()
    {
        $this->DataGridCustomers->SelectedItemIndex=-1;
        $this->DataGridCustomers->DataSource=$this->Data;
        $this->DataGridCustomers->dataBind();
    }
    
    public function changePage($sender,$param)
    {
        $this->DataGridCustomers->CurrentPageIndex=$param->NewPageIndex;
        $this->DataGridCustomers->DataSource=$this->Data;
        $this->DataGridCustomers->dataBind();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,Prado::localize('Page').': ');
    }
    
    public function onSearch($param){
		$this->DataGridCustomers->SelectedItemIndex=-1;
		$this->editable->Visible = false;
		$this->CreateArray($this->FilterCollection_name->getCondition(), $this->FilterCollection_surname->getCondition() );
		$this->DataGridCustomers->DataSource=$this->Data;
        $this->DataGridCustomers->dataBind();
	}
	
	public function onClear($param){
		$this->DataGridCustomers->SelectedItemIndex=-1;
		$this->editable->Visible = false;
		$this->FilterCollection_name->clear();
		$this->FilterCollection_surname->clear();
		$this->CreateArray('','');
		$this->DataGridCustomers->DataSource=$this->Data;
        $this->DataGridCustomers->dataBind();
	}
	
	protected function sortData($data,$key)
	{
		$compare = create_function('$a,$b','if ($a["'.$key.'"] == $b["'.$key.'"]) {return 0;}else {return ($a["'.$key.'"] > $b["'.$key.'"]) ? 1 : -1;}');
		usort($data,$compare) ;
		return $data ;
	}
	
	public function sortDataGrid($sender,$param)
	{
		$this->DataGridCustomers->DataSource=$this->sortData($this->Data,$param->SortExpression);
		$this->DataGridCustomers->dataBind();
	}
	
	public function changePageSize($sender,$param)
	{
		$this->DataGridCustomers->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->DataGridCustomers->CurrentPageIndex=0;
		$this->DataGridCustomers->DataSource=$this->Data;
		$this->DataGridCustomers->dataBind();
	}
}

