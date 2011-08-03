<?php

class AppKit_Login_SilentAuthSuccessView extends AppKitBaseView {
    public function executeJson(AgaviRequestDataHolder $rd) {
        return $this->executeHtml($rd);

    }

    public function executeHtml(AgaviRequestDataHolder $rd) {

        if ($this->getAttribute('authenticated', false) == true) {

            $url = $this->getContext()->getRequest()->getUrl();

            //			$routes = $this->getContext()->getRequest()->getAttribute(
            //				'matched_routes', 'org.agavi.routing'
            //			);
            //
            //			$route = $this->getContext()->getRouting()->getRoute(array_pop($routes));

            if (preg_match('/\/login/', $url)) {
                $url = $this->getContext()->getRouting()->gen('index_page');
            }

            $this->getResponse()->setRedirect($url);

        } else {
            if (AgaviConfig::get('modules.appkit.auth.behaviour.enable_dialog', false) == true) {
                return $this->createForwardContainer('AppKit', 'Login.AjaxLogin', null, null, 'write');
            }
        }

        $this->setupHtml($rd);
    }

}

?>