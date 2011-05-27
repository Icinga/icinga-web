<?php

class AppKit_Admin_PrincipalEditorSuccessView extends AppKitBaseView {
    public function executeSimple(AgaviRequestDataHolder $rd) {
        $this->executeHtml($rd);
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Admin.PrincipalEditor');
        $this->setAttribute('container',$rd->getParameter('container'));
        $pa = $this->getContext()->getModel('PrincipalAdmin', 'AppKit');
        $this->setAttributeByRef('pa', $pa);


    }
}

?>