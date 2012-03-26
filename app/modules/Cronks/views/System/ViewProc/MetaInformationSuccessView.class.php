<?php

class Cronks_System_ViewProc_MetaInformationSuccessView extends CronksBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Icinga.Templates.MetaInformation');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        try {
            $file = AppKitFileUtil::getAlternateFilename(AgaviConfig::get('modules.cronks.xml.path.grid'), $rd->getParameter('template'), '.xml');
            $template = new CronkGridTemplateXmlParser($file);
            $template->parseTemplate();
            $user = $this->getContext()->getUser()->getNsmUser();
            $data = $template->getTemplateData();
           
            if($user->hasTarget('IcingaCommandRestrictions')) {
                $template->removeRestrictedCommands();
            }
            
            return json_encode(array(
                                   'template'	=> $template->getTemplateData(),
                                   'fields'	=> $template->getFields(),
                                   'keys'		=> $template->getFieldKeys(),
                                   'params'	=> $rd->getParameters(),
                                   'connections' => IcingaDoctrineDatabase::$icingaConnections
                               ));
        } catch (AppKitFileUtilException $e) {
            $msg = 'Could not find template for '. $rd->getParameter('template');
            AppKitAgaviUtil::log('Could not find template for '. $rd->getParameter('template'), AgaviLogger::ERROR);
            return $msg;
        }
    }
    
}

?>