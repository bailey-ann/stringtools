<?php
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
require_once('tcpdf.php');
//require_once 'PHPExcel.php';

class ListJobs extends FunctionList
{
	private $_data=null;
	private $userSelect=null;
	private $_data_jobs_customer=null;
	private $sort;
	
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
    	$this->Page->Title = Prado::localize('ListJobs');
		$this->Search->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->Cancel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->SearchRacquet->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->CancelRacquet->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';		
		$this->btnAddJob->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/add_job.gif';
		$this->Change->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/send.gif';
        if(!$this->IsPostBack)
        {
        	$this->zone_list_jobs->Visible = false;
        	$this->zone_label->Visible = false;
        	$this->btnAddJob->Visible = false;
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
    
    public function changePage($sender,$param)
    {
        $this->DataGridCustomers->CurrentPageIndex=$param->NewPageIndex;
        $this->CreateArray($this->FilterCollection_name->getCondition(),
        		$this->FilterCollection_surname->getCondition() ,$this->getViewState('sort',''));
        $this->DataGridCustomers->DataSource=$this->Data;
        $this->DataGridCustomers->dataBind();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,'Page: ');
    }
    
    public function onSearch($param){
		$this->CreateArray($this->FilterCollection_name->getCondition(), $this->FilterCollection_surname->getCondition() );
		$this->DataGridCustomers->DataSource=$this->Data;
        $this->DataGridCustomers->dataBind();
	}
	
	public function onClear($param){
		$this->FilterCollection_name->clear();
		$this->FilterCollection_surname->clear();
		$this->CreateArray('','');
		$this->DataGridCustomers->DataSource=$this->Data;
        $this->DataGridCustomers->dataBind();
	}
	
