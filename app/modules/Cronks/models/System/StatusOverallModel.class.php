<?php

class Cronks_System_StatusOverallModel extends CronksBaseModel {

	/**
	 *
	 * @var Web_Icinga_ApiContainerModel
	 */
	private $api	= null;

	public function initialize(AgaviContext $context, array $parameters = array()) {
		parent::initialize($context, $parameters);
		$this->api = $this->getContext()->getModel('Icinga.ApiContainer', 'Web');
	}

	private function getData() {

		$query = $this->api->createSearch()
			->setSearchTarget(IcingaApi::TARGET_HOST_STATUS_SUMMARY)
			->setResultType(IcingaApi::RESULT_ARRAY);

		IcingaPrincipalTargetTool::applyApiSecurityPrincipals($query);

		$host = $query->fetch()->getAll();

		$query = $this->api->createSearch()
			->setSearchTarget(IcingaApi::TARGET_SERVICE_STATUS_SUMMARY)
			->setResultType(IcingaApi::RESULT_ARRAY);

		IcingaPrincipalTargetTool::applyApiSecurityPrincipals($query);

		$service = $query->fetch()->getAll();

		print_r($service);

	}
	
	public function getJson() {
		$this->getData();
	}

}

?>