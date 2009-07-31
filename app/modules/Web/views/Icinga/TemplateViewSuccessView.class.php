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
		
		$layout_class = $template->getSectionParams('option')->getParameter('layout');
		$layout = AppKitClassUtil::createInstance($layout_class);
		
		$layout->setContainer($this->getContainer());
		$layout->setWorker($worker);
		$layout->setParameters($rd);
		
		return $layout->getLayoutContent();
	}
	
	public function executeJson(AgaviRequestDataHolder $rd)
	{
		$template_file = sprintf(
			'%s/%s.xml', 
			AgaviConfig::get('de.icinga.web.xml_template_folder'), 
			$rd->getParameter('template')
		);
		
		$template = new IcingaTemplateXmlParser($template_file);
		$template->parseTemplate();
		
		$data = array ();
		
		$data['columns'] = $template->getHeaderArray();
		if (!$rd->getParameter('fieldsonly', false)) {
			$worker = new IcingaTemplateWorker();
			$worker->setTemplate($template);
			$worker->setApi(AppKitFactories::getInstance()->getFactory('IcingaData')->API());
			
			if (is_numeric($rd->getParameter('start')) && is_numeric($rd->getParameter('limit'))) {
				$worker->setResultLimit($rd->getParameter('start'), $rd->getParameter('limit'));
			}
			
			$worker->buildAll();
			
			$data['resultCount'] = $worker->countResults();
			$data['resultRows'] = $worker->fetchDataArray();
			 
		}
		
		return json_encode($data);
	}
}

?>