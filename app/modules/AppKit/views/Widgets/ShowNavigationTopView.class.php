<?php

class AppKit_Widgets_ShowNavigationTopView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('title', 'Widgets.ShowNavigation');
		
		// Adding the nav container to the output
		$nav = $this->getContext()->getModel('NavigationContainer', 'AppKit');
		$this->setAttribute('container', $nav->getContainer());
		$this->setAttribute('container_iterator', $nav->getContainerIterator());
	}
}

?>