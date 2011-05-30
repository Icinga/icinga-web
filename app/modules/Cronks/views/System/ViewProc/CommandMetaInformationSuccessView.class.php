<?php

class Cronks_System_ViewProc_CommandMetaInformationSuccessView extends CronksBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'System.ViewProc.CommandMetaInformation');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {

        $command_name = $rd->getParameter('command');

        $cimod = $this->getContext()->getModel('System.CommandInfo', 'Cronks');

        return json_encode($cimod->getCommandInfo($command_name));
    }
}

?>