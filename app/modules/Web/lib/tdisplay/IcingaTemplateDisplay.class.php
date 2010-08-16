<?php

class IcingaTemplateDisplay extends AppKitSingleton {

	protected function wrapImagePath($path) {
		return AgaviConfig::get('org.icinga.appkit.web_path'). $path;
	}
	
}

?>