	protected function sortData($data,$key)
	{
		$compare = create_function('$a,$b','if ($a["'.$key.'"] == $b["'.$key.'"]) {return 0;}else {return ($a["'.$key.'"] > $b["'.$key.'"]) ? 1 : -1;}');
		//usort($data,"") ;
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
	
	public function changePageSize($sender,$param)
	{
		$this->DataGridCustomers->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->DataGridCustomers->CurrentPageIndex=0;
		$this->CreateArray($this->FilterCollection_name->getCondition(),
				$this->FilterCollection_surname->getCondition() ,$this->getViewState('sort',''));
		$this->DataGridCustomers->DataSource=$this->Data;
		$this->DataGridCustomers->dataBind();
	}
	
	public function selectCustomer($sender, $param){
		$this->setViewState('userSelect',null);
		$item = $param->Item;		
		$this->userSelect = TblUsers::finder()->findBy_id($param->Item->IDColumn->Text);
		$userRacquet = TblRacquetsUser::finder()->findAll("tbl_users_id = ? and active = 1", $this->userSelect->id);
		$this->HideListJob();
		
		if(count($userRacquet)>0)
			$this->ShowListJobs();
		else
			$this->ShowNoRacquet();
		
	}
	
	public function HideListJob(){
		$this->zone_label->Visible = false;
		$this->zone_list_jobs->Visible = false;
	}
	
	public function ShowNoRacquet(){	
		$this->zone_label->Visible = true;
		$this->NO_USER_RACQUET->Text = Prado::localize('NO_USER_RACQUET');
	}
	
	public function selectBackCustomer($id){	
		$this->userSelect = TblUsers::finder()->findBy_id($id);		
		$this->ShowListJobs();
	}
	
	public function ShowListJobs(){
		$this->LBL_LIST_JOB->Text = Prado::localize('List_Jobs_Customer') . " " . $this->userSelect->name . " " . $this->userSelect->surname;		
		$this->setViewState('userSelect',$this->userSelect);
		$this->zone_list_jobs->Visible = true;
		$this->btnAddJob->Visible = true;
		$this->loadDataJobsCustomer();
		
		//if($this->User->UserDB->id == 6)
			$this->PDFJob->Visible = true;
		//else
		//	$this->PDFJob->Visible = false;
	}
	
	
	
	/*** inizio zona lista job customer ***/
	
	protected function getDataJobsCustomer()
    {
        if($this->_data_jobs_customer===null)
            $this->loadDataJobsCustomer();
        return $this->_data_jobs_customer;
    }
    
    protected function loadDataJobsCustomer()
    {
        $this->CreateArrayJobsCustomer();
        $this->saveDataJobsCustomer();
        $this->DataGridListJobs->SelectedItemIndex=-1;
        $this->DataGridListJobs->DataSource=$this->DataJobsCustomer;
        $this->DataGridListJobs->dataBind();
    }
    
    protected function saveDataJobsCustomer()
    {
        $this->setViewState('DataJobsCustomer',$this->_data_jobs_customer);
    }
    
    protected function CreateArrayJobsCustomer($brand = '', $model = '', $serial = '')
    {
    	$param = array();
	    $param['id'] = $this->getViewState('userSelect',null)->id;
	    $param['serial'] = "%".$serial."%";
	    $param['brand'] = "%".$brand."%";
	    $param['model'] = "%".$model."%";
    	
		$sqlmap = Prado::getApplication()->Modules['sqlmap']->Client;
	    $this->_data_jobs_customer = $sqlmap->queryForList("SelectTblStringingJobs", $param);

    	foreach($this->_data_jobs_customer as $row){           	
        	
        	$row->customer = $this->getViewState('userSelect',null);
        	$row->user_racquet = TblRacquetsUser::finder()->findBy_id($row->tbl_racquets_user_id);
        	$row->user_racquet->racquet = TblRacquets::finder()->findBy_id($row->user_racquet->tbl_racquets_id);
        	$brand = TblBrands::finder()->findBy_id($row->user_racquet->racquet->tbl_brands_id);
        	$row->user_racquet->racquet->brand_name = $brand->description;
        	$row->stringer = $this->User->UserDB;
        	$row->stringing_machines = TblStringingMachines::finder()->findBy_id($row->tbl_stringing_machines_id);
        	$row->main_string = TblStrings::finder()->findBy_id($row->tbl_strings_id_main);
        	$row->cross_string = TblStrings::finder()->findBy_id($row->tbl_strings_id_cross);
        	$row->stringing_type = TblStringingJobType::finder()->findBy_id($row->tbl_stringing_type_id);
        	$row->grip = TblGrips::finder()->findBy_id($row->tbl_grip_id);
        	$row->tbl_overgrip_id = TblOvergrips::finder()->findBy_id($row->tbl_overgrip_id);
        }
    }
    
    public function addJob($param){
		$user = $this->getViewState('userSelect',null);
		$this->Response->redirect($this->Service->constructUrl('Job.ManageJob', array('idUser'=>$user->id), false));
	}
	
	public function selectJob($sender, $param){
		$item = $param->Item;	
		$this->Response->redirect($this->Service->constructUrl('Job.ManageJob', array('idJob'=>$param->Item->IDJobColumn->Text), false));
	}
	
	public function changePageJob($sender,$param)
    {
        $this->DataGridListJobs->CurrentPageIndex=$param->NewPageIndex;
        $this->DataGridListJobs->DataSource=$this->DataJobsCustomer;
        $this->DataGridListJobs->dataBind();
    }
 
    public function pagerCreatedJob($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,'Page: ');
    }
	
	public function onSearchRacquet($param){
		$this->CreateArrayJobsCustomer($this->FilterCollection_brand->getNoFieldCondition(),
			$this->FilterCollection_model->getNoFieldCondition(), 
			$this->FilterCollection_serial->getNoFieldCondition() );
			
		$this->DataGridListJobs->DataSource=$this->DataJobsCustomer;
        $this->DataGridListJobs->dataBind();
	}
	
