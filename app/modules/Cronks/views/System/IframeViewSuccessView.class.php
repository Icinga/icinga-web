<?php

class Cronks_System_IframeViewSuccessView extends CronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Cronks.IframeView');
		
		$url = $rd->getParameter('url');
		
		if ($rd->getParameter('user') && $rd->getParameter('password')) {
			$url = preg_replace('@:\/\/@', sprintf('://%s:%s@', $rd->getParameter('user'), $rd->getParameter('password')), $url, 1);
		}
		
		$this->setAttribute('url', $url);
	}
}

?>