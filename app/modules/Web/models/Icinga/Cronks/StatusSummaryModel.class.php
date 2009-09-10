<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Web_Icinga_Cronks_StatusSummaryModel extends ICINGAWebBaseModel
{

	private $dataStates = array (
		'host'		=> array (
			0	=> 'OK',
			1	=> 'UNKNOWN',
			2	=> 'DOWN',
			10	=> 'NOT OK',
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
	);

	private $type = false;
	private $dataTmp = array();
	private $data = false;
	private $countNotOk = 0;
	private $countAll = 0;

	public function init ($type) {
		$this->type = $type;
		$this->dataTmp = array();
		$this->data = false;
		$this->countAll = 0;
		$this->countNotOk = 0;
	}

	public function addData ($state, $count) {
		$count = (int)$count;
		$this->dataTmp[$state] = $count;
		if ($state != 0) {
			$this->countNotOk += $count;
		}
		$this->countAll += $count;
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
					array_push($this->data, $data);
				}
			}
		}
		return $this->data;
	}

	public function getStatusDataCollection ($type, $state, $count = 0) {
		$data = array (
			'state_id'		=> $state,
			'state_name'	=> $this->dataStates[$type][$state],
			'count'			=> $count,
			'type'			=> $type,
		);
		return $data;
	}

}

?>