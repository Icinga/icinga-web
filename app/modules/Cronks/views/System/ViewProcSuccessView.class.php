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

    private function getTemplateFile(AgaviRequestDataHolder $rd) {
        try {
            return AppKitFileUtil::getAlternateFilename(AgaviConfig::get('modules.cronks.xml.path.grid'), $rd->getParameter('template'), '.xml');
        } catch(AppKitFileUtilException $e) {
            AppKitAgaviUtil::log('Could not find template for '. $rd->getParameter('template'), AgaviLogger::ERROR);
            throw $e;
        }
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {

        $this->setupHtml($rd);

        try {
            $file = $this->getTemplateFile($rd);

            $template = new IcingaTemplateXmlParser($file->getRealPath(), $this->getContext());
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
        } catch(AppKitFileUtilException $e) {
            return $this->getContext()->getTranslationManager()->_('Sorry, could not find a xml file for %s', null, null, array($rd->getParameter('template')));
        }
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        $data = array();

        try {

            $file = $this->getTemplateFile($rd);
            $template = new IcingaTemplateXmlParser($file->getRealPath(), $this->getContext());
            $template->parseTemplate();

            $worker = new IcingaTemplateWorker();
            $worker->setTemplate($template);
            $worker->setApi($this->api->getConnection());
            $worker->setUser($this->getContext()->getUser()->getNsmUser());

            if(is_numeric($rd->getParameter('page_start')) && is_numeric($rd->getParameter('page_limit'))) {
                $worker->setResultLimit($rd->getParameter('page_start'), $rd->getParameter('page_limit'));
            }

            if($rd->getParameter('sort_field', null) !== null) {
                $worker->setOrderColumn($rd->getParameter('sort_field'), $rd->getParameter('sort_dir', 'ASC'));
            }

            // Apply the filter to our template worker
            if(is_array($rd->getParameter('f'))) {
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

        } catch(AppKitFileUtilException $e) {
            $data['resultSuccess'] = true;
            $data['resultCount'] = 0;
            $data['resultRows'] = null;
        }

        return json_encode($data);
    }
}

?>