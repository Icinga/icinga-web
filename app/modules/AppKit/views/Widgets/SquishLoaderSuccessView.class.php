<?php

class AppKit_Widgets_SquishLoaderSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		// $this->setupHtml($rd);
		
		$type = $rd->getParameter('type');
		
		// Get the magick
		$response = $this->getContainer()->getResponse();
			
		// Setting some header upon the image type
		$response->clearHttpHeaders();
		
		$response->setHttpHeader('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 3600), true);
		$response->setHttpHeader('Cache-Control', 'public', true);
		$response->setHttpHeader('Age', 10, true);
		$response->setHttpHeader('Pragma', '', true);
		
		switch ($type) {
			case AppKit_SquishFileContainerModel::TYPE_JAVASCRIPT:
				$response->setHttpHeader('Content-type', 'text/javascript', true);	
			break;
			
			case AppKit_SquishFileContainerModel::TYPE_STYLESHEET:
				$response->setHttpHeader('Content-type', 'text/css', true);	
			break;
		}
		
		return $this->getAttribute('content');
	}
}

?>