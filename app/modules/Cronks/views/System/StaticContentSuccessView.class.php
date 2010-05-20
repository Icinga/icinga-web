<?php

/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Cronks_System_StaticContentSuccessView extends CronksBaseView
{

	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);
	}

	/**
	 * retrieves content via model and returns it
	 * @param	AgaviRequestDataHolder		$rd				required by Agavi but not used here
	 * @return	string						$content		generated content
	 * @author	Christian Doebler <christian.doebler@netways.de>
	 */
	public function executeSimple(AgaviRequestDataHolder $rd) {
		
		if ($rd->getParameter('interface', false) == true) {
			return $this->executeHtml($rd);
		}
		
		$templateFile = sprintf(
			'%s/%s.xml', 
			AgaviConfig::get('modules.cronks.xml.path'), 
			$rd->getParameter('template')
		);

		$model = $this->getContext()->getModel('System.StaticContent', 'Cronks');
		$content = $model->getContent($templateFile);

		return sprintf('<div class="%s">%s</div>', 'static-content-container', $content);
	}
}

?>
