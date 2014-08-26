<?php
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
require_once 'tcpdf.php' ;
require_once 'PHPExcel.php';
require_once ('jpgraph.php');
require_once ('jpgraph_bar.php');

class ListCashMonth extends FunctionList
{
	private $_data=null;
	private $sort;
	
	 
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	$this->Page->Title = Prado::localize('ReportCashMonth');		
    	$this->Excel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/excel-64.png';	
		$this->Pdf->ImageUrl = $this->Page->Theme->BaseUrl.'/images/pdf-64.png';
		//$this->Excel->Visible = false;	
		$this->Pdf->Visible = false;		
		if(!$this->IsPostBack)
		{
        	$this->ShowList();
        }
        
        
        
    }
	
	public function ShowList(){
		$this->LBL_LIST->Text = Prado::localize('ReportCashMonth');
		$this->zone_list_jobs->Visible = true;
		$this->setUpFilter();
		$this->loadData();		
	}
	
	protected function setUpFilter()
	{
		$sqlmap = $this->Application->Modules['sqlmap']->Database;
    	$sqlmap->Active = true;
    	$sql = "select YEAR( date_stringing ) AS year FROM  tbl_stringing_jobs  WHERE tbl_users_id_stringer = ". $this->User->UserDB->id . " GROUP BY YEAR( date_stringing ) ORDER BY YEAR( date_stringing ) desc";
    	$command = $sqlmap->createCommand($sql);
    	$value = $command->query()->readAll();
    	
    	
    	$arrayYear = array();
    	$arrayYear[] = array('id'=>0,'year'=>Prado::localize('All'));
    	for($j=0;$j<count($value);$j++){
    		$arrayYear[] = array('id'=>$value[$j]['year'],'year'=>$value[$j]['year']);
    	}
    	$this->DDLYear->DataSource=$arrayYear;
    	$this->DDLYear->dataBind();
    	
    	$this->DDLMonth->DataSource=$this->getArrayForDDLMount($this->getApplication()->getGlobalization()->Culture);
    	$this->DDLMonth->dataBind();
	}
	
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
    
    public function selectionChangedDDLYear($sender,$param){
    	$this->CreateArray($this->getViewState('sort','') );
    	$this->DataGridList->DataSource=$this->Data;
    	$this->DataGridList->dataBind();
    }
    
    public function selectionChangedDDLMonth($sender,$param){
    	$this->CreateArray($this->getViewState('sort','') );
    	$this->DataGridList->DataSource=$this->Data;
    	$this->DataGridList->dataBind();
    }
    
    protected function CreateArray($order = '')
    {
    	
    	
    	
    	$sqlmap = $this->Application->Modules['sqlmap']->Database;
    	$sqlmap->Active = true;
    	$sql = "SELECT YEAR( date_stringing ) AS year, MONTH(date_stringing) AS monthNumber, DATE_FORMAT(date_stringing, '%M') AS month,  COUNT( * ) AS stringing, SUM( total_price ) AS amount FROM  tbl_stringing_jobs  WHERE tbl_users_id_stringer = ". $this->User->UserDB->id;

    	//filtri
    	$year = $this->DDLYear->SelectedValue;    	
    	if($year != "" && $year != 0){
    		$sql .= " and YEAR( date_stringing ) = " . $year;
    		
    	}
    	
    	$month = $this->DDLMonth->SelectedValue;
    	if($month != "" && $month != 0){
    		$sql .= " and MONTH( date_stringing ) = " . $month;
    	}    	
    	//endfiltri
    	
    	$sql .=  " GROUP BY YEAR( date_stringing ), MONTH(date_stringing) ";
    	
    	
    	
    	if($order == '' )
    		$sql .= " ORDER BY YEAR( date_stringing ) DESC, MONTH( date_stringing ) DESC";
    	else if($order == 'year')
    		$sql .= " ORDER BY YEAR( date_stringing ) ASC";
    	else if($order == 'year')
    		$sql .= " ORDER BY MONTH( date_stringing ) ASC";
    	else if($order == 'stringing')
    		$sql .= " ORDER BY stringing DESC";
    	else if($order == 'amount')
    		$sql .= " ORDER BY amount DESC";
    
    	$command = $sqlmap->createCommand($sql);
    	$arrayGlobale = $command->query()->readAll();
    	

    	for($j=0;$j<count($arrayGlobale);$j++){
    		
    		$arrayGlobale[$j]['month'] = $this->getMountName($this->getApplication()->getGlobalization()->Culture, $arrayGlobale[$j]['month']);  		
    		$incasso = $arrayGlobale[$j]["amount"];
    		$anno = $arrayGlobale[$j]["year"];
    		$mese = $arrayGlobale[$j]['monthNumber'];
    		
    		$sql = "SELECT SUM( rel_spese_stringer.valore_spesa ) AS totale FROM rel_spese_stringer where rel_spese_stringer.id_stringer = ". $this->User->UserDB->id . "  AND YEAR ( rel_spese_stringer.data ) = " . $anno . " AND MONTH ( rel_spese_stringer.data ) = " . $mese;
    		$command = $sqlmap->createCommand($sql);
    		$spesaMensile = 0;
    		$spese = $command->query()->readAll();
    		$spesaMensile = $spese[0]["totale"];
    		$saldo = 0;
    		$arrayGlobale[$j]["spese"] = number_format((float)$spesaMensile, 2, '.', '');
    		$arrayGlobale[$j]["saldo"] = number_format((float)$incasso - $spesaMensile, 2, '.', '');
    	
    	}
    	$this->_data = $arrayGlobale;
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
	
	
	
	public function loadStringingGraph(){
		
		
				
		
		$arrayMonth = $this->getArrayMountLabel($this->getApplication()->getGlobalization()->Culture);
		$arrayMonthShort = $this->getArrayMountLabelShort($this->getApplication()->getGlobalization()->Culture);
		
		
		$arrayYear = array();
		$arryColor = array("#cc1111", "#11cccc", "#1111cc");
		$dataStringing =array();

		$arrayPlot = array();
		
		
		for($j=0;$j<count($this->Data);$j++){
			$year = $this->Data[$j]['year'];
			if (in_array($year, $arrayYear)) {
			}else {
				$arrayYear[] = $year;
			}
		}
		for($j=0;$j<count($arrayYear);$j++){
			$yearFirst = $arrayYear[$j];

			$arrayValue = array();
			
			for($i=0;$i<count($this->Data);$i++){
				$yearSecond = $this->Data[$i]['year'];
				
				if($yearFirst == $yearSecond){
					//ok
					$monthFirst = $this->Data[$i]['month'];
					
					for($z=0;$z<count($arrayMonth);$z++){
						$monthSecondo = $arrayMonth[$z];
						if($monthFirst == $monthSecondo)
							$arrayValue[$z] = $monthFirst = $this->Data[$i]['stringing'];
						//else
						//	$arrayValue[] = 0;
					}
				}
			}
		for($z=0;$z<count($arrayMonth);$z++){
				if($arrayValue[$z] == null)
					$arrayValue[$z] = 0;
			}
			
			$plot = new BarPlot($arrayValue);
			$plot->value->Show();
			$plot->value->SetFormat('%01.0f');
			$plot->value->HideZero();
			$plot->SetColor("white");
			$plot->SetFillColor($arryColor[$j]);
			$plot->SetLegend($yearFirst);
			$arrayPlot[] = $plot;
		}
		


		// Create the graph. These two calls are always required
		$graph = new Graph(550,300,'auto');
		$graph->SetScale("textlin");
		$graph->graph_theme = null;
		$graph->SetFrame(false);
		

		$graph->xaxis->SetTickLabels($arrayMonthShort);

		
		$gbplot = new GroupBarPlot($arrayPlot);
		 
		$graph->Add($gbplot);
		
		
		
		
		$graph->title->Set(Prado::localize('Stringing'));
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','#00A78A');
		$graph->legend->SetPos(0.5,0.99,'center','bottom');
		
		// Display the graph
		$stringingFileName = "stringing". $this->User->UserDB->id .".png";
		$this->Session['stringing_graph'] = $stringingFileName;
		$graph->Stroke($this->Application->Parameters["PATH_CUSTOM_IMAGES"].$stringingFileName);
	}
	
	public function getStringingPathGrahFile(){
		return $this->Application->Parameters["PATH_CUSTOM_IMAGES"].
												$this->Session['stringing_graph'];
	}
	
	public function loadAmountGraph(){
		$arrayMonth = $this->getArrayMountLabel($this->getApplication()->getGlobalization()->Culture);
		$arrayMonthShort = $this->getArrayMountLabelShort($this->getApplication()->getGlobalization()->Culture);
		
		
		$arrayYear = array();
		$arryColor = array("#cc1111", "#11cccc", "#1111cc");
		$dataStringing =array();

		$arrayPlot = array();
		
		
		for($j=0;$j<count($this->Data);$j++){
			$year = $this->Data[$j]['year'];
			if (in_array($year, $arrayYear)) {
			}else {
				$arrayYear[] = $year;
			}
		}
		for($j=0;$j<count($arrayYear);$j++){
			$yearFirst = $arrayYear[$j];

			$arrayValue = array();
			
			for($i=0;$i<count($this->Data);$i++){
				$yearSecond = $this->Data[$i]['year'];
				
				if($yearFirst == $yearSecond){
					//ok
					$monthFirst = $this->Data[$i]['month'];
					
					for($z=0;$z<count($arrayMonth);$z++){
						$monthSecondo = $arrayMonth[$z];
						if($monthFirst == $monthSecondo)
							$arrayValue[$z] = $monthFirst = $this->Data[$i]['amount'];
						//else
						//	$arrayValue[] = 0;
					}
				}
			}
			for($z=0;$z<count($arrayMonth);$z++){
				if($arrayValue[$z] == null)
					$arrayValue[$z] = 0;
			}
			
			$plot = new BarPlot($arrayValue);
			$plot->value->Show();
			$plot->value->SetFormat('%01.0f');
			$plot->value->HideZero();
			$plot->SetColor("white");
			$plot->SetFillColor($arryColor[$j]);
			$plot->SetLegend($yearFirst);
			$arrayPlot[] = $plot;
		}
		


		// Create the graph. These two calls are always required
		$graph = new Graph(550,300,'auto');
		$graph->SetScale("textlin");
		$graph->graph_theme = null;
		$graph->SetFrame(false);
		
		$graph->xaxis->SetTickLabels($arrayMonthShort);

		
		$gbplot = new GroupBarPlot($arrayPlot);
		 
		$graph->Add($gbplot);
		
		
		$graph->title->Set(Prado::localize('amount_graph'));
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','#00A78A');
		$graph->legend->SetPos(0.5,0.99,'center','bottom');
		
		// Display the graph
		$graph->title->Set(Prado::localize('Amounts'));
		
		$amountFileName = "amount". $this->User->UserDB->id .".png";
		$this->Session['amount_graph'] = $amountFileName;
		$graph->Stroke($this->Application->Parameters["PATH_CUSTOM_IMAGES"].$amountFileName);
	}

	public function getAmountPathGrahFile(){
		return $this->Application->Parameters["PATH_CUSTOM_IMAGES"].
												$this->Session['amount_graph'];
	}
	
	public function loadSpeseGraph(){
		$arrayMonth = $this->getArrayMountLabel($this->getApplication()->getGlobalization()->Culture);
		$arrayMonthShort = $this->getArrayMountLabelShort($this->getApplication()->getGlobalization()->Culture);
	
	
		$arrayYear = array();
		$arryColor = array("#cc1111", "#11cccc", "#1111cc");
		$dataStringing =array();
	
		$arrayPlot = array();
	
	
		for($j=0;$j<count($this->Data);$j++){
			$year = $this->Data[$j]['year'];
			if (in_array($year, $arrayYear)) {
			}else {
				$arrayYear[] = $year;
			}
		}
		for($j=0;$j<count($arrayYear);$j++){
			$yearFirst = $arrayYear[$j];
	
			$arrayValue = array();
				
			for($i=0;$i<count($this->Data);$i++){
				$yearSecond = $this->Data[$i]['year'];
	
				if($yearFirst == $yearSecond){
					//ok
					$monthFirst = $this->Data[$i]['month'];
						
					for($z=0;$z<count($arrayMonth);$z++){
						$monthSecondo = $arrayMonth[$z];
						if($monthFirst == $monthSecondo)
							$arrayValue[$z] = $monthFirst = $this->Data[$i]['spese'];
						//else
						//	$arrayValue[] = 0;
					}
				}
			}
			for($z=0;$z<count($arrayMonth);$z++){
				if($arrayValue[$z] == null)
					$arrayValue[$z] = 0;
			}
				
			$plot = new BarPlot($arrayValue);
			$plot->value->Show();
			$plot->value->SetFormat('%01.0f');
			$plot->value->HideZero();
			$plot->SetColor("white");
			$plot->SetFillColor($arryColor[$j]);
			$plot->SetLegend($yearFirst);
			$arrayPlot[] = $plot;
		}
	
	
	
		// Create the graph. These two calls are always required
		$graph = new Graph(550,300,'auto');
		$graph->SetScale("textlin");
		$graph->graph_theme = null;
		$graph->SetFrame(false);
	
		$graph->xaxis->SetTickLabels($arrayMonthShort);
	
	
		$gbplot = new GroupBarPlot($arrayPlot);
			
		$graph->Add($gbplot);
	
	
		$graph->title->Set(Prado::localize('spese_graph'));
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','#00A78A');
		$graph->legend->SetPos(0.5,0.99,'center','bottom');
	
		// Display the graph
		$graph->title->Set(Prado::localize('Spese'));
	
		$speseFileName = "spese". $this->User->UserDB->id .".png";
		$this->Session['spese_graph'] = $speseFileName;
		$graph->Stroke($this->Application->Parameters["PATH_CUSTOM_IMAGES"].$speseFileName);
	}
	
	public function getSpesePathGrahFile(){
		return $this->Application->Parameters["PATH_CUSTOM_IMAGES"].
		$this->Session['spese_graph'];
	}
	
	public function loadSaldoGraph(){
		$arrayMonth = $this->getArrayMountLabel($this->getApplication()->getGlobalization()->Culture);
		$arrayMonthShort = $this->getArrayMountLabelShort($this->getApplication()->getGlobalization()->Culture);
	
	
		$arrayYear = array();
		$arryColor = array("#cc1111", "#11cccc", "#1111cc");
		$dataStringing =array();
	
		$arrayPlot = array();
	
	
		for($j=0;$j<count($this->Data);$j++){
			$year = $this->Data[$j]['year'];
			if (in_array($year, $arrayYear)) {
			}else {
				$arrayYear[] = $year;
			}
		}
		for($j=0;$j<count($arrayYear);$j++){
			$yearFirst = $arrayYear[$j];
	
			$arrayValue = array();
	
			for($i=0;$i<count($this->Data);$i++){
				$yearSecond = $this->Data[$i]['year'];
	
				if($yearFirst == $yearSecond){
					//ok
					$monthFirst = $this->Data[$i]['month'];
	
					for($z=0;$z<count($arrayMonth);$z++){
						$monthSecondo = $arrayMonth[$z];
						if($monthFirst == $monthSecondo)
							$arrayValue[$z] = $monthFirst = $this->Data[$i]['saldo'];
						//else
						//	$arrayValue[] = 0;
					}
				}
			}
			for($z=0;$z<count($arrayMonth);$z++){
				if($arrayValue[$z] == null)
					$arrayValue[$z] = 0;
			}
	
			$plot = new BarPlot($arrayValue);
			$plot->value->Show();
			$plot->value->SetFormat('%01.0f');
			$plot->value->HideZero();
			$plot->SetColor("white");
			$plot->SetFillColor($arryColor[$j]);
			$plot->SetLegend($yearFirst);
			$arrayPlot[] = $plot;
		}
	
	
	
		// Create the graph. These two calls are always required
		$graph = new Graph(550,300,'auto');
		$graph->SetScale("textlin");
		$graph->graph_theme = null;
		$graph->SetFrame(false);
	
		$graph->xaxis->SetTickLabels($arrayMonthShort);
	
	
		$gbplot = new GroupBarPlot($arrayPlot);
			
		$graph->Add($gbplot);
	
	
		$graph->title->Set(Prado::localize('saldo_graph'));
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','#00A78A');
		$graph->legend->SetPos(0.5,0.99,'center','bottom');
	
		// Display the graph
		$graph->title->Set(Prado::localize('Saldo'));
	
		$saldoFileName = "saldo". $this->User->UserDB->id .".png";
		$this->Session['saldo_graph'] = $saldoFileName;
		$graph->Stroke($this->Application->Parameters["PATH_CUSTOM_IMAGES"].$saldoFileName);
	}
	
	public function getSaldoPathGrahFile(){
		return $this->Application->Parameters["PATH_CUSTOM_IMAGES"].
		$this->Session['saldo_graph'];
	}
	

	
	
	
	public function exportExcel()
	{				
		$objPHPExcel = new PHPExcel();
		$row = 1;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, Prado::localize('Year'));
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, Prado::localize('Stringing'));
		$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, Prado::localize('Stringing'));
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, Prado::localize('Amount'));
		$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, Prado::localize('Spese'));
		$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, Prado::localize('Saldo'));
		$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
		$row++;
		$this->CreateArray($this->getViewState('sort','') );
		for($j=0;$j<count($this->_data);$j++){
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->_data[$j]["year"]);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->_data[$j]["month"]);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $this->_data[$j]["stringing"]);			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $this->_data[$j]["amount"]);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $this->_data[$j]["spese"]);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $this->_data[$j]["saldo"]);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
			
			$row++;
		}		
		
		$objPHPExcel->getActiveSheet()->setTitle(Prado::localize('ListCashMonth'));
		
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
	
	public function exportPdf()
	{
		
	}
}