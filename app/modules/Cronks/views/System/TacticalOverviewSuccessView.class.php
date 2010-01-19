<?php

class Cronks_System_TacticalOverviewSuccessView extends ICINGACronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'System.TacticalOverview');
	}
}

?>