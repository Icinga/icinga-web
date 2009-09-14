<?php

class Cronks_System_CronkLoaderSuccessView extends ICINGACronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		
		$this->setAttribute('title', 'Icinga.CronkLoader');
		
		try {
			$cronks		= AgaviConfig::get('de.icinga.web.cronks');
			$cronk		= $rd->getParameter('cronk'); 
			
			$parameters = new AgaviParameterHolder($rd->getParameter('p', array()));
			if ($parameters->getParameter('htmlid', null) == null) {
				$parameters->setParameter('htmlid', 'cronk-'. AppKitRandomUtil::genSimpleId(10));
			}
				
			// Adding default parameters if they are not overwritten
			$meta = AgaviConfig::get('de.icinga.web.cronks');
			if (array_key_exists($cronk, $meta)) {
				$meta = $meta[$cronk];
				if (array_key_exists('parameter', $meta) && is_array($meta['parameter'])) {
					foreach ($meta['parameter'] as $pKey=>$pVal) {
						if ($parameters->getParameter($pKey, null) == null) {
							$parameters->setParameter($pKey, $pVal);
						}
					}
				} 
			}
			
			if (array_key_exists($cronk, $cronks)) {
				$module = $cronks[$cronk]['module'];
				$action = $cronks[$cronk]['action'];
				
				$c = $this->createForwardContainer($module, $action, $parameters->getParameters(), 'simplecontent', 'read')
					->execute()
					->getContent();
				
						
				return $c;
			}
			else {
				return 'Sorry, the cronk could not be loaded (not exist)';
			}
		}
		catch (Exception $e) {
			return $e->getMessage();
		}
		
		return 'Some strange error occured';
	}
}

?>