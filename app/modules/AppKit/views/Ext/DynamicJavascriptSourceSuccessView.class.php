<?php

class AppKit_Ext_DynamicJavascriptSourceSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Ext.DynamicJavascriptSource');
	}
	
	public function executeJavascript(AgaviRequestDataHolder $rd) {
		$scripts = AgaviConfig::get('de.icinga.appkit.include_dynamic_javascript');
		
		$script = $rd->getParameter('script');
		
		if (array_key_exists($script, $scripts) && file_exists($scripts[$script])) {
			return AppKitInlineIncluderUtil::getFileContent($scripts[$script]);
		}
		
		return "";
	}
}

?>