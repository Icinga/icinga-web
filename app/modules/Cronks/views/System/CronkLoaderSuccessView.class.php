<?php

class Cronks_System_CronkLoaderSuccessView extends ICINGACronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		
		$this->setAttribute('title', 'Icinga.CronkLoader');
		
		try {
			$cronks		= AgaviConfig::get('modules.cronks.cronks');
			$cronk		= $rd->getParameter('cronk'); 
			
			$parameters = new AgaviParameterHolder($rd->getParameter('p', array()));
			
			if ($parameters->getParameter('cmpid', null) == null) {
				$parameters->setParameter('cmpid', 'cronk-'. AppKitRandomUtil::genSimpleId(10));
			}
				
			if (array_key_exists($cronk, $cronks)) {
				$meta = $cronks[$cronk];
				
				if (array_key_exists('ae:parameter', $meta) && is_array($meta['ae:parameter'])) {
					foreach ($meta['ae:parameter'] as $pKey=>$pVal) {
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