<?php

class AppKit_Widgets_SquishLoaderSuccessView extends AppKitBaseView
{
	public function executeJavascript(AgaviRequestDataHolder $rd)
	{
		$model =& $this->getAttribute('model');
		
		if ($model == null) {
			return;
		}
		
		// Get the magick
		$response = $this->getContainer()->getResponse();
		if(AgaviConfig::get('org.icinga.appkit.include_javascript.allowcache')) {
			$response->clearHttpHeaders();
			$response->setHttpHeader('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + (3600*24)), true);
			$response->setHttpHeader('Cache-Control', 'public', true);
			$response->setHttpHeader('Age', 10, true);
			$response->setHttpHeader('Pragma', null, true);
		}
		if ($this->getAttribute('errors', false)) {
			return "throw '". join(", ", $this->getAttribute('errors')). "';";
		}
		else {
			$content = $model->getContent(). chr(10);
			$content .= 'AppKit.c.path = "'. AgaviConfig::get('org.icinga.appkit.web_path'). '";'. chr(10);
			$content .= $this->executeActions($model->getActions());
			return $content;
		}
	}
	
	private function executeActions(array $actions = array()) {
		$out = null;
		foreach ($actions as $a) {
			$p = array ();
			if(!isset($a['arguments']))
				$a['arguments'] = false;
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
