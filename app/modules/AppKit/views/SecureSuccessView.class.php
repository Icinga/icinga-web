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


class AppKit_SecureSuccessView extends AppKitBaseView {

    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);
    }

    private function sendHeader() {
        $this->getResponse()->setHttpStatusCode('401');
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->sendHeader();
        $this->setAttribute('_title', 'Access Denied');
        $this->setupHtml($rd);
    }


    public function executeJson(AgaviRequestDataHolder $rd) {
        return json_encode(array(
            'success' => false,
            'errors' => array('401 unauthorized')
        ));
    }
}

?>
