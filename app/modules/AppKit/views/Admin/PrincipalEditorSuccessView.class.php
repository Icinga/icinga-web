<?php

class AppKit_Admin_PrincipalEditorSuccessView extends ICINGAAppKitBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Admin.PrincipalEditor');
		
		$pa = $this->getContext()->getModel('PrincipalAdmin', 'AppKit');
		$this->setAttributeByRef('pa', $pa);
		
	}
}

?>