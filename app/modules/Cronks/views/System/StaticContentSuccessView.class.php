<?php

class Cronks_System_StaticContentSuccessView extends ICINGACronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$template_file = sprintf(
			'%s/%s.xml', 
			AgaviConfig::get('de.icinga.web.xml_template_folder'), 
			$rd->getParameter('template')
		);
		
		$template = new IcingaTemplateXmlParser($template_file);
		$template->parseTemplate();
		
		$worker = new IcingaTemplateWorker();
		$worker->setTemplate($template);
		$worker->setApi(AppKitFactories::getInstance()->getFactory('IcingaData')->API());
		
		$layout_class = $template->getSectionParams('option')->getParameter('layout');
		$layout = AppKitClassUtil::createInstance($layout_class);
		
		$layout->setContainer($this->getContainer());
		$layout->setWorker($worker);
		$layout->setParameters($rd);
		
		return $layout->getLayoutContent();
	}
}

?>