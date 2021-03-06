<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
require_once 'tcpdf.php' ;
require_once 'PHPExcel.php';
require_once ('jpgraph.php');
require_once ('jpgraph_bar.php');

class ListCashYear extends FunctionList
{
	private $_data=null;
	private $sort;
	
	 
	public function onLoad($param)
    {
    	parent::onLoad($param);
    	$this->Page->Title = Prado::localize('ReportCashYear');		
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
		$this->LBL_LIST->Text = Prado::localize('ReportCashYear');
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
    	$arrayGlobale = $command->query()->readAll();
    	
    	for($j=0;$j<count($arrayGlobale);$j++){
    		$incasso = $arrayGlobale[$j]["amount"];
    		$anno = $arrayGlobale[$j]["year"];
    		
    		$sql = "SELECT SUM( rel_spese_stringer.valore_spesa ) AS totale FROM rel_spese_stringer where rel_spese_stringer.id_stringer = ". $this->User->UserDB->id . "  AND YEAR ( rel_spese_stringer.data ) = " . $anno;
    		$command = $sqlmap->createCommand($sql);
    		$spesaAnno = 0;
    		$spese = $command->query()->readAll();
    		$spesaAnno = $spese[0]["totale"];
    		$saldo = 0;
    		$arrayGlobale[$j]["spese"] = number_format((float)$spesaAnno, 2, '.', '');
    		$arrayGlobale[$j]["saldo"] = number_format((float)$incasso - $spesaAnno, 2, '.', '');
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
		
		$maxValue = 0;
		
		$dataStringing =array();
		$label = array();
		for($j=0;$j<count($this->Data);$j++){
			$value = $this->Data[$j]['stringing'];
			if($value > $maxValue)
				$maxValue = $value;
			$dataStringing[] = $value;
			$label[] = $this->Data[$j]['year'];
		}
		


		// Create the graph. These two calls are always required
		$graph = new Graph(350,220,'auto');
		$graph->SetScale("textlin");
		$graph->graph_theme = null;
		$graph->SetFrame(false);
		

		$graph->xaxis->SetTickLabels($label);
		
		// Create the bar plots
		$b1plot = new BarPlot($dataStringing);
		
		//$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
		
		// ...and add it to the graPH
		$b1plot->value->Show();
		$b1plot->value->SetFormat('%01.0f'); 
		$b1plot->value->SetColor("black","darkred"); 
		$graph->Add($b1plot);
		
		
		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#cc1111");
		
		$graph->title->Set(Prado::localize('Stringing'));
		//$graph->yscale->SetAutoTicks(); 
		//$graph->yscale->SetAutoMax($maxValue+3);
		
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
		$dataAmount =array();
		$label = array();
		for($j=0;$j<count($this->Data);$j++){
			$dataAmount[] = $this->Data[$j]['amount'];
			$label[] = $this->Data[$j]['year'];
		}
		


		// Create the graph. These two calls are always required
		$graph = new Graph(350,220,'auto');
		$graph->SetScale("textlin");
		$graph->graph_theme = null;
		$graph->SetFrame(false);

		$graph->xaxis->SetTickLabels($label);
		
		// Create the bar plots
		$b2plot = new BarPlot($dataAmount);
		
		//$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
		
		// ...and add it to the graPH
		$b2plot->value->Show();
		$b2plot->value->SetFormat('%01.2f'); 
		//$b2plot->value->SetFont(FS_BOLD); 
		$b2plot->value->SetColor("black","darkred"); 
		$graph->Add($b2plot);
		
		$b2plot->SetColor("white");
		$b2plot->SetFillColor("#11cccc");

		
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
		$dataAmount =array();
		$label = array();
		for($j=0;$j<count($this->Data);$j++){
			$dataAmount[] = $this->Data[$j]['spese'];
			$label[] = $this->Data[$j]['year'];
		}
	
	
	
		// Create the graph. These two calls are always required
		$graph = new Graph(350,220,'auto');
		$graph->SetScale("textlin");
		$graph->graph_theme = null;
		$graph->SetFrame(false);
	
		$graph->xaxis->SetTickLabels($label);
	
		// Create the bar plots
		$b2plot = new BarPlot($dataAmount);
	
		//$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
	
		// ...and add it to the graPH
		$b2plot->value->Show();
		$b2plot->value->SetFormat('%01.2f');
		//$b2plot->value->SetFont(FS_BOLD);
		$b2plot->value->SetColor("black","darkred");
		$graph->Add($b2plot);
	
		$b2plot->SetColor("white");
		$b2plot->SetFillColor("#116fcc");
	
	
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
		$dataAmount =array();
		$label = array();
		for($j=0;$j<count($this->Data);$j++){
			$dataAmount[] = $this->Data[$j]['saldo'];
			$label[] = $this->Data[$j]['year'];
		}
	
	
	
		// Create the graph. These two calls are always required
		$graph = new Graph(350,220,'auto');
		$graph->SetScale("textlin");
		$graph->graph_theme = null;
		$graph->SetFrame(false);
	
		$graph->xaxis->SetTickLabels($label);
	
		// Create the bar plots
		$b2plot = new BarPlot($dataAmount);
	
		//$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
	
		// ...and add it to the graPH
		$b2plot->value->Show();
		$b2plot->value->SetFormat('%01.2f');
		//$b2plot->value->SetFont(FS_BOLD);
		$b2plot->value->SetColor("black","darkred");
		$graph->Add($b2plot);
	
		$b2plot->SetColor("white");
		$b2plot->SetFillColor("#cccc11");
	
	
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
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, Prado::localize('Amount'));
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, Prado::localize('Spese'));
		$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, Prado::localize('Saldo'));
		$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
		$row++;
		$this->CreateArray($this->getViewState('sort','') );
		for($j=0;$j<count($this->_data);$j++){
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->_data[$j]["year"]);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->_data[$j]["stringing"]);			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $this->_data[$j]["amount"]);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $this->_data[$j]["spese"]);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $this->_data[$j]["saldo"]);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
			$row++;
		}		
		
		$objPHPExcel->getActiveSheet()->setTitle(Prado::localize('ListCashYear'));
		
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