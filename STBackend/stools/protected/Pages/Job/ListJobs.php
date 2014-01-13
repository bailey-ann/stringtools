<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
require_once('tcpdf.php');
require_once 'PHPExcel.php';

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
        $this->Excel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/excel-64.png';
		if(!$this->IsPostBack)
        {
        	$this->zone_list_jobs->Visible = false;
        	$this->zone_label->Visible = false;
        	$this->btnAddJob->Visible = false;
        	$this->Excel->Visible = false;
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
		$this->Excel->Visible = true;
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
	}
	
	public function exportExcel()
	{				
		$objPHPExcel = new PHPExcel();
		$row = 1;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, Prado::localize('JobID'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, Prado::localize('Date Stringing'));	
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, Prado::localize('Customer'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, Prado::localize('Racquet'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, Prado::localize('SerialRacquet'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, Prado::localize('Stringer'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, Prado::localize('StringingMachine'));
		
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, Prado::localize('StringMains'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, Prado::localize('Tension'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, Prado::localize('Prestretch'));
		
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, Prado::localize('StringCross'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, Prado::localize('Tension'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $row, Prado::localize('Prestretch'));
		
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $row, Prado::localize('StringingType'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $row, Prado::localize('DynamicTension'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $row, Prado::localize('Stencyl'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $row, Prado::localize('GrommetsGuard'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $row, Prado::localize('Grips'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $row, Prado::localize('Overgrips'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $row, Prado::localize('TotalPrice'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $row, Prado::localize('NoteStringing'));
		$objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->getFont()->setBold(true);
		
		$this->CreateArrayJobsCustomer($this->FilterCollection_brand->getNoFieldCondition(),
			$this->FilterCollection_model->getNoFieldCondition(), 
			$this->FilterCollection_serial->getNoFieldCondition() );
			
		$row = $row + 2;
		
			
		for($j=0;$j<count($this->DataJobsCustomer);$j++){
			
			$job = $this->DataJobsCustomer[$j];
			$stringJob = $this->formatJob($job->id);
		
			$racquetCustomer = TblRacquetsUser::finder()->findBy_id($job->tbl_racquets_user_id);
			
			$customer = TblUsers::finder()->findBy_id($racquetCustomer->tbl_users_id);
			
			$racquetModel = TblRacquets::finder()->findBy_id($racquetCustomer->tbl_racquets_id);
			$brandRacquet = TblBrands::finder()->findBy_id($racquetModel->tbl_brands_id);
			
			$stringingMachine = TblStringingMachines::finder()->findBy_id($job->tbl_stringing_machines_id);
			$brandStringingMachine = TblBrands::finder()->findBy_id($stringingMachine->tbl_brands_id);
			
			$mainString = TblStrings::finder()->findBy_id($job->tbl_strings_id_main);
			$brandMainString = TblBrands::finder()->findBy_id($mainString->tbl_brands_id);
			$gaugeMainString = TblGauges::finder()->findBy_id($mainString->tbl_gauges_id);
			//$row->gauge_desc = $gauge->usa . " (" . $gauge->diameter.")";
			
			$crossString = TblStrings::finder()->findBy_id($job->tbl_strings_id_cross);
			$brandCrossString = TblBrands::finder()->findBy_id($crossString->tbl_brands_id);
			$gaugeCrossString = TblGauges::finder()->findBy_id($crossString->tbl_gauges_id);
			
			$stringingJobType = TblStringingJobType::finder()->findBy_id($job->tbl_stringing_type_id);
			
			$stencyl = Prado::localize('No');
			if($job->stencyl == 1)
				$stencyl = Prado::localize('Yes');
				
			$grommet = Prado::localize('No');
			if($job->grommets_guard == 1)
				$grommet = Prado::localize('Yes');
							
			$gripString = Prado::localize('No');
			if($job->tbl_grip_id != 0){
				$grip = TblGrips::finder()->findBy_id($job->tbl_grip_id);
				$brand = TblBrands::finder()->findBy_id($grip->tbl_brands_id);
				$gripString = $brand->description . " " . $grip->model;
			}
					
			$overgripString = Prado::localize('No');
			if($job->tbl_overgrip_id != null){
				$overgrip = TblOvergrips::finder()->findBy_id($job->tbl_overgrip_id->id);
				$brand = TblBrands::finder()->findBy_id($overgrip->tbl_brands_id);
				$overgripString = $brand->description . " " . $overgrip->model;
			}
			
			$y = substr($job->date_stringing, 0, 4);
			$m = substr($job->date_stringing, 5, 2);
			$d = substr($job->date_stringing, 8, 2);
			$dateString = $d . "-".$m."-".$y;
				
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $stringJob);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $dateString);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $customer->name . ' ' . $customer->surname);			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $brandRacquet->description. ' ' . $racquetModel->model);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $racquetCustomer->serial);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $this->User->UserDB->name . ' ' . $this->User->UserDB->surname);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $brandStringingMachine->description . ' ' . $stringingMachine->model);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $brandMainString->description . ' ' . $mainString->model);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $job->weight_main. ' ' . $this->User->UserDB->weight_unit->description);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $job->prestretch_main.' %');
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $brandCrossString->description . ' ' . $crossString->model);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $job->wieght_cross . ' ' . $this->User->UserDB->weight_unit->description);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $job->prestretch_cross.' %');
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $stringingJobType->description);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $job->dynamic_tension);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $stencyl);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $grommet);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $gripString);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $overgripString);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $job->total_price);
			//$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $job->note);
			
			$row++;
		}
		
		$objPHPExcel->getActiveSheet()->setTitle(Prado::localize('CLAIM_CHECK'));
		
		$objPHPExcel->setActiveSheetIndex(0);
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="export.xls"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');		
		header ('Expires: Mon, 26 Jul 2030 05:00:00 GMT');
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate');
		header ('Pragma: public'); 
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	
	
	
	/*** zone zona lista job customer ***/
}