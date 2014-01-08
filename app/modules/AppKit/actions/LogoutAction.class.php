<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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


class AppKit_LogoutAction extends AppKitBaseAction {
    /**
     * Returns the default view if the action does not serve the request
     * method used.
     *
     * @return     mixed <ul>
     *                     <li>A string containing the view name associated
     *                     with this action; or</li>
     *                     <li>An array with two indices: the parent module
     *                     of the view to be executed and the view to be
     *                     executed.</li>
     *                   </ul>
     */
    public function getDefaultViewName() {
        return 'Success';
    }

    public function execute(AgaviRequestDataHolder $rd) {

        $this->setAttribute('sendHeader', false);
        $this->setAttribute('doRedirect', true);
        
        if ($rd->getParameter('unauthorized', false) == 1) {
            $this->setAttribute('sendHeader', true);
        }
        
        if ($rd->getParameter('logout', false) == 1) {
            
            if ($this->isHttpRelated() == true) {
                $this->setAttribute('httpBasic', true);
                $this->setAttribute('doRedirect', false);
            }
            
            $this->getContext()->getUser()->doLogout();

        }

        return $this->getDefaultViewName();
    }

    public function isSecure() {
        return true;
    }
    
    private function isHttpRelated() {
        $providerName = $this->getContext()->getUser()->getAttribute(AppKitSecurityUser::AUTHPROVIDER_ATTRIBUTE);
        $model = $this->getContext()->getModel('Auth.Dispatch', 'AppKit');
        try {
            $provider = $model->getProvider($providerName);
        } catch(AgaviSecurityException $e) {
            return false;
        }
        
        if ($provider && $provider instanceof AppKit_Auth_Provider_HTTPBasicAuthenticationModel) {
            $this->setAttribute('auth_realm', $provider->getRealm());
            return true;
        }
        
        return false;
    }
}

?>