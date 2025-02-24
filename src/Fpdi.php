<?php

namespace JVelthuis\JVPdf;

class Fpdi extends \setasign\Fpdi\Tcpdf\Fpdi {
	
	
	public $templateCallback;
	
	/**
	 * @var \JVelthuis\JVPdf\JVPDF;
	 */
	public $wrapper;
	
	
	public function AddPage($orientation = '', $format = '', $keepmargins = false, $tocpage = false){
		parent::AddPage($orientation, $format, $keepmargins, $tocpage);
		if(isset($this->wrapper) && $this->wrapper->useTemplateWithAddNewPage ){
			$this->wrapper->pdf->useTemplate($this->wrapper->template);
		}
	}
	
	public function AddBlankPage($orientation = '', $format = '', $keepmargins = false, $tocpage = false){
		return parent::AddPage($orientation, $format, $keepmargins, $tocpage);
	}
	
	
}
