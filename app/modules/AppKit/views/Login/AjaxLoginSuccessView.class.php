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


class AppKit_Login_AjaxLoginSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        if ($this->getContext()->getUser()->isAuthenticated() !== true) {
            $this->getResponse()->setHttpStatusCode('403');
        }

        $this->setAttribute('message', false);
        $message = AgaviConfig::get('modules.appkit.auth.message', false);

        if ($rd->has('cookies', 'icinga-web-loginname') && AgaviConfig::get('modules.appkit.auth.behaviour.store_loginname', false)===true) {
            $this->setAttribute('username', $rd->get('cookies', 'icinga-web-loginname'));
        }

        if ($message !== false && is_array($message)) {
            if (isset($message['show']) && $message['show']==true) {

                if (isset($message['include_file']) && file_exists($message['include_file'])) {
                    $text = file_get_contents($message['include_file']);
                } else {
                    $text = isset($message['text']) ? $message['text'] : null;
                }

                if ($text) {
                    $text = AppKitAgaviUtil::replaceConfigVars($text);
                }

                $this->setAttribute('message', true);
                $this->setAttribute('message_text', $text);
                $this->setAttribute('message_title', $message['title']);
                $this->setAttribute('message_expand_first', isset($message['expand_first']) ? (bool)$message['expand_first'] : false);
            }
        }
    }

    public function executeJson(AgaviRequestDataHolder $rd) {

        $authenticated = false;
        $errors = array();
        $user = $this->getContext()->getUser();

        if ($this->getAttribute('authenticated', false) === true && $user->isAuthenticated() && $this->getAttribute('executed', false) === true) {
            $authenticated = true;
        } else {
            $errors['username'] = 'Login failed!';
            $this->getResponse()->setHttpStatusCode('403');
        }

        return json_encode(array(
                               'success'        => $authenticated,
                               'errors'     => $errors
                           ));

    }
}

?>