<?php

class Reporting_Report_GenerateSuccessView extends ReportingBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
 		
	}
	
	public function executePdf(AgaviRequestDataHolder $rd) {
	    return "PDF";
	}
	
	public function executeCsv(AgaviRequestDataHolder $rd) {
	    return "CSV";
	}
}

?>