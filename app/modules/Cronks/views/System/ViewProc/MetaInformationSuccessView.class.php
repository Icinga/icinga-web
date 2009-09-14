<?php

class Cronks_System_ViewProc_MetaInformationSuccessView extends ICINGACronksBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Icinga.Templates.MetaInformation');
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
		$template_file = sprintf(
			'%s/%s.xml', 
			AgaviConfig::get('de.icinga.web.xml_template_folder'), 
			$rd->getParameter('template')
		);
		
		$template = new IcingaTemplateXmlParser($template_file);
		$template->parseTemplate();
		
		return json_encode(array(
			'template'	=> $template->getTemplateData(),
			'fields'	=> $template->getFields(),
			'keys'		=> $template->getFieldKeys()
		));
	}
}

?>