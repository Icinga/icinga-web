<?php

class IcingaDataPrincipalTarget extends AppKitPrincipalTarget {
	
	protected $api_mapping_fields = array ();
	
	public function getApiMappingFields() {
		return $this->api_mapping_fields;
	}
	
	protected function setApiMappingFields(array $a) {
		$this->api_mapping_fields = $a;
	}
	
	public function getApiMappingField($field) {
		if (array_key_exists($field, $this->api_mapping_fields)) {
			return $this->api_mapping_fields[$field];
		}
		
		return null;
	}
	
}

?>