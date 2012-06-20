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

/**
 * View to create a javascript with all our application state information
 * @author mhein
 *
 */
class AppKit_Ext_ApplicationStateSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);
        return $this->executeJavascript($rd);
    }

    public function executeJavascript(AgaviRequestDataHolder $rd) {

        $out = array(
                   'data'   => ''
               );

        $cmd = $rd->getParameter('cmd', 'read');
        $provider = $this->getContext()->getModel('Ext.ApplicationState', 'AppKit');
        $response = $this->getContainer()->getResponse();

        switch ($cmd) {
            case 'init':
                $data = json_decode($provider->readState());

                if (is_array($data)) {
                    foreach($data as $i=>$v) {
                        $data[$i]->value = addslashes($v->value);
                    }
                }

                $response->setHttpHeader('Content-Type', 'text/javascript', true);

                return 'Ext.onReady(function() { '. chr(10)
                       . 'var d = \''. json_encode($data). '\'; '. chr(10)
                       . ' AppKit.setInitialState((d ? Ext.decode(d) : [])); '. chr(10)
                       . '});';
                break;

            case 'write':
            case 'read':
            default:

                $response->setHttpHeader('Content-Type', 'text/x-json', true);

                if (!$provider->stateAvailable()) {
                    return null;
                }

                $data = json_decode($provider->readState());
                $out['data'] = (array)$data;
                break;
        }

        $out['success'] = true;

        return json_encode($out);

        //      $data = array ();
        //      $cdata = '';
        //
        //      if ($this->getContext()->getUser()->isAuthenticated()) {
        //
        //          $user = $this->getContext()->getUser();
        //
        //          // To debug some session/cookie/user problems
        //          $cdata .= sprintf('// User: %s (id=%d)', $user->getNsmUser()->user_name, $user->getNsmUser()->user_id). chr(10)
        //          . sprintf('// Tstamp: %s', $this->getContext()->getTranslationManager()->_d(time())). chr(10)
        //          . chr(10);
        //
        //          $data = $this->getContext()->getUser()->getPrefVal(AppKitExtApplicationStateFilter::DATA_NAMESPACE, null, true);
        //
        //          if ($data !== null) {
        //              $data = unserialize(base64_decode($data));
        //          }
        //      }
        //
        //      return sprintf(
        //          '%sExt.onReady(function() {'. "\n"
        //          . "\t". 'AppKit.Ext.setAppState(%s);'. "\n"
        //          . '});'. "\n", $cdata, json_encode($data)
        //      );

    }
}

?>
