<?php

Prado::using('System.Web.UI.WebControls.TDataGrid');
Prado::using('System.Util.TSimpleDateFormatter');


abstract class TFilter extends TCompositeControl{
	
	protected 		$_control;
	protected 		$_validator;
	private				$_dataField;
	protected 		$_autoValidator = true;
	protected 		$_autoLabel = false;
	protected 		$_headerText;
	protected 		$_filterId;
	
	public function createLabel($s){
		if ( $this->_autoLabel ){
			$label = new TLabel;
			$label->setText(($this->_headerText ? $this->_headerText : $this->_dataField));
			$label->setForControl($s);
			$this->getControls()->add($label);
		}
	}
	
	public function setFilterId($s){ $this->_filterId = $s; }
	public function setHeaderText($s){ $this->_headerText = $s; }
	public function setDataField($s){ $this->_dataField = $s; }
	public function getDataField(){
		if ( !$this->_dataField ) throw new Exception("datafield must not be empty!");
		return $this->_dataField; }
	public function setAutoValidator($b) { $this->_autoValidator = $b; }
	
	public function getFilter(){ return $this->getControl(); }
	
	public function getControl(){
		$this->ensureChildControls();
		return $this->_control;
	}
	
	protected function genFilterId(){ 
		return "filter_".str_replace(".", "_", ($this->_filterId ? $this->_filterId : $this->getDataField())); 
	}
	
	abstract public function getCondition();

	abstract public function clear();
	
}

class  TEmptyFilter extends TFilter{
	public function getCondition(){ return NULL; }
	public function clear(){ return NULL; }
	
}

abstract class TFilterList extends TFilter{
	
	const FILTER_LIST 	= 'LISTBOX';
	const FILTER_CHECK 	= 'CHECKBOXLIST';
	const FILTER_DROP 	= 'DROPDOWNLIST';
	const FILTER_RADIO 	= 'RADIOBUTTONLIST';
	
	protected $_type; 
	protected $_selectionMode = 'Single';
	protected $_promptText;
	protected $_promptValue;

	public function setPromptText($s){ $this->_promptText = $s; }
	public function setPromptValue($s){ $this->_promptValue = $s; }
	
	public function setDataSource($o){ $this->_control->setDataSource($o); }
	public function setDataTextField($s){ $this->_control->setDataTextField($s); }
	public function setDataValueField($s){ $this->_control->setDataValueField($s); }
	public function dataBind(){ $this->_control->dataBind(); }
	
	public function createChildControls(){
		switch($this->_type){
			case self::FILTER_LIST: $o = new TListBox;
				//$o->setSelectionMode($this->_selectionMode);
				break;
			case self::FILTER_CHECK: $o = new TCheckBoxList;
				break;
			case self::FILTER_DROP: $o = new TDropDownList;
				break;
			case self::FILTER_RADIO: $o = new TRadioButtonList;
				break;
			default: throw new TInvalidDataTypeException(	'Type of TFilterList must be on of TFilterList::FILTER_LIST, TFilterList::FILTER_CHECK, '.
																								'TFilterList::FILTER_DROP, TFilterList::FILTER_RADIO'); //TODO Put to messages
		}
		$o->setPromptText($this->_promptText);
		$o->setPromptValue($this->_promptValue);
	
		$o->setId($this->genFilterId());
		$this->_control = $o;
		$this->createLabel($o->getId());
		$this->getControls()->add($o);
	}
	
	public function addParsedObject($o){
		$this->ensureChildControls();
		if ( $o instanceof TListItem)
			$this->_control->addParsedObject($o);  
		else
			parent::addParsedObject($o);
	}
	
	public function getCondition(){
		$s = '';
		$indices=$this->_control->SelectedValues;
		if ( count($indices) > 1 ){
			$s = ' in ('.implode(',', $indices).')';
		}
		else if ( count($indices) == 1 )
			$s = ' = '.$indices[0];
		return ($s ? $this->getDataField().$s : NULL); 
	}
	
	public function clear(){
		$this->_control->clearSelection();		
	}
	
}

 
class TFilterDropDownList extends TFilterList{ public function __construct(){ $this->_type = self::FILTER_DROP; } }   
class TFilterCheckBoxList extends TFilterList{ public function __construct(){ $this->_type = self::FILTER_CHECK; } }  
class TFilterRadioButtonList extends TFilterList{ public function __construct(){ $this->_type = self::FILTER_RADIO; } }
class TFilterListBox extends TFilterList{ 
	public function __construct(){ $this->_type = self::FILTER_LIST; } 
	public function setSelectionMode($s){  
		$this->_selectionMode = $s; 
	} 
}

class TFilterText extends TFilter{
	
