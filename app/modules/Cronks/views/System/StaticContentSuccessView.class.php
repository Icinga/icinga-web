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

		try {
			try {
				$file = AppKitFileUtil::getAlternateFilename(AgaviConfig::get('modules.cronks.xml.path.to'), $rd->getParameter('template'), '.xml');
				
				$model = $this->getContext()->getModel('System.StaticContent', 'Cronks', array (
				    'rparam' => $rd->getParameter('p', array ())
				));
				
				$model->setTemplateFile($file->getRealPath());
				
				$content = $model->renderTemplate($rd->getParameter('render', 'MAIN'), $rd->getParameters());
				
				return sprintf('<div class="%s">%s</div>', 'static-content-container', $content);
			}
			catch (AppKitFileUtilException $e) {
				$msg = 'Could not find template for '. $rd->getParameter('template');
				AppKitAgaviUtil::log('Could not find template for '. $rd->getParameter('template'), AgaviLogger::ERROR);
				return $msg;
			}
		}
		catch (Exception $e) {
			return $e->getMessage();
		}

	}
}

?>
