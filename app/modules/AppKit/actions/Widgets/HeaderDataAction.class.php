<?php

class AppKit_Widgets_HeaderDataAction extends AppKitBaseAction {

    public function getDefaultViewName() {
        return 'Success';
    }

    public function execute(AgaviRequestDataHolder $rd) {
        $type = $rd->getParameter('type', 'javascript');




        switch ($type) {
            case 'javascript':
                $includes = array(
                                $this->getContext()->getRouting()->gen('modules.appkit.squishloader.javascript'),
                                $this->getContext()->getRouting()->gen('modules.appkit.ext.applicationState', array('cmd' => 'init')),
                                $this->getContext()->getRouting()->gen('modules.appkit.ext.initI18n')
                            );
                break;

            case 'css':
                $includes = array(
                                $this->getContext()->getRouting()->gen('styles.css')
                            );

                $resources = $this->getContext()->getModel('Resources', 'AppKit');

                $imports = $resources->getCssImports();

                $this->setAttribute('imports', $imports);

                break;
        }

        $this->setAttribute('includes', $includes);

        return $this->getDefaultViewName();
    }

}
