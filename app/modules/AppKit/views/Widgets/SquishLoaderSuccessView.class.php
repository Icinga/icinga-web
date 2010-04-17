<?php

class AppKit_Widgets_SquishLoaderSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		// $this->setupHtml($rd);
		
		$model =& $this->getAttribute('model');
		
		if ($model == null) {
			return;
		}
		
		// Get the magick
		$response = $this->getContainer()->getResponse();
			
		// Setting some header upon the image type
		$response->clearHttpHeaders();
		
		$response->setHttpHeader('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 3600), true);
		$response->setHttpHeader('Cache-Control', 'public', true);
		$response->setHttpHeader('Age', 10, true);
		$response->setHttpHeader('Pragma', '', true);
		
		switch ($model->getType()) {
			case AppKit_SquishFileContainerModel::TYPE_JAVASCRIPT:
				$response->setHttpHeader('Content-type', 'text/javascript', true);	
			break;
			
			case AppKit_SquishFileContainerModel::TYPE_STYLESHEET:
				$response->setHttpHeader('Content-type', 'text/css', true);	
			break;
		}
		
		if ($this->getAttribute('errors', false)) {
			return "throw '". join(", ", $this->getAttribute('errors')). "';";
		}
		else {
			$content = $model->getContent();
			$content .= $this->executeActions($model->getActions());
			return $content;
		}
	}
	
	private function executeActions(array $actions = array()) {
		$out = null;
		foreach ($actions as $a) {
			$p = array ();
			if (is_array($a['arguments'])) $p = $a['arguments'];
			$a['arguments']['is_slot'] = true;
			$r = $this->createForwardContainer($a['module'], $a['action'], $p, $a['output_type'])
			->execute();
			
			if ($r->hasContent()) {
				$out .= $r->getContent(). "\n\n";
			}
		}
		return $out;
	}
}

?>