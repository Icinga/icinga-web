<?php

class Web_Icinga_TemplateViewSuccessView extends ICINGAWebBaseView
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
		
		$worker->buildAll();
		
		
		
		$this->setAttribute('title', 'Icinga.TemplateView');
	}
}

?>