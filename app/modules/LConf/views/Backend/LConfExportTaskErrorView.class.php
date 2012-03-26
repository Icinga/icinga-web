<?php

class LConf_Backend_LConfExportTaskErrorView extends IcingaLConfBaseView
{
	public function executeJson(AgaviRequestDataHolder $rd) 
	{
		$this->getResponse()->setHttpStatusCode('500');
		$json = array(
			"success" => false,
			"error" => $this->getAttribute("error_msg","An error occured")
		);
		return json_encode($json);
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.LConfExportTask');
	}
}

?>
