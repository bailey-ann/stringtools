<?php

class FunctionList extends TPage
{

	
	public function testF() {
		return "testF";
	}
	
	public function GetListOfTypeStringUsed($listJob){
		$listOfTypeString = TblStringType::finder()->findAll();
		$testarray = array();
		foreach( $listOfTypeString as $key => $type ){
			$testarray[$type->id] = 0;
		}

		foreach ( $listJob as $key => $job ) {
       		$stringMain = TblStrings::finder()->findBy_id($job->tbl_strings_id_main);
       		$intValue = $testarray[$stringMain->tbl_string_type_id];
       		$intValue = $intValue + 1;
       		$testarray[$stringMain->tbl_string_type_id] = $intValue;
       		if($job->tbl_strings_id_cross != $job->tbl_strings_id_main){
       			$stringCross = TblStrings::finder()->findBy_id($job->tbl_strings_id_cross);
       			$intValue = $testarray[$stringCross->tbl_string_type_id];
       			$intValue = $intValue + 1;
       			$testarray[$stringCross->tbl_string_type_id] = $intValue;
       		}
		}
		$finalArray = array();
		foreach( $listOfTypeString as $key => $type ){
			$finalArray[] = array('description'=>$type->description,'value'=>$testarray[$type->id]);
		}
		$finalArray = $this->array_multiorderby($finalArray, 'value desc, description desc');
		return $finalArray;
	}
	
	public function GetListOfStringUsed($listJob){
		
		$listOfString = TblStrings::finder()->findAll();
		$testarray = array();
		foreach( $listOfString as $key => $type ){
			$testarray[$type->id] = 0;
		}

		foreach ( $listJob as $key => $job ) {
       		$stringMain = TblStrings::finder()->findBy_id($job->tbl_strings_id_main);
       		$intValue = $testarray[$stringMain->id];
       		$intValue = $intValue + 1;
       		$testarray[$stringMain->id] = $intValue;
       		if($job->tbl_strings_id_cross != $job->tbl_strings_id_main){
       			$stringCross = TblStrings::finder()->findBy_id($job->tbl_strings_id_cross);
       			$intValue = $testarray[$stringCross->id];
       			$intValue = $intValue + 1;
       			$testarray[$stringCross->id] = $intValue;
       		}
		}
		//var_dump($testarray);
		$finalArray = array();
		foreach( $listOfString as $key => $type ){
			$string = TblStrings::finder()->findBy_id($type->id);
			$brand = TblBrands::finder()->findBy_id($string->tbl_brands_id);
			$gauge = TblGauges::finder()->findBy_id($type->tbl_gauges_id);
			$description = $brand->description . " " . $string->model . " " . $gauge->usa . " / " . $gauge->diameter;
			$finalArray[] = array('description'=>$description,'value'=>$testarray[$type->id]);
		}
		$finalArray = $this->array_multiorderby($finalArray, 'value desc, description desc');
		
		foreach($finalArray as $k => $v) {
		    if ($v["value"] == 0) {
		    	unset($finalArray[$k]);
		    }
		}
		
		return $finalArray;
	}
	
