<?php

class Cronks_System_ViewProc_SendCommandSuccessView extends ICINGACronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'System.ViewProc.SendCommand');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
		
		$out = array (
			'success'	=> $this->getAttribute('ok'),
			'errors'	=> array (
				'default'	=> $this->getAttribute('error')
			)
		);
		
		return json_encode($out);
		
	}
}

?>