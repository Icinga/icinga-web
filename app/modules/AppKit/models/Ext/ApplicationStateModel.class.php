<?php

class AppKit_Ext_ApplicationStateModel extends ICINGAAppKitBaseModel implements AgaviISingletonModel
{

	const PREFNS = 'de.icinga.ext.appstate';
	
	public function stateAvailable() {
		if ($this->getContext()->getUser()->isAuthenticated()) {
			return true;
		}
		return false;
	}
	
	public function readState() {
		$data = null;
		
		if ($this->stateAvailable()) {
			$data = $this->getContext()->getUser()->getPrefVal(self::PREFNS, null, true);
		}
		
		return $data;
	}
	
	public function writeState($data) {
		if ($this->stateAvailable()) {
			$existing = json_decode($this->readState());
			$data = array_merge((is_array($existing)) ? $existing : array (), json_decode(($data)));
			$this->getContext()->getUser()->setPref(self::PREFNS, json_encode($data), true, true);
		}
	}
	
}

?>