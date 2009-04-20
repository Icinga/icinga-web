<?php

class AppKit_Ajax_AutoCompleteSuccessView extends NETWAYSAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('title', 'Ajax.AutoComplete');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
		
		$result = $this->getAttribute('json_result', array());
		
		return json_encode(array(
			'ResultSet'	=> array(
				'Count'		=> count($result),
				'Result'	=> $result
			)
		));
		
	}
}

?>