<?php

class AppKit_Widgets_AddHeaderDataSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
		
		// not needed
		// $this->setAttribute('title', 'Widgets.AddHeaderData');
		
		AppKitEventDispatcher::getInstance()->triggerSimpleEvent(
			'appkit.headerdata.publish',
			'Last change to add some header data',
			$this->getContext()
		);
		
		$header = $this->getContext()->getModel('HeaderData', 'AppKit');
		
		$this->setAttribute('css_files', $header->getCssFiles());
		$this->setAttribute('css_raw', $header->getCssData());
		
		$this->setAttribute('js_files', $header->getJsFiles());
		$this->setAttribute('js_raw', $header->getJsData());
		
		$this->setAttribute('meta_tags', $header->getMetaTags());
		
	}
}

?>