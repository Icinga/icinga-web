<?php

class LConf_Interface_Admin_PrincipalsSuccessView extends IcingaLConfBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Interface.Admin.Principals');
	}
}

?>