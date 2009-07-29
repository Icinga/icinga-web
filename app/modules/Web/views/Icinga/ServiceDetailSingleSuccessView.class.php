<?php

class Web_Icinga_ServiceDetailSingleSuccessView extends ICINGAWebBaseView
{
	
	private $fields = array (
		'SERVICE_ID', 'SERVICE_INSTANCE_ID', 'SERVICE_CONFIG_TYPE', 'SERVICE_IS_ACTIVE',
		'SERVICE_OBJECT_ID', 'SERVICE_NAME', 'SERVICE_DISPLAY_NAME', 'SERVICE_NOTIFICATIONS_ENABLED',
		'SERVICE_OUTPUT', 'SERVICE_PERFDATA', 'SERVICE_CURRENT_STATE', 'SERVICE_CURRENT_CHECK_ATTEMPT',
		'SERVICE_MAX_CHECK_ATTEMPTS', 'SERVICE_LAST_CHECK', 'SERVICE_LAST_STATE_CHANGE',
		'SERVICE_CHECK_TYPE', 'SERVICE_LATENCY', 'SERVICE_EXECUTION_TIME', 'SERVICE_NEXT_CHECK',
		'SERVICE_HAS_BEEN_CHECKED', 'SERVICE_LAST_HARD_STATE_CHANGE', 'SERVICE_LAST_NOTIFICATION',
		'SERVICE_STATE_TYPE', 'SERVICE_IS_FLAPPING', 'SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED',
		'SERVICE_SCHEDULED_DOWNTIME_DEPTH', 'SERVICE_STATUS_UPDATE_TIME', 'SERVICE_EXECUTION_TIME_MIN',
		'SERVICE_EXECUTION_TIME_AVG', 'SERVICE_EXECUTION_TIME_MAX', 'SERVICE_LATENCY_MIN',
		'SERVICE_LATENCY_AVG', 'SERVICE_LATENCY_MAX',
	);
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		
		$data = AppKitFactories::getInstance()->getFactory('IcingaData');
		
		$result = $data->API()->createSearch()
			->setSearchTarget(IcingaApi::TARGET_SERVICE)
			->setResultColumns($this->fields)
			->setSearchFilter('HOST_NAME', $rd->getParameter('hostname'))
			->setSearchFilter('SERVICE_NAME', $rd->getParameter('servicename'))
			->fetch();
		
		$service = null;
		try {
			if ($result->getResultCount() == 1 && $result->service_name) {
				$service = $result->getRow();
				$this->setAttributeByRef('service', $service);
			}
		}
		catch (IcingaApiResultException $e) {
			// Ignore this result!
		}
		
		if ($service) {
			$this->setAttribute('title', 'Details for '. $service->service_name);
		}
		else {
			$this->setAttribute('title', 'Host details');
		}
	}
}

?>