<?php

namespace JVelthuis\JVPdf;

use LaminasPdf\Page as LaminasPage;

class JVPDF_Page
{
	private LaminasPage $laminasPage;

	var JVPDF $pdf;
	
	public function __construct(LaminasPage $laminasPage, JVPDF $pdf){
		$this->laminasPage = $laminasPage;
		$this->pdf = $pdf;
	}
	
	public function getLaminasPage(){
		return $this->laminasPage;
	}
	
	// Add methods for drawing text, images, etc.
}
