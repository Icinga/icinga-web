<?php

class AppKit_Widgets_SquishLoaderSuccessView extends AppKitBaseView
{
	public function executeJavascript(AgaviRequestDataHolder $rd) {
		
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
			$content = $this->getAttribute('javascript_content');
			$content .= 'AppKit.util.Config.add(\'path\', \''. AgaviConfig::get('org.icinga.appkit.web_path'). '\');'. chr(10);
			$content .= 'AppKit.util.Config.add(\'image_path\', \''. AgaviConfig::get('org.icinga.appkit.image_path'). '\');'. chr(10);
			
			$content .= $this->executeActions(
				$this->getAttribute('javascript_actions')
			);
			return $content;
		}
	}
	
	private function executeActions(array $actions = array()) {
		$out = null;

		if (count($actions)==1 && isset($actions[0])) {

			foreach ($actions[0] as $modules) {

				foreach ($modules as $a) {
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

			}

		}
		return $out;
	}
}

?>
