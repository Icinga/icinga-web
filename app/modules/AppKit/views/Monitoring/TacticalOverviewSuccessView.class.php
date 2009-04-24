<?php

class AppKit_Monitoring_TacticalOverviewSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('title', 'Tactical Overview');
	}
}

?>