	const MATCH_EXACT = 'MATCH_EXACT';
	const MATCH_INNER = 'MATCH_INNER';
	const MATCH_LEFT =  'MATCH_LEFT';
	const MATCH_RIGHT = 'MATCH_RIGHT';
	const DEFAULT_MATCH = self::MATCH_INNER;
	
	protected $_match = self::DEFAULT_MATCH;
	
	protected $_vali;
	
	public function createChildControls(){
		$o = new TTextBox;
		$o->setId($this->genFilterId());
		$this->_control = $o;
		$this->createLabel($o->getId());
		$this->getControls()->add($o);
		if ( $this->_vali ){
			$this->_vali->setControlToValidate($o->getId());
			$this->getControls()->add($this->_vali);
		}
	}
	
	public function ensureValidator(){
		if ( !$this->_vali ) $this->_vali = new TRegularExpressionValidator(); 
	}
	
	public function setMatch($s){ $this->_match = $s; }
	public function getMatch(){ return $this->_match; }
	
	public function getCondition(){
		$s = $this->_control->getText();
		if ( !$s ) return NULL;
		switch($this->_match){
			case self::MATCH_INNER:	$s = "%$s%";
				break;
			case self::MATCH_RIGHT: $s = "%$s";
				break;
			case self::MATCH_LEFT: $s = "$s%"; 
				break;
			case self::MATCH_EXACT: 
				break;
			default: throw new TInvalidDataTypeException(	'Match of TFilterText must be one of TFilterText::MATCH_INNER, TFilterText::MATCH_RIGHT, '.
																								'TFilterText::MATCH_LEFT, TFilterText::MATCH_EXACT'); //TODO Put to messages
		}
		return $this->getDataField()." LIKE '$s' ";
	}
	
	public function getNoFieldCondition(){
		$s = $this->_control->getText();
		if ( !$s ) 
			return NULL;
		else
			return $s;
	}
	
	public function clear(){
		$this->_control->setText('');
	}
	
	public function setRegularExpression($s){
		$this->ensureValidator();
		$this->_vali->setRegularExpression($s); 
	}
	
	public function setErrorMessage($s){
		$this->ensureValidator();
		$this->_vali->setErrorMessage($s); 
	}
	
	public function setEnableClientScript($b){
		$this->ensureValidator();
		$this->_vali->setEnableClientScript($b); 
	}
	
	public function setDisplay($s){
		$this->ensureValidator();
		$this->_vali->setDisplay($s);
	}
	
	public function setControlCssClass($s){
		$this->ensureValidator();
		$this->_vali->setControlCssClass($s);
	}
		
}

class TFilterDate extends TFilter{
	
	const MATCH_EXACT = 'MATCH_EXACT';
	const MATCH_LESSER = 'MATCH_LESSER';
	const MATCH_LESSER_EQUAL = 'MATCH_LESSER_EQUAL';
	const MATCH_GREATER = 'MATCH_GREATER';
	const MATCH_GREATER_EQUAL = 'MATCH_GREATER_EQUAL';
	const DEFAULT_MATCH = self::MATCH_EXACT;
	
	protected $_match = self::DEFAULT_MATCH;
	
	private $_dateFormat = 'dd.MM.yyyy';

	
	public function __construct($s = ''){ $this->_filterId = $s;} 
	
	public function createChildControls(){
		$m = new TTextBox;
		$m->setId(($this->_filterId ? $this->_filterId : $this->genFilterId()));
		$this->getControls()->add($m); 
		
		$vali = new TDataTypeValidator;
		$vali->setEnableClientScript(true);
		$vali->setDisplay('Dynamic');
		$vali->setControlToValidate($m->getId());
		$vali->setDataType('Date');
		$vali->setDateFormat($this->_dateFormat);
		$vali->setText(Prado::localize('Wrong date format'));
		$this->createLabel($m->getId());
		$this->getControls()->add($vali);
		
		$this->_control =  $m;		
	}
	
	public function setDateFormat($s){ $this->_dateFormat = $s; }
	
	protected function getDbDate(){
		if ( !($s = $this->_control->getText()) ) return NULL;
		$formatter = new TSimpleDateFormatter($this->_dateFormat);
		$formatterDb = new TSimpleDateFormatter('yyyy-MM-dd');
		if ( ($ts = $formatter->parse($s)) == false ) 
			throw new TInvalidDataTypeException(	'Invalid date input. (The validator should have caught that already)'); //TODO Put to messages	
		$dbDate = $formatterDb->format($ts);
		if ( !$dbDate ) 
			throw new TInvalidDataTypeException(	'Invalid date input. (The validator should have caught that already)'); //TODO Put to messages
		return $dbDate;
	}
	
