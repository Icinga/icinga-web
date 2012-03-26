<?php

class LConf_Interface_Admin_MainSuccessView extends IcingaLConfBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Interface.Admin.Main');
		
		$container = $this->getContainer();
		$EditWindow = new AgaviRequestDataHolder();
		$EditWindow = $container->createExecutionContainer("LConf","Interface.Admin.Principals",$EditWindow,"simple");
		$this->setAttribute("js_editWindow",$EditWindow->execute()->getContent());
	}
}

?>