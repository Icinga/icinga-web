<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Cronks_System_StatusSummaryModel extends ICINGACronksBaseModel
{

	private $api = false;

	private $dataStates = array (
		'host'		=> array (
			0	=> 'UP',
			1	=> 'DOWN',
			2	=> 'UNREACHABLE',
			10	=> 'NOT OK',
			20	=> 'All',
		),
		'hostchart'	=> array (
			0	=> 'UP',
			1	=> 'DOWN',
			2	=> 'UNREACHABLE',
			20	=> 'All',
		),
		'service'	=> array (
			0	=> 'OK',
			1	=> 'WARNING',
			2	=> 'CRITICAL',
			3	=> 'UNKNOWN',
			10	=> 'NOT OK',
			20	=> 'All',
		),
		'servicechart'	=> array (
			0	=> 'OK',
			1	=> 'WARNING',
			2	=> 'CRITICAL',
			3	=> 'UNKNOWN',
			20	=> 'All',
		),
	);

	private $dataSources = array (
		'host'			=> array (
			'target'		=> IcingaApi::TARGET_HOST_STATUS_SUMMARY,
			'column'		=> 'HOST_STATE',
		),
		'hostchart'		=> array (
			'target'		=> IcingaApi::TARGET_HOST_STATUS_SUMMARY,
			'column'		=> 'HOST_STATE',
			'title'			=> 'Hosts',
		),
		'service'		=> array (
			'target'		=> IcingaApi::TARGET_SERVICE_STATUS_SUMMARY,
			'column'		=> 'SERVICE_STATE',
		),
		'servicechart'	=> array (
			'target'		=> IcingaApi::TARGET_SERVICE_STATUS_SUMMARY,
			'column'		=> 'SERVICE_STATE',
			'title'			=> 'Services',
		),
	);

	private $typeNames = array (
		'host'			=> 'Hosts',
		'service'		=> 'Services',
		'hostchart'		=> 'Hosts',
		'servicechart'	=> 'Services',
	);

	private $type = false;
	private $dataTmp = array();
	private $data = false;
	private $countNotOk = 0;
	private $countAll = 0;

	public function __construct () {
		$this->api = AppKitFactories::getInstance()->getFactory('IcingaData');
	}

	public function init ($type) {
		if (array_key_exists($type, $this->dataSources)) {
			$this->type = $type;
		} else {
			$this->type = false;
		}
		$this->dataTmp = array();
		$this->data = false;
		$this->countAll = 0;
		$this->countNotOk = 0;
		return $this;
	}

	private function addData ($state, $count) {
		$count = (int)$count;
		$this->dataTmp[$state] = $count;
		if ($state != 0) {
			$this->countNotOk += $count;
		}
		$this->countAll += $count;
		return $this;
	}

	private function getStatusDataCollection ($type, $state, $count = 0) {
		$data = array (
			'state_id'		=> $state,
			'state_name'	=> $this->dataStates[$type][$state],
			'count'			=> $count,
			'type'			=> $type,
			'type_name'		=> $this->typeNames[$type],
		);
		return $data;
	}

	public function getStatusData () {
		if ($this->type !== false) {
			if ($this->data === false) {
				$this->data = array();
				foreach ($this->dataStates[$this->type] as $stateId => $stateName) {
					switch ($stateId) {
						case 10:
							$stateCount = $this->countNotOk;
							break;
						case 20:
							$stateCount = $this->countAll;
							break;
						default:
							$stateCount = (array_key_exists($stateId, $this->dataTmp)) ? $this->dataTmp[$stateId] : 0;
							break;
					}
					$data = $this->getStatusDataCollection($this->type, $stateId, $stateCount);
//					if ($this->type == 'host' || $this->type == 'service') {
						array_push($this->data, $data);
//					} else {
//						$this->data[$data['state_name']] = $data['count'];
//					}
				}
				if ($this->type != 'host' && $this->type != 'service') {
					$this->data['type'] = $this->dataSources[$this->type]['title'];
					$this->data = array($this->data);
				}
			}
		}
		return $this->data;
	}

	public function fetchData () {
		if ($this->type !== false) {
			$result = $this->api->API()->createSearch()
				->setSearchTarget($this->dataSources[$this->type]['target'])
				->fetch();
			foreach ($result as $row) {
				$this->addData($row->{$this->dataSources[$this->type]['column']}, $row->COUNT);
			}
		}
		return $this;
	}

}

?>