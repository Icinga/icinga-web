<?php

class AppKit_Widgets_AddHeaderDataSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		$this->setAttribute('title', 'Widgets.AddHeaderData');
		
		$header = $this->getContext()->getModel('HeaderData', 'AppKit');
		
		
		
		$this->setAttribute('css_files', $header->getCssFiles());
		$this->setAttribute('css_raw', $header->getCssData());
		
		$this->setAttribute('js_files', $header->getJsFiles());
		$this->setAttribute('js_raw', $header->getJsData());
		
		$this->setAttribute('meta_tags', $header->getMetaTags());
		
	}
}

?>