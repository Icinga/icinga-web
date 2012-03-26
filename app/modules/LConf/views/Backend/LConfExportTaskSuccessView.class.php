<?php

class LConf_Backend_LConfExportTaskSuccessView extends IcingaLConfBaseView
{
	public function executeJson(AgaviRequestDataHolder $rd) {
		$success = true;
		$json = array(
			"success" => true,
			"config" => $this->getAttribute("config",array())
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
