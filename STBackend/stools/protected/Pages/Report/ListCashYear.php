<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//require_once('tcpdf.php');
//require_once 'Writer.php';

class ListCashYear extends FunctionList
{
	private $_data=null;
	private $sort;
	
	 
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	$this->Page->Title = Prado::localize('ListCashYear');		
    	$this->Excel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/excel-64.png';	
		$this->Pdf->ImageUrl = $this->Page->Theme->BaseUrl.'/images/pdf-64.png';
		$this->Excel->Visible = false;	
		$this->Pdf->Visible = false;		
		if(!$this->IsPostBack)
		{
        	$this->ShowList();
        }
        
        
        
    }
	
	public function ShowList(){
		$this->LBL_LIST->Text = Prado::localize('ListCashYear');
		$this->zone_list_jobs->Visible = true;
		$this->loadData();		
	}
	
	
	
	/*** inizio zona lista job customer ***/
	
	protected function getData()
    {
        if($this->_data===null)
            $this->loadData();
        return $this->_data;
    }
    
    protected function loadData()
    {
        $this->CreateArray();
        $this->saveData();
        $this->DataGridList->SelectedItemIndex=-1;
        $this->DataGridList->DataSource=$this->Data;
        $this->DataGridList->dataBind();
    }
    
    protected function saveData()
    {
        $this->setViewState('Data',$this->_data);
    }
    
    protected function CreateArray($order = '')
    {
    	
    	$sqlmap = $this->Application->Modules['sqlmap']->Database;
    	$sqlmap->Active = true;
    	$sql = "SELECT YEAR( date_stringing ) AS year, COUNT( * ) AS stringing, SUM( total_price ) AS amount FROM  tbl_stringing_jobs  WHERE tbl_users_id_stringer = ". $this->User->UserDB->id . " GROUP BY YEAR( date_stringing ) ";

    	if($order == '' )
    		$sql .= " ORDER BY YEAR( date_stringing ) DESC";
    	else if($order == 'anno')
    		$sql .= " ORDER BY YEAR( date_stringing ) ASC";
    	else if($order == 'stringing')
    		$sql .= " ORDER BY stringing DESC";
    	else if($order == 'amount')
    		$sql .= " ORDER BY amount DESC";
    
    	$command = $sqlmap->createCommand($sql);
    	$this->_data = $command->query()->readAll();
    }
	
	public function changePage($sender,$param)
    {
        $this->DataGridList->CurrentPageIndex=$param->NewPageIndex;
        $this->CreateArray($this->getViewState('sort','') );
        $this->DataGridList->DataSource=$this->Data;
        $this->DataGridList->dataBind();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,'Page: ');
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
	
	
	public function sortDataGrid($sender,$param)
	{
		$this->sort = $param->SortExpression;
		$this->setViewState('sort',$this->sort);
		$this->CreateArray($this->sort  );
		$this->DataGridList->DataSource=$this->Data;
        $this->DataGridList->dataBind();
	}

	
	
	
	public function exportExcel()
	{					
	}
	
	public function exportPdf()
	{
		
	}
}