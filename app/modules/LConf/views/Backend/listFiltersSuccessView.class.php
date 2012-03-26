<?php

class LConf_Backend_listFiltersSuccessView extends IcingaLConfBaseView
{
	public function executeJson(AgaviRequestDataHolder $rd) {
		$manager = $this->getContext()->getModel('LDAPFilterManager',"LConf");
		return json_encode(array("filters"=>$manager->getFilters()));
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.listFilters');
	}
}

?>