<?php
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
require_once 'tcpdf.php' ;
require_once 'PHPExcel.php';

class ListStringsUsrsa extends FunctionList
{
	private $_data=null;
	private $userSelect=null;
	private $_data_customer=null;
	private $sort;
	
	 
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	$this->Page->Title = Prado::localize('ListStringsUsrsa');
		$this->SearchRacquet->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->CancelRacquet->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';			
		
		if(!$this->IsPostBack)
		{
        	$this->ShowList();
        	$this->editable->visible = false;
        	
        	$criteria = new TActiveRecordCriteria;
        	$criteria->OrdersBy['material'] = 'asc';
        	$brands = ViewMaterialUsrsa::finder()->findAll($criteria);
        	$viewMaterialUsrsa = new ViewMaterialUsrsa();
        	$viewMaterialUsrsa->material = "Nessuno";
        	array_push($brands, $viewMaterialUsrsa);
        	$this->filter_Racquet->SelectedValue = "Nessuno";
        	$this->filter_Racquet->DataSource=$brands;
        	$this->filter_Racquet->dataBind();
        	
        }else{
        }

    }
	
	public function ShowList(){
		$this->LBL_LIST_JOB->Text = Prado::localize('List_Strings_Usrsa');
		$this->zone_list_jobs->Visible = true;
		$this->loadDataCustomer();
	}
	
	
	
	/*** inizio zona lista customer ***/
	
	protected function getDataCustomer()
    {
        if($this->_data_customer===null)
            $this->loadDataCustomer();
        return $this->_data_customer;
    }
    
    protected function loadDataCustomer()
    {
        $this->CreateArrayCustomer();
        $this->saveDataCustomer();
        $this->DataGridList->SelectedItemIndex=-1;
        $this->DataGridList->DataSource=$this->DataCustomer;
        $this->DataGridList->dataBind();
    }
    
    protected function saveDataCustomer()
    {
    	$this->setViewState('DataCustomer',$this->_data_customer);
    }
    
    protected function CreateArrayCustomer($order = '')
    {
    	
    	$sqlmap = $this->Application->Modules['sqlmap']->Database;
    	$sqlmap->Active = true;
    	
    	$sql = "SELECT " .
			"view_tbl_strings_usrsa.id, " .
			"view_tbl_strings_usrsa.`name`, " .
			"view_tbl_strings_usrsa.material, " .
			"view_tbl_strings_usrsa.gauge, " .
			"view_tbl_strings_usrsa.stiffness_lbin, " .
			"view_tbl_strings_usrsa.stiffness_nm, " .
			"view_tbl_strings_usrsa.tension_loss_lbs, " .
			"view_tbl_strings_usrsa.tension_loss_kg, " .
			"view_tbl_strings_usrsa.anno " .
			"FROM " .
			"view_tbl_strings_usrsa";
    	
    	//filtri
    	
    	$sql .= " where view_tbl_strings_usrsa.`name` like '%".$this->filter_name->Text."%' ";
    	if("Nessuno" != $this->filter_Racquet->Text)
    		$sql .= " and view_tbl_strings_usrsa.material like '%".$this->filter_Racquet->Text."%' ";
    	
    	if($order == '')
    		$sql .= " order by view_tbl_strings_usrsa.`name` ASC";
    	else if($order == 'name')
    		$sql .= " order by view_tbl_strings_usrsa.`name` DESC";   	
    	else if($order == 'material')
    		$sql .= " order by view_tbl_strings_usrsa.material DESC";
    	
    	$command = $sqlmap->createCommand($sql);
    	$this->_data_customer = $command->query()->readAll();
    }  
	
	public function select($sender, $param){
		$this->editable->visible = true;
		$item = $param->Item;	
		$string = ViewTblStringsUsrsa::finder()->findBy_id($param->Item->IDColumn->Text);
		$this->Name->Text = $string->name;
		$this->Material->Text = $string->material;
		$this->Gauge->Text = $string->gauge;
		$this->Stiffness_lbin->Text = $string->stiffness_lbin;
		$this->Stiffness_nm->Text = $string->stiffness_nm;
		$this->Tension_loss_lbs->Text = $string->tension_loss_lbs;
		$this->Tension_loss_kg->Text = $string->tension_loss_kg;
	}
	
	public function changePage($sender,$param)
    {
        $this->DataGridList->CurrentPageIndex=$param->NewPageIndex;
        $this->CreateArrayCustomer($this->getViewState('sort','') );
        $this->DataGridList->DataSource=$this->DataCustomer;
        $this->DataGridList->dataBind();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,'Page: ');
    }
	
	public function onSearchRacquet($param){
		$this->editable->visible = false;
		$this->CreateArrayCustomer($this->getViewState('sort',''));	
		$this->DataGridList->SelectedItemIndex=-1;
		$this->DataGridList->DataSource=$this->DataCustomer;
        $this->DataGridList->dataBind();
	}
	
	public function onClearRacquet($param){
		$this->editable->visible = false;
		$this->filter_name->Text = "";
		$this->filter_Racquet->SelectedValue = "Nessuno";
		$this->CreateArrayCustomer();
		$this->DataGridList->SelectedItemIndex=-1;
		$this->DataGridList->DataSource=$this->DataCustomer;
        $this->DataGridList->dataBind();
	}
	
	public function onItemCommand($sender,$param)
	{
		switch ($param->getCommandName())
		{
			case "clonazione":
				$this->Clona($sender,$param);
				break;
			case "pdf":
				$this->MakePDF($sender,$param);
				break;
		}
	}
	
	protected function sortData($data,$key)
	{
		$compare = create_function('$a,$b','if ($a["'.$key.'"] == $b["'.$key.'"]) {return 0;}else {return ($a["'.$key.'"] > $b["'.$key.'"]) ? 1 : -1;}');
		usort($data,"") ;
		return $data ;
	}
	
	public function sortDataGrid($sender,$param)
	{
		$this->sort = $param->SortExpression;
		$this->setViewState('sort',$this->sort);
		$this->CreateArrayCustomer($this->sort  );		
		$this->DataGridList->DataSource=$this->DataCustomer;
        $this->DataGridList->dataBind();
	}
}