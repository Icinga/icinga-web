<?php

class IcingaTemplateDisplay extends AppKitSingleton {

	protected function wrapImagePath($path) {
		return AgaviConfig::get('de.icinga.appkit.web_path'). $path;
	}
	
}

?>