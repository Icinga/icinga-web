<?php

class Web_Icinga_Cronks_StatusSummaryModel extends ICINGAWebBaseModel
{

	private $dataStates = array (
		'host'		=> array (
			0 => 'OK',
			1 => 'UNKNOWN',
			2 => 'DOWN',
		),
		'service'	=> array (
			0 => 'OK',
			1 => 'WARNING',
			2 => 'CRITICAL',
			3 => 'UNKNOWN',
		),
	);

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