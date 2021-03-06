<?php
class OPSDatePicker extends TTemplateControl implements IValidatable{
	public function onPreRender($param){
		if (!$this->ShowHours && $this->ShowMinutes){
			throw new TConfigurationException(
				'Invalid OPSDatePicker Config: You cannot specify ShowMinutes="true" while ShowHours="false"'
			);
		}
	}
	
	public function setFromYear($date){
		$this->DatePicker->FromYear = $date;
	}
	
	public function setUpToYear($date){
		$this->DatePicker->UpToYear = $date;
	}

    public function getDatePicker(){
        $this->ensureChildControls();
        return $this->getRegisteredObject('datePicker');
    }
    public function getHourPicker(){
        $this->ensureChildControls();
        return $this->getRegisteredObject('hourPicker');
    }
    public function getMinutePicker(){
        $this->ensureChildControls();
        return $this->getRegisteredObject('minutePicker');
    }
	
	public function getShowHours(){
		return $this->HourPicker->Visible;
	}
	public function setShowHours($value){
		$this->HourPicker->Visible = TPropertyValue::ensureBoolean($value);
	}
	public function getShowMinutes(){
		return $this->MinutePicker->Visible;
	}
	public function setShowMinutes($value){
		$this->MinutePicker->Visible = TPropertyValue::ensureBoolean($value);
	}

	public function getTimeStamp(){
		return strtotime($this->Date);
	}
	public function setTimeStamp($value){
		$ts = TPropertyValue::ensureInteger($value);
		$this->DatePicker->TimeStamp = $ts;
		$this->HourPicker->SelectedValue = date('H', $ts);
		$this->MinutePicker->SelectedValue = date('i', $ts);
	}
	public function getDate(){
		$dateStr = $this->DatePicker->Date;
		if ($this->ShowHours){
			$dateStr .= ' '.$this->HourPicker->SelectedValue;
		}
		if ($this->ShowMinutes){
			$dateStr .= ':'.$this->MinutePicker->SelectedValue;
		}
		return $dateStr;
	}
	public function setDate($value){
		$dateStr = TPropertyValue::ensureString($value);
		$this->TimeStamp = strtotime($dateStr);
	}
	
	public function getValidationPropertyValue(){
		if ($this->DatePicker->Date === ''){
			return '';
		}
		else{
			return $this->TimeStamp;
		}
	}
	
	public function getIsValid(){}
	public function setIsValid($value){}
	
	public function getDataOk(){
		$dateStr = $this->DatePicker->Date;
		list($d, $m, $y) = explode('/', $dateStr);
		return $y."-".$m."-".$d." ".$this->HourPicker->SelectedValue.":".$this->MinutePicker->SelectedValue.":00";
	}
}
?>