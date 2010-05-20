<?php

class Web_Icinga_HelpSuccessView extends IcingaWebBaseView {
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd, 'slot');

		$this->setAttribute('_title', 'Icinga.Help');
	}
}

?>