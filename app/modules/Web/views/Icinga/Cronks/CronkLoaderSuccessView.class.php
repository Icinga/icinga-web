<?php

class Web_Icinga_Cronks_CronkLoaderSuccessView extends ICINGAWebBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setAttribute('title', 'Icinga.CronkLoader');
		
		$cronks		= AgaviConfig::get('de.icinga.web.cronks');
		$cronk		= $rd->getParameter('cronk');
		$parameters	= $rd->getParameter('p', array());
		
		if (array_key_exists($cronk, $cronks)) {
			$module = $cronks[$cronk]['module'];
			$action = $cronks[$cronk]['action'];
			
			$c = $this->createForwardContainer($module, $action, $parameters, 'simplecontent', 'read')
				->execute()
				->getContent();
			
				
			return $c;
		}
		
		$this->setupHtml($rd);
	}
}

?>