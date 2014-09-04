<?php

class IncassiVari extends FunctionList
{
	private $_data=null;
	private $incasso=null;
	private $sort;
	
	protected function getData()
    {
        if($this->_data===null)
            $this->loadData();
        return $this->_data;
    }
    
    protected function CreateArray($descrizione = '', $order = ''){
	    $sqlmap = $this->Application->Modules['sqlmap']->Database;
		$sqlmap->Active = true;
		$sql = "SELECT rel_incassi_vari_stringer.* FROM rel_incassi_vari_stringer where rel_incassi_vari_stringer.id_stringer = " . $this->User->UserDB->id;
		$stringCriteria = " ";
		if($descrizione)
			$stringCriteria .= " AND " . $descrizione;
		
		//filtri
		$year = $this->DDLYear->SelectedValue;
		if($year != "" && $year != 0){
			$sql .= " and YEAR( data ) = " . $year;
		
		}
		
		$month = $this->DDLMonth->SelectedValue;
		if($month != "" && $month != 0){
			$sql .= " and MONTH( data ) = " . $month;
		}
		
    	$sql .= $stringCriteria;
    	if($order == 'descrizione')
    		$sql .= " order by descrizione";
    	else if($order == 'valore_incasso')
    		$sql .= " order by valore_incasso";
    	else if($order == 'data')
    		$sql .= " order by data";
    	else if($order == '')
    		$sql .= " order by data desc";
    	$command = $sqlmap->createCommand($sql);
    	$this->_data = $command->query()->readAll();
    }
    
    public function selectionChangedDDLYear($sender,$param){
    	$this->CreateArray($this->getViewState('sort','') );
    	$this->DataGridIncassiVari->DataSource=$this->Data;
    	$this->DataGridIncassiVari->dataBind();
    }
    
    public function selectionChangedDDLMonth($sender,$param){
    	$this->CreateArray($this->getViewState('sort','') );
    	$this->DataGridIncassiVari->DataSource=$this->Data;
    	$this->DataGridIncassiVari->dataBind();
    }
    
    protected function setUpFilter()
    {
    	$sqlmap = $this->Application->Modules['sqlmap']->Database;
    	$sqlmap->Active = true;
    	$sql = "select YEAR( data ) AS year FROM rel_incassi_vari_stringer  WHERE rel_incassi_vari_stringer.id_stringer = ". $this->User->UserDB->id . " GROUP BY YEAR( data ) ORDER BY YEAR( data ) desc";
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
    	$this->Page->Title = Prado::localize('ManageCustomers');
    	$this->editable->Visible = false;
    	$this->Search->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/search.gif';
		$this->Cancel->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->New->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/new-incasso.gif';
		$this->Save->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/save.gif';
		$this->Cancel_2->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/cancel.gif';
		$this->Change->ImageUrl = $this->Page->Theme->BaseUrl.'/images/'.$this->getApplication()->getGlobalization()->Culture.'/send.gif';
        if(!$this->IsPostBack)
        {
            $this->DataGridIncassiVari->DataSource=$this->Data;
            $this->DataGridIncassiVari->dataBind();
            $this->setUpFilter();
        }
    }
    
    public function selectIncassiVari($sender,$param)
    {
    	$this->editable->Visible = true;
    	$this->TypeEdit->Text = Prado::localize('Edit IncassiVari');
    	$this->incasso = RelIncassiVariStringer::finder()->findBy_id($param->Item->IDColumn->Text);
    	$this->DescrizioneIncassiVari->Text = $this->incasso->descrizione;
    	$this->ValoreIncassiVari->Text = $this->incasso->valore_incasso;
    	$this->DataIncassiVari->setTimeStamp(strtotime($this->incasso->data));
    	$this->setViewState('incasso',$this->incasso);
    }
    
    public function cancelItem($sender,$param)
    {
        $this->DataGridIncassiVari->SelectedItemIndex=-1;
        $this->DataGridIncassiVari->DataSource=$this->Data;
        $this->DataGridIncassiVari->dataBind();
    }
    
