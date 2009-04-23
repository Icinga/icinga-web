<?php

class AppKit_MyPreferencesModel extends ICINGAAppKitBaseModel implements AgaviISingletonModel
{

	private $editablePreferences	= array();
	
	public function __construct() {
		$this->loadPrefValues();
	}
	
	private function loadPrefValues() {
		$i = new ReflectionClass('AppKitUserPreferences');
		if (count( ($this->editablePreferences = $i->getConstants()) ) < 0) {
			throw new AppKitModelException('Could not load the preference constants!');
		}
		
		return true;
	}
	
	private function getUpdateAttributes(array $in) {
		$out = array();
		foreach ($in as $key=>$val) {
			if (array_search($key, $this->editablePreferences) !== false) {
				$out[$key] = $val;
			}
		}
		
		return $out;
	}
	
	public function updatePreferences(AgaviRequestDataHolder $rd) {
		$update = $this->getUpdateAttributes($rd->getParameters());
		foreach ($update as $pref_key=>$pref_val) {
			$this->getContext()->getUser()->setPref($pref_key, $pref_val);
		}
		
		return true;
	}
	
}

?>