	function array_multiorderby( $data, $orderby, $children_key=false )
	{
		// parsing orderby
		$args = array();
		$x = explode( ' ', str_replace( ',', ' ', $orderby ) );
		foreach( $x as $item ) 
		{
			$item = trim( $item );
			if( $item=='' ) continue;
			if( strtolower($item)=='asc' ) $item = SORT_ASC;
			else if( strtolower($item)=='desc' ) $item = SORT_DESC;
			$args[] = $item;
		}
		
		// order
		foreach ($args as $n => $field) 
		{
			if (is_string($field)) 
			{
				$tmp = array();
				foreach ($data as $key => $row)
				$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
			}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		$data = array_pop($args);
		
		// order children
		if( $children_key )
		{
			foreach( $data as $k=>$v ) 
				if( is_array($v[$children_key]) ) 
					$data[$k][$children_key] = array_multiorderby( $v[$children_key], $orderby, $children_key );
		}
		
		// return
		return $data;
	}
	
	function formatJob($id){
		$addFirst = "S";
		$addChar = "0";
		$lenghtJob = 6;
		while(strlen($id) < $lenghtJob){
			$id = $addChar . $id;
		}
		return $addFirst.$id;
	}
	
	function makeHtmlJob($job){ //.''.   '..'
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
		if($job->tbl_overgrip_id != 0){
			$overgrip = TblOvergrips::finder()->findBy_id($job->tbl_overgrip_id);
			$brand = TblBrands::finder()->findBy_id($overgrip->tbl_brands_id);
			$overgripString = $brand->description . " " . $overgrip->model;
		}
		
		$y = substr($job->date_stringing, 0, 4);
		$m = substr($job->date_stringing, 5, 2);
		$d = substr($job->date_stringing, 8, 2);
		$dateString = $d . "-".$m."-".$y;
		
		$htmlMain = '<table border="1"  cellpadding="2">
						<tr>
							<td colspan="4" ><span style="font-weight:bold">'.Prado::localize('StringMains').'</span>
							</td>
						</tr>
						<tr>
							<td colspan="4" align="center">'.$brandMainString->description . ' ' . $mainString->model. ' ' . $gaugeMainString->usa . ' (' . $gaugeMainString->diameter.')
							</td>
						</tr>
						<tr>
							<td >'.Prado::localize('Tension').'
							</td>
							<td >'.$job->weight_main. ' ' . $this->User->UserDB->weight_unit->description . ' 
							</td>
							<td >'.Prado::localize('Prestretch').'
							</td>
							<td >'.$job->prestretch_main.' %
							</td>
						</tr>
					</table>';
					
		$htmlCross = '<table border="1"  cellpadding="2">
						<tr>
							<td colspan="4" ><span style="font-weight:bold">'.Prado::localize('StringCross').'</span>
							</td>
						</tr>
						<tr>
							<td colspan="4" align="center">'.$brandCrossString->description . ' ' . $crossString->model. ' ' . $gaugeCrossString->usa . ' (' . $gaugeCrossString->diameter.')
							</td>
						</tr>
						<tr>
							<td >'.Prado::localize('Tension').'
							</td>
							<td >'.$job->wieght_cross . ' ' . $this->User->UserDB->weight_unit->description . ' 
							</td>
							<td >'.Prado::localize('Prestretch').'
							</td>
							<td >'.$job->prestretch_cross.' %
							</td>
						</tr>
					</table>';
							
				
		$html = '<table border="1"  cellpadding="4"> 
					<tr>
						<td>'.Prado::localize('JobID').'
						</td>
						<td >'.$stringJob. ' 
						</td>
						<td>'.Prado::localize('Date Stringing').'
						</td>
						<td >'.$dateString. ' 
						</td>
					</tr>
					<tr>
						<td>'.Prado::localize('Customer').'
						</td>
						<td colspan="3">'.$customer->name . ' ' . $customer->surname. ' 
						</td>
					</tr>
					<tr>
						<td>'.Prado::localize('Racquet').'
						</td>
						<td colspan="3">'.$brandRacquet->description. ' ' . $racquetModel->model . '
						</td>
					</tr>
					<tr>
						<td>'.Prado::localize('SerialRacquet').'
						</td>
						<td colspan="3">'.$racquetCustomer->serial. ' 
						</td>
					</tr>
					<tr>
						<td>'.Prado::localize('Stringer').'
						</td>
						<td colspan="3">'.$this->User->UserDB->name . ' ' . $this->User->UserDB->surname. ' 
						</td>
					</tr>
					<tr>
						<td>'.Prado::localize('StringingMachine').'
						</td>
						<td colspan="3">'.$brandStringingMachine->description . ' ' . $stringingMachine->model. ' 
						</td>
					</tr>
					<tr>
						<td colspan="4">'.$htmlMain.'
						</td>
					</tr>
					<tr>
						<td colspan="4">'.$htmlCross.'
						</td>
					</tr>
					<tr>
						<td width="25%">'.Prado::localize('StringingType').'
						</td>
						<td width="25%">'.$stringingJobType->description.'
						</td>
						<td width="25%">'.Prado::localize('DynamicTension').'
						</td>
						<td width="25%">'.$job->dynamic_tension . ' 
						</td>
					</tr>
					<tr>
						<td>'.Prado::localize('Stencyl').'
						</td>
						<td>'.$stencyl.'
						</td>
						<td>'.Prado::localize('GrommetsGuard').'
						</td>
						<td>'.$grommet . ' 
						</td>
					</tr>
					<tr >
						<td width="15%">'.Prado::localize('Grips').'
						</td>
						<td width="35%">'.$gripString.'
						</td>
						<td width="15%">'.Prado::localize('Overgrips').'
						</td>
						<td width="35%">'.$overgripString . ' 
						</td>
					</tr>
					<tr>
						<td width="20%">'.Prado::localize('TotalPrice').'
						</td>
						<td width="80%">'.$job->total_price. ' 
						</td>
					</tr>
					<tr>
						<td width="20%">'.Prado::localize('NoteStringing').'
						</td>
						<td width="80%">'.$job->note. ' 
						</td>
					</tr>
				</table>';
		return $html;
	}

}