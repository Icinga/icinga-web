<?php

class Cronks_System_CronkLoaderSuccessView extends CronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		
		$this->setAttribute('title', 'Icinga.CronkLoader');
		
		$tm = $this->getContext()->getTranslationManager();
		
		try {
			
			$model = $this->getContext()->getModel('Provider.CronksData', 'Cronks');
			
			$crname = $rd->getParameter('cronk'); 
			
			$parameters = array() + (array)$rd->getParameter('p', array());
			
			if ($model->hasCronk($crname)) {
				$cronk = $model->getCronk($crname);
				
				if (array_key_exists('ae:parameter', $cronk) && is_array($cronk['ae:parameter'])) {
					
//					foreach($cronk['ae:parameter'] as $key=>$param) {
//						if(is_array($param) || is_object($param)) {
//							$param = json_encode($param);
//							$cronk['ae:parameter'][$key] = $param;
//							$parameters[$key] = $param;
//						}
//					}

					$parameters = (array)$cronk['ae:parameter']
					+ $parameters
					+ array('module' => $cronk['module'], 'action' => $cronk['action']);
				}
				
				if (array_key_exists('state', $cronk) && isset($cronk['state'])) {
					$parameters['state'] = $cronk['state'];
				}
				
				return $this->createForwardContainer($cronk['module'], $cronk['action'], $parameters, 'simple', 'write');
			}
			else {
				return $tm->_('Sorry, cronk "%s" not found', null, null, array($crname));
			}
		}
		catch (Exception $e) {
			return $tm->_('Exception thrown: %s', null, null, array($e->getMessage()));
		}
		
		return 'Some strange error occured';
	}
}

?>
