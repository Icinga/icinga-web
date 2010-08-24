<?php

class Cronks_System_ViewProcSuccessView extends CronksBaseView {
	
	/**
	 * @var Web_Icinga_ApiContainerModel
	 */
	private $api = null;
	
	public function initialize(AgaviExecutionContainer $container) {
		parent::initialize($container);
		$this->api = $this->getContext()->getModel('Icinga.ApiContainer', 'Web');
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd) {
		$this->setupHtml($rd);

		$template_file = sprintf(
			'%s/%s.xml', 
			AgaviConfig::get('modules.cronks.xml.path.grid'), 
			$rd->getParameter('template')
		);
		
		$template = new IcingaTemplateXmlParser($template_file, $this->getContext());
		$template->parseTemplate();
		
		$worker = new IcingaTemplateWorker();
		$worker->setTemplate($template);
		$worker->setApi($this->api->getConnection());
		
		$layout_class = $template->getSectionParams('option')->getParameter('layout');
		$layout = AppKitClassUtil::createInstance($layout_class);
		
		$layout->setContainer($this->getContainer());
		$layout->setWorker($worker);
		$layout->setParameters($rd);
		
		return $layout->getLayoutContent();
	}
	
	public function executeJson(AgaviRequestDataHolder $rd) {
		$template_file = sprintf(
			'%s/%s.xml', 
			AgaviConfig::get('modules.cronks.xml.path.grid'), 
			$rd->getParameter('template')
		);
		
		$template = new IcingaTemplateXmlParser($template_file, $this->getContext());
		$template->parseTemplate();
		
		$data = array ();

		$worker = new IcingaTemplateWorker();
		$worker->setTemplate($template);
		$worker->setApi($this->api->getConnection());
		$worker->setUser($this->getContext()->getUser()->getNsmUser());
		
		if (is_numeric($rd->getParameter('page_start')) && is_numeric($rd->getParameter('page_limit'))) {
			$worker->setResultLimit($rd->getParameter('page_start'), $rd->getParameter('page_limit'));
		}
		
		if ($rd->getParameter('sort_field', null) !== null) {
			$worker->setOrderColumn($rd->getParameter('sort_field'), $rd->getParameter('sort_dir', 'ASC'));
		}
		
		// Apply the filter to our template worker
		if (is_array($rd->getParameter('f'))) {
			$pm = $this->getContext()->getModel('System.ViewProcFilterParams', 'Cronks');
			$pm->setParams($rd->getParameter('f'));
			$pm->applyToWorker($worker);
		}
		
		$worker->buildAll();

		// var_dump($worker->fetchDataArray());
		
		$data['resultRows'] = $worker->fetchDataArray();
		$data['resultCount'] = $worker->countResults();
		
		// OK hopefully all done
		$data['resultSuccess'] = true; 

		return json_encode($data);
	}
}

?>