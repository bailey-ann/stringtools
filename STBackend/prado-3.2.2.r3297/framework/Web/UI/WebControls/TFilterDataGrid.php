<?php

Prado::using('System.Web.UI.WebControls.TDataGrid');


class TFilterDataGrid extends TDataGrid{
	
	private $_filterCollection;
	private $_filterRow;
	
	public function addParsedObject($object)
	{
		if($object instanceof TFilterCollection){
			$this->_filterCollection = $object;
			$this->getControls()->add($this->_filterCollection);
		} else 
		parent::addParsedObject($object);
	}
	
	/* Renders the tabular data.
	 * @param THtmlWriter writer
	 */
	protected function renderTable($writer) //copied verbatim from TDataGrid
	{
		$this->renderBeginTag($writer);
		if($this->getHeader() && $this->getHeader()->getVisible())
		{
			$writer->writeLine();
			if($style=$this->getViewState('TableHeadStyle',null))
				$style->addAttributesToRender($writer);
			$writer->renderBeginTag('thead');
			$this->getHeader()->render($writer);
			$writer->renderEndTag();
		}
		if ($this->_filterCollection) //NEW
		{
			$writer->writeLine();
			//if($style=$this->getViewState('TableHeadStyle',null))
			//	$style->addAttributesToRender($writer);
			$writer->renderBeginTag('thead');
			$this->_filterCollection->render($writer);
			$writer->renderEndTag();
		} //#NEW
		$writer->writeLine();
		if($style=$this->getViewState('TableBodyStyle',null))
			$style->addAttributesToRender($writer);
		$writer->renderBeginTag('tbody');
		foreach($this->getItems() as $item)
			$item->renderControl($writer);
		$writer->renderEndTag();

		if($this->getFooter() && $this->getFooter()->getVisible())
		{
			$writer->writeLine();
			if($style=$this->getViewState('TableFootStyle',null))
				$style->addAttributesToRender($writer);
			$writer->renderBeginTag('tfoot');
			$this->getFooter()->render($writer);
			$writer->renderEndTag();
		}

		$writer->writeLine();
		$this->renderEndTag($writer);
	}
	
}

?>