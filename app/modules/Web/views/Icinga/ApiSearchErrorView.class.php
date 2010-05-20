<?php

class Web_Icinga_ApiSearchErrorView extends IcingaWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		return "Invalid Arguments!";
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) 
	{
		$context = $this->getContext();
		$validation = $this->getContainer()->getValidationManager();
		$errorMsg = array("error"=>array());
		foreach($validation->getErrorMessages() as $error) {
			$errorMsg["error"][] =  $error;
		}
		return json_encode($errorMsg);
	}

	public function executeXml(AgaviRequestDataHolder $rd) 
	{
		
		echo "<?xml version='1.0' encoding='utf-8'><error><message>Invalid arguments!</message></error>";
	}

	public function executeSimple(AgaviRequestDataHolder $rd) 
	{
		echo "Invalid arguments";
	}
}