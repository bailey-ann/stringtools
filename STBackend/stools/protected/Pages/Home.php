<?php

//require_once('fpdf.php');

class Home extends FunctionList
{
	//public $DataType;
	public function onLoad($param)
    {
    	
    	if($this->User->Active == 1){
    		$this->reg_ok->Visible = false;
    		$this->content->Visible = true;
    		
    		$sqlmap = Prado::getApplication()->Modules['sqlmap']->Client;
    		
    		$numberCustomers = 0;    		
	   		$arraNumberCustomers = $sqlmap->queryForList("CountListCustomersByStringer",$this->User->UserDB->id);
	   		if(count($arraNumberCustomers) > 0)
	   			$numberCustomers = $arraNumberCustomers[0];	   		
	   		$this->COUNT_LIST_CUSTOMERS->Text = $numberCustomers;
	   		
	   		$numberJobMonth = 0;
	   		$arraNumberJobMonth = $sqlmap->queryForList("CountListJobByStringerMonth",$this->User->UserDB->id);
	   		if(count($arraNumberJobMonth) > 0)
	   			$numberJobMonth = $arraNumberJobMonth[0];
	   		$this->COUNT_LIST_STRINGING_MONTH->Text = $numberJobMonth;
	   		
	   		$numberJob = 0;
	   		$arraNumberJob = $sqlmap->queryForList("CountListJobByStringer",$this->User->UserDB->id);
	   		if(count($arraNumberJob) > 0)
	   			$numberJob = $arraNumberJob[0];	   		
	   		$this->COUNT_LIST_STRINGING->Text = $numberJob;
	   		
	   		$lastDate = "--";
	   		$arraLastDate= $sqlmap->queryForList("GetLastDateStringing",$this->User->UserDB->id);
	   		if(count($arraLastDate) > 0)
	   			$lastDate = $arraLastDate[0];	 

	   		if($lastDate != "--"){
	   			$date = new DateTime($lastDate);  		
	   			$this->DATA_LAST_STRINGING->Text = $date->format('d/m/Y');
	   		}
	   		
	   		
	   		$criteria = array();
	   		$criteria['id'] = $this->User->UserDB->id;
	   		$criteria['serial'] = "%%";
	   		$criteria['brand'] = "%%";
	   		$criteria['model'] = "%%";
	   		$listJob = $sqlmap->queryForList("SelectTblStringingJobsForStringer",$criteria);
	   		$listJobMonth = $sqlmap->queryForList("SelectTblStringingJobsForStringerMonth",$criteria);
	   		
	   		$DataTypeString= $this->GetListOfTypeStringUsed($listJobMonth);
	   		$this->RepeaterStringTypeMouth->DataSource=$DataTypeString;
	   		$this->RepeaterStringTypeMouth->dataBind();
	   		
	   		$DataString= $this->GetListOfStringUsed($listJobMonth);
	   		$this->RepeaterStringMouth->DataSource=$DataString;
	   		$this->RepeaterStringMouth->dataBind();
	   		
	   		
	   		
	   		$DataTypeString= $this->GetListOfTypeStringUsed($listJob);
	   		$this->RepeaterStringType->DataSource=$DataTypeString;
            $this->RepeaterStringType->dataBind();    
            
            $DataString= $this->GetListOfStringUsed($listJob);
            $this->RepeaterString->DataSource=$DataString;
            $this->RepeaterString->dataBind(); 
    	}else{
    		$this->reg_ok->Visible = true;
    		$this->content->Visible = false;
    	}
    }
    
    public function openPDF(){
		$pdf=new FPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',12); // Set del font arial grassetto 12px
		$pdf->Text(10,80,'Cliente:');
		$pdf->SetFont('Arial','',12);	
		$pdf->Text(10,85,"Mario Rossi");
		$pdf->Text(10,90,"Via Bianchi, 16");
		$pdf->Text(10,95,"Mantova");
		$pdf->Text(10,100,"Italia");
		$pdf->Output('fattura_28072012.pdf','D'); // mostro sul browser
    }
    
}

