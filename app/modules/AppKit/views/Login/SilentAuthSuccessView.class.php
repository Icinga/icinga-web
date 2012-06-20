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


class AppKit_Login_SilentAuthSuccessView extends AppKitBaseView {
    public function executeJson(AgaviRequestDataHolder $rd) {
        return $this->executeHtml($rd);

    }

    public function executeHtml(AgaviRequestDataHolder $rd) {

        if ($this->getAttribute('authenticated', false) == true) {

            $url = $this->getContext()->getRequest()->getUrl();

            //          $routes = $this->getContext()->getRequest()->getAttribute(
            //              'matched_routes', 'org.agavi.routing'
            //          );
            //
            //          $route = $this->getContext()->getRouting()->getRoute(array_pop($routes));

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