    public function createClicked()
    {   	
    	$this->setViewState('incasso',null); 
    	$this->DataGridIncassiVari->SelectedItemIndex=-1;	
    	$this->editable->Visible = true;
    	$this->TypeEdit->Text = Prado::localize('Insert New IncassiVari');
        $this->DescrizioneIncassiVari->Text = "";
        $this->ValoreIncassiVari->Text = "0";
        $this->DataIncassiVari->setTimeStamp(strtotime("now"));
    }
    
    public function saveClicked()
    {   	
    	if($this->getViewState('incasso',null) == null){
    		$this->incasso = new RelIncassiVariStringer();    		
    		$this->incasso->id_stringer = $this->User->UserDB->id;;
    	}else 
    		$this->incasso = $this->getViewState('incasso',null);
		$this->incasso->descrizione = $this->DescrizioneIncassiVari->Text;
		$this->incasso->valore_incasso = $this->ValoreIncassiVari->Text;
		$this->incasso->data = $this->DataIncassiVari->getDataOk();
		$this->incasso->save();
		
    	$this->RefreshData();
		
    	$this->CreateArray();
        $this->DataGridIncassiVari->SelectedItemIndex=-1;
        $this->DataGridIncassiVari->DataSource=$this->Data;
        $this->DataGridIncassiVari->dataBind();
    	
    }
    
    /* funzione che nasconde la gui per editare/creare l'oggetto */
    public function cancelClicked()
    {
        $this->DataGridIncassiVari->SelectedItemIndex=-1;
        $this->DataGridIncassiVari->DataSource=$this->Data;
        $this->DataGridIncassiVari->dataBind();
    }
    
    public function changePage($sender,$param)
    {
        $this->DataGridIncassiVari->CurrentPageIndex=$param->NewPageIndex;
        $this->CreateArray($this->FilterCollection_descrizione->getCondition(),$this->getViewState('sort',''));
        $this->DataGridIncassiVari->DataSource=$this->Data;
        $this->DataGridIncassiVari->dataBind();
    }
 
    public function pagerCreated($sender,$param)
    {
        $param->Pager->Controls->insertAt(0,Prado::localize('Page').': ');
    }
    
    public function onSearch($param){
		$this->DataGridIncassiVari->SelectedItemIndex=-1;
		$this->editable->Visible = false;
		$this->CreateArray($this->FilterCollection_descrizione->getCondition() );
		$this->DataGridIncassiVari->DataSource=$this->Data;
        $this->DataGridIncassiVari->dataBind();
	}
	
	public function onClear($param){
		$this->DataGridIncassiVari->SelectedItemIndex=-1;
		$this->editable->Visible = false;
		$this->setUpFilter();
		$this->FilterCollection_descrizione->clear();
		$this->CreateArray('','');		
		$this->DataGridIncassiVari->DataSource=$this->Data;
        $this->DataGridIncassiVari->dataBind();
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
		$this->CreateArray($this->FilterCollection_descrizione->getCondition(),$this->sort);
		$this->DataGridIncassiVari->DataSource=$this->sortData($this->Data,$param->SortExpression);
		$this->DataGridIncassiVari->dataBind();
	}
	
	public function changePageSize($sender,$param)
	{
		$this->DataGridIncassiVari->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->DataGridIncassiVari->CurrentPageIndex=0;
		$this->CreateArray($this->FilterCollection_descrizione->getCondition(),$this->getViewState('sort',''));
		$this->DataGridIncassiVari->DataSource=$this->Data;
		$this->DataGridIncassiVari->dataBind();
	}
	
	public function onItemCommand($sender,$param)
	{
		switch ($param->getCommandName())
		{
			case "elimina":
				$this->Elimina($sender,$param);
				break;
		}
	}
	
	public function Elimina($sender,$param)
	{
		$item = $param->Item;
		$incasso = RelIncassiVariStringer::finder()->findBy_id($param->Item->IDColumn->Text);
		$incasso->delete();
		$this->CreateArray();
		$this->DataGridIncassiVari->SelectedItemIndex=-1;
        $this->DataGridIncassiVari->DataSource=$this->Data;
        $this->DataGridIncassiVari->dataBind();
	}
}

