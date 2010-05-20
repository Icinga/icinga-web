<?php

class AppKit_Ext_initI18nSuccessView extends AppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Ext.initI18n');
	}
	
	public function executeJavascript(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
		
		$tm = $this->getContext()->getTranslationManager();
		$locale = $tm->getCurrentLocale();
		
		$files = array ();
		
		$default = $tm->getDefaultDomain();
		$defaults = explode('.', $default);
		
		$translator = $tm->getDomainTranslator($default, AgaviTranslationManager::MESSAGE);
		
		if ($translator instanceof AppKitGettextTranslator) {
			foreach ($translator->getDomainPaths() as $domain=>$path) {
				foreach (AgaviLocale::getLookupPath($tm->getCurrentLocale()->getIdentifier()) as $prefix) {
					$file = sprintf('%s/%s.json', $path, $prefix);
					if (file_exists($file)) {
						$json = file_get_contents($file);
						if (strlen($json)) {
							$sr = array('@\s*\/\*.+?\*\/@s', '@\s*\/\/.+$@m', '@$\s+@m');
							$json = preg_replace($sr, '', $json);
						}
						else {
							$json = '{}';
						}
						
						$files[$domain] = array($prefix, $json); 
						
						continue;
					}
				}
			}
		}
		
		$this->setAttribute('files', $files);
		$this->setAttribute('default', $defaults[1]);
	}
}

?>