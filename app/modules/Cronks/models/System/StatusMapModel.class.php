<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Cronks_System_StatusMapModel extends ICINGACronksBaseModel
{

	private $api = false;

	public function __construct () {
		$this->api = AppKitFactories::getInstance()->getFactory('IcingaData');
	}

	public function getParentChildStructure () {

		$hosts = array();
		$hostReferences = array();

		$apiResHosts = $this->api->API()
			->createSearch()
			->setSearchTarget(IcingaApi::TARGET_HOST)
			->setResultColumns(array('HOST_OBJECT_ID', 'HOST_NAME'))
			->fetch();

		$apiResHostParents = $this->api->API()
			->createSearch()
			->setSearchTarget(IcingaApi::TARGET_HOST_PARENTS)
			->fetch();

		foreach ($apiResHosts as $row) {
			$hosts[$row->host_object_id] = array(
					'id'		=> $row->host_object_id,
					'name'		=> $row->host_name,
					'data'		=> array(
						'relation'	=> $row->host_name,
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

		return $this->flattenStructure($hosts);

	}

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

		$hostsFlat = array(
			'id'		=> 0,
			'name'		=> 'Icinga',
			'data'		=> array(
				'relation'	=> 'Icinga Monitoring Process',
			),
			'children'	=> $hostsFlat,
		);

		return $hostsFlat;
	}

}

?>