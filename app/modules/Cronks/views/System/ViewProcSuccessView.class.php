<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


class Cronks_System_ViewProcSuccessView extends CronksBaseView {

    /**
     * @var Web_Icinga_ApiContainerModel
     */
    private $api = null;

    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);
        
    }

    private function getTemplateFile(AgaviRequestDataHolder $rd) {
        
        try {
            $modules = AgaviConfig::get("org.icinga.modules",array());
            $fileName = $rd->getParameter('template');
            foreach($modules as $name=>$path) {
                if(file_exists($path."/config/templates/".$fileName.'.xml')) {
                    return AppKitFileUtil::getAlternateFilename($path."/config/templates/",$fileName, '.xml');
                }
            }
            return AppKitFileUtil::getAlternateFilename(AgaviConfig::get('modules.cronks.xml.path.grid'), $fileName, '.xml');
        } catch (AppKitFileUtilException $e) {
            AppKitAgaviUtil::log('Could not find template for '. $rd->getParameter('template'), AgaviLogger::ERROR);
            throw $e;
        }
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {

        $this->setupHtml($rd);

        try {
            $file = $this->getTemplateFile($rd);

            $template = new CronkGridTemplateXmlParser($file->getRealPath(), $this->getContext());
            $template->parseTemplate();

            $worker = CronkGridTemplateWorkerFactory::createWorker($template, $this->getContext());

            $layout_class = $template->getSectionParams('option')->getParameter('layout');
            $layout = AppKitClassUtil::createInstance($layout_class);

            $layout->setContainer($this->getContainer());
            $layout->setWorker($worker);
            $layout->setParameters($rd);
            
            return $layout->getLayoutContent();
        } catch (AppKitFileUtilException $e) {
            return $this->getContext()->getTranslationManager()->_('Sorry, could not find a xml file for %s', null, null, array($rd->getParameter('template')));
        }
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        $data = array();
        
        $jsonResult = new AppKitExtJsonDocument();
        
        try {

            $file = $this->getTemplateFile($rd);

            $template = new CronkGridTemplateXmlParser($file->getRealPath(), $this->getContext());
            $template->parseTemplate();
            $connection = $rd->getParameter("connection","icinga");

            $worker = CronkGridTemplateWorkerFactory::createWorker($template, $this->getContext(), $connection);

            
            if (is_numeric($rd->getParameter('page_start')) && is_numeric($rd->getParameter('page_limit'))) {
                $worker->setResultLimit($rd->getParameter('page_start'), $rd->getParameter('page_limit'));
            } else {
                $user = $this->context->getUser();
                $worker->setResultLimit(
                    0,
                    $user->getPrefVal('org.icinga.grid.pagerMaxItems',
                        AgaviConfig::get('modules.cronks.grid.pagerMaxItems', 25)
                    )
                );
            }


            if ($rd->getParameter('sort_field', null) !== null) {
                $worker->setOrderColumn($rd->getParameter('sort_field'), $rd->getParameter('sort_dir', 'ASC'));
            
                if($rd->getParameter('additional_sort_field',null) !== null) {
                    $worker->addOrderColumn($rd->getParameter('additional_sort_field'), $rd->getParameter('sort_dir', 'ASC'));
                }
            }
            
            // apply json and legacy filters
            $pm = $this->getContext()->getModel('System.ViewProcFilterParams', 'Cronks');
            
            if (is_array($rd->getParameter('f'))) {
                $pm->setParams($rd->getParameter('f'));
            }
            if ($rd->getParameter('filter_json',false)) {
                $pm->setParamsFromJson(json_decode($rd->getParameter('filter_json'),true));

            }
            $pm->applyToWorker($worker);
            
            $worker->buildAll();

            $data = $worker->fetchDataArray();
            $worker->countResults();
            
            $jsonResult->hasFieldBulk(array_fill_keys($template->getFieldKeys(), ""));
            $jsonResult->setSuccess(true);
            $jsonResult->setDefault(AppKitExtJsonDocument::PROPERTY_TOTAL, $worker->countResults());
            $jsonResult->setData($data);

        } catch (AppKitFileUtilException $e) {
            $jsonResult->resetDoc();
            $jsonResult->hasFieldBulk($template->getFieldKeys());
            $jsonResult->setSuccess(true);
            $jsonResult->setDefault(AppKitExtJsonDocument::PROPERTY_TOTAL, 0);
        }

        return (string)$jsonResult;
    }
}
