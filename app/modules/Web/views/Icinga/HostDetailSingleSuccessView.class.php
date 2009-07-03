<?php

class Web_Icinga_HostDetailSingleSuccessView extends ICINGAWebBaseView
{
	
	private $fields = array(
		'HOST_ID',
		'HOST_OBJECT_ID',
		'HOST_NAME',
		'HOST_ALIAS',
		'HOST_DISPLAY_NAME',
		'HOST_ADDRESS',
		'HOST_ACTIVE_CHECKS_ENABLED',
		'HOST_CONFIG_TYPE',
		'HOST_IS_ACTIVE',
		'HOST_OUTPUT',
		'HOST_PERFDATA',
		'HOST_CURRENT_STATE',
		'HOST_CURRENT_CHECK_ATTEMPT',
		'HOST_MAX_CHECK_ATTEMPTS',
		'HOST_LAST_CHECK',
		'HOST_LAST_STATE_CHANGE',
		'HOST_CHECK_TYPE',
		'HOST_LATENCY',
		'HOST_EXECUTION_TIME',
		'HOST_NEXT_CHECK',
		'HOST_HAS_BEEN_CHECKED',
		'HOST_LAST_HARD_STATE_CHANGE',
		'HOST_LAST_NOTIFICATION',
		'HOST_STATE_TYPE',
		'HOST_IS_FLAPPING',
		'HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED',
		'HOST_SCHEDULED_DOWNTIME_DEPTH',
		'HOST_STATUS_UPDATE_TIME',
		'HOST_EXECUTION_TIME_MIN',
		'HOST_EXECUTION_TIME_AVG',
		'HOST_EXECUTION_TIME_MAX',
		'HOST_LATENCY_MIN',
		'HOST_LATENCY_AVG',
		'HOST_LATENCY_MAX'
	);
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		
		$data = AppKitFactories::getInstance()->getFactory('IcingaData');
		
		$result = $data->API()->createSearch()
			->setSearchTarget(IcingaApi::TARGET_HOST)
			->setResultColumns($this->fields)
			->setSearchFilter('HOST_NAME', $rd->getParameter('hostname'))
			->setSearchOrder('HOST_NAME')
			->fetch();
		
		$host = null;
		try {
			if ($result->getResultCount() == 1 && $result->host_name) {
				$host = $result->getRow();
				$this->setAttributeByRef('host', $host);
			}
		}
		catch (IcingaApiResultException $e) {
			// Ignore this result!
		}
		
		if ($host) {
			$this->setAttribute('title', 'Details for '. $host->host_name);
		}
		else {
			$this->setAttribute('title', 'Host details');
		}
	}
}

?>