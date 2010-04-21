<?php

class Cronks_System_CronkLoaderSuccessView extends ICINGACronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		
		$this->setAttribute('title', 'Icinga.CronkLoader');
		
		$tm = $this->getContext()->getTranslationManager();
		
		try {
			
			$model = $this->getContext()->getModel('System.CronkData', 'Cronks', array('filter' => 'exec'));
			
			$crname = $rd->getParameter('cronk'); 
			
			$parameters = array() + (array)$rd->getParameter('p', array());
			
			if ($model->hasCronk($crname)) {
				$cronk = $model->getCronk($crname);
				
				if (array_key_exists('ae:parameter', $cronk)) {
					$parameters = (array)$cronk['ae:parameter'] + $parameters;
				}
				
				return $this->createForwardContainer($cronk['module'], $cronk['action'], $parameters, 'simple', 'read');
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
