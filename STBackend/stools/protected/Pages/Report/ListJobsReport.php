<?php
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
require_once 'tcpdf.php' ;
require_once 'PHPExcel.php';

class ListJobsReport extends FunctionList
{
	private $_data=null;
	private $userSelect=null;
	private $_data_jobs_customer=null;
	private $sort;
	
	 
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	$this->Page->Title = Prado::localize('ListJobs');
		$this->SearchRacquet->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->CancelRacquet->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';	
		$this->Excel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/excel-64.png';	
		$this->Pdf->ImageUrl = $this->Page->Theme->BaseUrl.'/images/pdf-64.png';
		$this->Excel->Visible = false;	
		$this->Pdf->Visible = false;		
		if(!$this->IsPostBack)
		{
        	$this->ShowListJobs();
        }
        
        
        
    }
	
	public function ShowListJobs(){
		$this->LBL_LIST_JOB->Text = Prado::localize('List_Jobs_Customer');
		$this->zone_list_jobs->Visible = true;
		$this->loadDataJobsCustomer();
		
		$this->PDFJob->Visible = true;
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
    
    protected function CreateArrayJobsCustomer($order = '')
    {
    	/*$param = array();
	    $param['id'] = $this->User->UserDB->id;
	    $param['serial'] = "%".$serial."%";
	    $param['brand'] = "%".$brand."%";
	    $param['model'] = "%".$model."%";
    	
		$sqlmap = Prado::getApplication()->Modules['sqlmap']->Client;
		if($order == 'date')
	    	$this->_data_jobs_customer = $sqlmap->queryForList("SelectTblStringingJobsByStringerOrderDate", $param);
		else if($order == 'name')
	    	$this->_data_jobs_customer = $sqlmap->queryForList("SelectTblStringingJobsByStringerOrderName", $param);
		else if($order == 'surname')
	    	$this->_data_jobs_customer = $sqlmap->queryForList("SelectTblStringingJobsByStringerOrderSurname", $param);
		else
			$this->_data_jobs_customer = $sqlmap->queryForList("SelectTblStringingJobsByStringer", $param);

    	foreach($this->_data_jobs_customer as $row){           	
        	
        	
        	$row->user_racquet = TblRacquetsUser::finder()->findBy_id($row->tbl_racquets_user_id);
        	$row->user_racquet->customer = TblUsers::finder()->findBy_id($row->user_racquet->tbl_users_id);
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
        }*/
    	
    	$sqlmap = $this->Application->Modules['sqlmap']->Database;
    	$sqlmap->Active = true;
    	
    	$sql = "SELECT
			tbl_stringing_jobs.id,
			tbl_stringing_jobs.date_stringing,
			concat(tbl_users.name, ' ', tbl_users.surname) as customer,
			concat(tbl_brands.description, ' ', tbl_racquets.model) as racquet,
			concat(BSM.description, ' ', SM.model, ' ', tbl_stringing_jobs.weight_main, ' ', tbl_weight_unit.description) as string_main,
			concat(BSC.description, ' ', SC.model, ' ', tbl_stringing_jobs.wieght_cross, ' ', tbl_weight_unit.description) as string_cross,
			tbl_stringing_jobs.dynamic_tension
			FROM
			tbl_stringing_jobs
			INNER JOIN tbl_racquets_user ON tbl_stringing_jobs.tbl_racquets_user_id = tbl_racquets_user.id
			INNER JOIN tbl_users ON tbl_racquets_user.tbl_users_id = tbl_users.id
			INNER JOIN tbl_racquets ON tbl_racquets_user.tbl_racquets_id = tbl_racquets.id
			INNER JOIN tbl_brands ON tbl_racquets.tbl_brands_id = tbl_brands.id
			
			INNER JOIN tbl_strings SM ON tbl_stringing_jobs.tbl_strings_id_main = SM.id 
			INNER JOIN tbl_brands BSM ON SM.tbl_brands_id = BSM.id 
			
			INNER JOIN tbl_strings SC ON tbl_stringing_jobs.tbl_strings_id_main = SC.id 
			INNER JOIN tbl_brands BSC ON SC.tbl_brands_id = BSC.id 
			
			INNER JOIN tbl_users STRINGER ON tbl_stringing_jobs.tbl_users_id_stringer = STRINGER.id 
			INNER JOIN tbl_weight_unit ON tbl_weight_unit.id = STRINGER.tbl_weight_unit_id 
			
			where tbl_stringing_jobs.tbl_users_id_stringer = ".$this->User->UserDB->id;
    	
    	//filtri
    	$sql .= " and concat(tbl_users.name, ' ', tbl_users.surname) like '%".$this->filter_customer->Text."%' ";
    	$sql .= " and concat(tbl_brands.description, ' ', tbl_racquets.model) like '%".$this->filter_Racquet->Text."%' ";
    	
    	if($order == '')
    		$sql .= " order by tbl_stringing_jobs.date_stringing DESC";
    	else if($order == 'date')
    		$sql .= " order by tbl_stringing_jobs.date_stringing DESC";
    	else if($order == 'customer')
    		$sql .= " order by customer DESC, tbl_stringing_jobs.date_stringing DESC";
    	else if($order == 'racquet')
    		$sql .= " order by racquet DESC, tbl_stringing_jobs.date_stringing DESC";
    	
    	$command = $sqlmap->createCommand($sql);
    	$this->_data_jobs_customer = $command->query()->readAll();
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
        $this->CreateArrayJobsCustomer($this->getViewState('sort','') );
        $this->DataGridListJobs->DataSource=$this->DataJobsCustomer;
        $this->DataGridListJobs->dataBind();
    }
 
    public function pagerCreatedJob($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,'Page: ');
    }
	
	public function onSearchRacquet($param){
		$this->CreateArrayJobsCustomer($this->getViewState('sort',''));
			
		$this->DataGridListJobs->DataSource=$this->DataJobsCustomer;
        $this->DataGridListJobs->dataBind();
	}
	
	public function onClearRacquet($param){
		$this->filter_customer->Text = "";
		$this->filter_Racquet->Text = "";
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
		$this->CreateArrayJobsCustomer($this->sort  );
		$this->DataGridListJobs->DataSource=$this->DataJobsCustomer;
        $this->DataGridListJobs->dataBind();
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
	}
	
	public function exportPdf()
	{
		
	}
	
	
	/*** zone zona lista job customer ***/
}