	public function onClearRacquet($param){
		$this->FilterCollection_brand->clear();
		$this->FilterCollection_model->clear();
		$this->FilterCollection_serial->clear();
		$this->CreateArrayJobsCustomer();
		$this->DataGridListJobs->DataSource=$this->DataJobsCustomer;
        $this->DataGridListJobs->dataBind();
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
	
	public function Clona($sender,$param)
	{
		$item = $param->Item;	
		$this->Response->redirect($this->Service->constructUrl('Job.ManageJob', array('idCloneJob'=>$param->Item->IDJobColumn->Text), false));
	}
	
	public function MakePDF($sender,$param)
	{
		$stringJob = $this->formatJob($param->Item->IDJobColumn->Text);
		$job = TblStringingJobs::finder()->findBy_id($param->Item->IDJobColumn->Text);
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor("www.stringtools.it");
		$pdf->SetTitle($stringJob);
		
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		// set font
		$pdf->SetFont('times', '', 13);
		
		// add a page
		$pdf->AddPage();
		
		//immagine utente
		$urlJpg = 'themes/White/images/logo/'.$this->User->UserDB->id.".jpg";
		//$urlPng = 'themes/White/images/logo/'.$this->User->UserDB->id.".png";
		if (file_exists($urlJpg)) 
			$pdf->Image($urlJpg,10,6,40,15,'JPG','www.stringtools.it','', true, 150, '', false, false, 0);
		else
			$pdf->Image('themes/White/images/logo-st-www.jpg',10,6,40,15,'JPG','www.stringtools.it','', true, 150, '', false, false, 0);
		//$pdf->Image('themes/White/images/logo-st-www.jpg',10,6,40, '', '', 'http://www.tcpdf.org', '', false, 300);
			
		$pdf->Ln(15);
			
		$pdf->Cell(0, 0, $this->User->UserDB->surname . " " . $this->User->UserDB->name, 0, 1, 'L', 0, '', 0);
		if($this->User->UserDB->telephone != "")
			$pdf->Cell(0, 0, $this->User->UserDB->telephone, 0, 1, 'L', 0, '', 1);
		if($this->User->UserDB->mobile_telephone != "")
			$pdf->Cell(0, 0, $this->User->UserDB->mobile_telephone, 0, 1, 'L', 0, '', 1);
		if($this->User->UserDB->email != "")
			$pdf->Cell(0, 0, $this->User->UserDB->email, 0, 1, 'L', 0, '', 1);
			
		$pdf->Ln(10);
		
		$pdf->Cell(0, 0, date("d-m-Y") , 0, 1, 'R', 0, '', 1);
		
		$pdf->Ln(10);
		
		$pdf->SetFont('times', '', 16);
		$pdf->Cell(0, 0, Prado::localize('CLAIM_CHECK'), 0, 1, 'C', 0, '', 1);
		
		$pdf->Ln(10);
		
		$pdf->SetFont('times', '', 13);
		$pdf->writeHTML($this->makeHtmlJob($job), true, false, true, false, '');
		
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="'.$stringJob.'.pdf"');
		$pdf->Output($stringJob.'.pdf', 'D');

		/*$pdf = new FPDF(); // Creo nuova classe
		$pdf->SetAuthor("www.stringtools.it"); // L'autore del documento
		$pdf->AddPage(); // Aggiunge una pagina default
		
		//immagine utente
		if (file_exists('themes/White/images/logo/'.$this->User->UserDB->id.".jpg")) 
			$pdf->Image('themes/White/images/logo/'.$this->User->UserDB->id.".jpg",10,6,40);
		else
			$pdf->Image('themes/White/images/logo-st-www.jpg',10,6,40);
		
		//dati utente
		$y = 40;
		
		$pdf->SetFont('Arial','',12); // Set del font arial grassetto 12px
		
		$pdf->Text(10,$y,$this->User->UserDB->surname . " " . $this->User->UserDB->name);
		$y  = $y + 5;
		
		if($this->User->UserDB->telephone != ""){
			$pdf->Text(10,$y,$this->User->UserDB->telephone);
			$y  = $y + 5;
		}
		
		if($this->User->UserDB->mobile_telephone != ""){
			$pdf->Text(10,$y,$this->User->UserDB->mobile_telephone);
			$y  = $y + 5;
		}
		
		if($this->User->UserDB->email != ""){
			$pdf->Text(10,$y,$this->User->UserDB->email);
			$y  = $y + 5;
		}
		
		$y  = $y + 5;
		
		$pdf->Text(10,$y,Prado::localize('CLAIM_CHECK'));
		
		
		$pdf->Output($this->formatJob($param->Item->IDJobColumn->Text).'.pdf','D');*/
	}
	
	public function exportExcel()
	{					
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
									 ->setLastModifiedBy("Maarten Balliauw")
									 ->setTitle("Office 2007 XLSX Test Document")
									 ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test result file");
		
		
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Hello')
		            ->setCellValue('A2', 'GIGI!')
		            ->setCellValue('B2', 'world!')
		            ->setCellValue('C1', 'Hello')
		            ->setCellValue('D2', 'world!');
		
		// Miscellaneous glyphs, UTF-8
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A4', 'Miscellaneous glyphs')
		            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="01simple.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	
	
	
	/*** zone zona lista job customer ***/
}