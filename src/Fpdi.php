<?php

namespace JVelthuis\JVPdf;

class Fpdi extends \setasign\Fpdi\Tcpdf\Fpdi {
	
	public $templateId;
	
	
	public function AcceptPageBreak(){
		
		if ($this->num_columns > 1) {
			// multi column mode
			if ($this->current_column < ($this->num_columns - 1)) {
				// go to next column
				$this->selectColumn($this->current_column + 1);
			} elseif ($this->AutoPageBreak) {
				// add a new page
				$this->AddPage();
				if(isset($this->templateId)){
					$this->useTemplate($this->templateId);
				}
				// set first column
				$this->selectColumn(0);
			}
			// avoid page breaking from checkPageBreak()
			return false;
		}
		return $this->AutoPageBreak;
	}
	
}
