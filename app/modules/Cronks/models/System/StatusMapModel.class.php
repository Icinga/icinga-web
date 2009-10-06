<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Cronks_System_StatusMapModel extends ICINGACronksBaseModel
{

	private $api = false;
	private $tm = false;

	private $hostResultColumns = array(
		'HOST_OBJECT_ID', 'HOST_NAME', 'HOST_ADDRESS', 'HOST_ALIAS', 'HOST_DISPLAY_NAME', 'HOST_CURRENT_STATE', 'HOST_OUTPUT',
		'HOST_PERFDATA', 'HOST_CURRENT_CHECK_ATTEMPT', 'HOST_MAX_CHECK_ATTEMPTS', 'HOST_LAST_CHECK', 'HOST_CHECK_TYPE',
		'HOST_LATENCY', 'HOST_EXECUTION_TIME', 'HOST_NEXT_CHECK', 'HOST_LAST_HARD_STATE_CHANGE', 'HOST_LAST_NOTIFICATION',
		'HOST_IS_FLAPPING', 'HOST_SCHEDULED_DOWNTIME_DEPTH', 'HOST_STATUS_UPDATE_TIME'
	);

	/**
	 * class constructor
	 * @return	Cronks_System_StatusMapModel			class object
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function __construct () {
		$this->api = AppKitFactories::getInstance()->getFactory('IcingaData');
	}

	/**
	 * (non-PHPdoc)
	 * @see lib/agavi/src/model/AgaviModel#initialize($context, $parameters)
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function initialize(AgaviContext $context, array $parameters = array())
	{
		parent::initialize($context, $parameters);
		$this->tm = $this->getContext()->getTranslationManager();
	}

	/**
	 * fetches hosts ans re-structures them to support processing of parent-child relationships
	 * @return	array									hosts in parent-child relation
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function getParentChildStructure () {

		$hosts = array();
		$hostReferences = array();

		$apiResHosts = $this->api->API()
			->createSearch()
			->setResultType(IcingaApi::RESULT_ARRAY)
			->setSearchTarget(IcingaApi::TARGET_HOST)
			->setResultColumns($this->hostResultColumns)
			->fetch()
			->getAll();

		$apiResHostParents = $this->api->API()
			->createSearch()
			->setSearchTarget(IcingaApi::TARGET_HOST_PARENTS)
			->fetch();

		foreach ($apiResHosts as $row) {
			$hosts[$row['host_object_id']] = array(
					'id'		=> $row['host_object_id'],
					'name'		=> $row['host_name'],
					'data'		=> array(
						'relation'	=> $this->getHostDataTable($row),
					),
					'children'	=> array(),
			);
		}

		foreach ($apiResHostParents as $row) {
			if (!array_key_exists($row->host_child_object_id, $hostReferences)) {
				$hostReferences[$row->host_child_object_id] = $hosts[$row->host_child_object_id];
			}
			unset($hosts[$row->host_child_object_id]);
			if (array_key_exists($row->host_parent_object_id, $hosts)) {
				$hosts[$row->host_parent_object_id]['children'][$row->host_child_object_id] =& $hostReferences[$row->host_child_object_id];
			} elseif (array_key_exists($row->host_parent_object_id, $hostReferences)) {
				$hostReferences[$row->host_parent_object_id]['children'][$row->host_child_object_id] =& $hostReferences[$row->host_child_object_id];
			}
		}

		$hostsFlatStruct = $this->flattenStructure($hosts);

		if (count($hostsFlatStruct) == 1) {
			$hostsFlat = $hostsFlatStruct;
			$icingaProc = array(
				'id'		=> '-1',
				'name'		=> 'Icinga',
				'data'		=> array(
					'relation'	=> 'Icinga Monitoring Process',
				),
				'children'	=> array(),
			);
			array_push($hostsFlat[0]['children'], $icingaProc);
		} else {
			$hostsFlat = array(
				'id'		=> '-1',
				'name'		=> 'Icinga',
				'data'		=> array(
					'relation'	=> 'Icinga Monitoring Process',
				),
				'children'	=> $hostsFlatStruct,
			);
		}

		return $hostsFlat;

	}

	/**
	 * wraps up additional host information in html table
	 * @param	array		$hostData				information for a certain host
	 * @return	string								host information as html table
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function getHostDataTable ($hostData) {
		$hostTable = null;
		foreach ($hostData as $key => $value) {
			if ($key == 'host_object_id') {
				continue;
			}
			$hostTable .= sprintf('<tr><td>%s</td><td>%s</td></tr>', $this->tm->_($key), $value);
		}
		$hostTable = '<table>' . $hostTable . '</table>';
		return $hostTable;
	}

	/**
	 * brings the structure in a more usable format for json
	 * @param	array		$hosts					host data
	 * @return	array								cleaned host data
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	private function flattenStructure ($hosts) {

		$hostsFlat = array();

		foreach ($hosts as $hostId => $hostData) {
			$currentHost = array (
					'id'		=> $hostData['id'],
					'name'		=> $hostData['name'],
					'data'		=> $hostData['data'],
					'children'	=> array(),
			);
			if (count($hostData['children'])) {
				$currentHost['children'] = $this->flattenStructure($hostData['children']);
			}
			array_push($hostsFlat, $currentHost);
		}

		return $hostsFlat;
	}

}

?>