	public function getCondition(){
		$s = $this->getDbDate();
		if ( !$s ) return NULL;
		$op = '';
		switch($this->_match)
		{
			case self::MATCH_EXACT:	$op = '=';
				break;
			case self::MATCH_LESSER:	$op = '<';
				break;
			case self::MATCH_LESSER_EQUAL:	$op = '<=';
				break;
			case self::MATCH_GREATER:	$op = '>';
				break;
			case self::MATCH_GREATER_EQUAL:	$op = '>=';
				break;
			default: 
				throw new TInvalidDataTypeException('Match of TFilterDate must be one of (TFilterDate::MATCH_(EXACT|(GREATER|LESSER[_EQUAL])))'); //TODO Put to messages
		}
		return ( $op ? $this->getDataField()." $op '$s'" : '');
	}
	
	public function clear(){
		$this->_control->setText('');
	}
	
	public function setMatch($s){ $this->_match = $s; }
	public function getMatch(){ return $this->_match; }
		
}

class TFilterDateRange extends TFilter{
	
	private $_filterFrom;
	private $_filterTo;
	private $_dateFormat = 'dd.MM.yyyy';
	
	public function getFilterFrom(){
		$this->ensureChildControls();
		return $this->_filterFrom->getControl();	
	}
	public function getFilterTo(){
		$this->ensureChildControls();
		return $this->_filterTo->getControl();
	}
	
	public function createChildControls(){
		$this->_filterFrom = new TFilterDate($this->genFilterId().'_from');
		$this->_filterFrom->setDataField($this->getDataField());
		$this->_filterFrom->setMatch(TFilterDate::MATCH_GREATER_EQUAL);
		$this->_filterFrom->setDateFormat($this->_dateFormat);
		
		$this->_filterTo = new TFilterDate($this->genFilterId().'_to');
		$this->_filterTo->setDataField($this->getDataField());
		$this->_filterTo->setMatch(TFilterDate::MATCH_LESSER_EQUAL);
		$this->_filterTo->setDateFormat($this->_dateFormat);

		$this->getControls()->add($this->_filterFrom);
		$this->getControls()->add($this->_filterTo);
		$this->_control = array($this->_filterFrom, $this->_filterTo);
	}
	
	public function setDateFormat($s){ $this->_dateFormat = $s; }
	
	public function getCondition(){
		$s = '';
		if ( ($condFrom = $this->_filterFrom->getCondition()) ) $s = $condFrom; 
		if ( ($condTo = $this->_filterTo->getCondition()) )  $s = ($s ? $s.' AND ' : '').$condTo;
		return ( $s ? $s : NULL );
	}
	
	public function clear(){
		$this->_filterFrom->clear();
		$this->_filterTo->clear();
	}
}

class TFilterCollection extends TTableRow{
	
	protected $_dataGridId;
	protected $_dataGridInstance;
	protected $_filters = array();
	
	public function getDataGrid(){ return $this->_dataGridId;	}
	public function setDataGrid($s){ $this->_dataGridId = $s;	}
	
	public function addParsedObject($o){
		if ( $o instanceof TFilter){
			$this->_filters[$o->getDataField()] = $o;
			$cell = new TTableCell;
			//$cell->setId('cell_'.$o->getId());
			$cell->getControls()->add($o);
			$this->getCells()->add($cell);
		} else 
		parent::addParsedObject($o);
	}
	
	public function getFilterForDataField($s){ return $this->_filters[$s]; }
	
	public function getCondition(){
		$a = array();
		foreach($this->_filters as $f){
			if ( $f instanceof TFilter )
				if ( ($c = $f->getCondition()) ){
					$a[] = $c;
				}
		}
		return implode(" AND ", $a);
	}
	
	public function getNoFieldCondition(){
		$a = array();
		foreach($this->_filters as $f){
			if ( $f instanceof TFilter )
				if ( ($c = $f->getNoFieldCondition()) ){
					$a[] = $c;
				}
		}
		return implode(" AND ", $a);
	}
	
	public function clear(){
		foreach($this->_filters as $f){
			if ( $f instanceof TFilter ) 
				$f->clear(); 
		}
	}
	
	public function getFilters(){
		$this->ensureChildControls(); 
		return $this->_filters; 
	}
	
	/* 
	public function toTableRow(){
		$this->ensureChildControls();
		$row = new TTableRow;
		$row->setTableSection(TTableRowSection::Header);
		$cells = $row->getCells();
		$i=0;
		foreach($this->getControls() as $filter){
		//foreach($this->_filters as $filter){
			if ( $filter instanceof TFilter ){
				$cell = new TTableCell;
				$cell->getControls()->add($filter);
				$cells->add($cell);
			}
		}
		return $row;
	}*/
	
}


?>