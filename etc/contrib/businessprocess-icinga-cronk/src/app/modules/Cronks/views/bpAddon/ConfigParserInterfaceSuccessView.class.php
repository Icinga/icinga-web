<?php

class Cronks_bpAddon_ConfigParserInterfaceSuccessView extends CronksBaseView
{
	public function executeJson(AgaviRequestDataHolder $rd) {
		try {
			$action = $rd->getParameter("action");
			$parser = $this->getContext()->getModel("bpAddon.configParser","Cronks");
			switch($action) {
				case 'parseJSON_show':
					$cfg = $parser->jsonToConfigString($rd->getParameter("json"));
					return json_encode(array(
						"errors" => $parser->getConsistencyErrors($cfg),
						"config" => $cfg
					));
					break;
				case 'parseJSON_save':
					$file = $rd->getParameter("filename");
					if(!$file)
						throw new AppKitException("Invalid filename provided.");
					$cfg = $parser->jsonToConfigFile($rd->getParameter("json"),$file);
					break;
				case 'parseCfg':
					$file = $rd->getParameter("filename");
					if(!$file)
						throw new AppKitException("Invalid filename provided.");
					$cfg = $parser->configFileToJson($file);
					return $cfg;
					break;
				case 'getConfigList':
					return json_encode($parser->listConfigFiles());
					break;
				case 'removeCfg':
					$file = $rd->getParameter("filename");
					if(!$file)
						throw new AppKitException("Invalid filename provided.");
					$cfg = $parser->removeConfigFile($file);
					return 'Success';
					break;
			}
		} catch(Exception $e) {
			$this->getResponse()->setHttpStatusCode('500');
			return $e->getMessage();
		}
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'bpAddon.ConfigParserInterface');
	}
}

?>