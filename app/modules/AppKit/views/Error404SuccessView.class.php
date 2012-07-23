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


class AppKit_Error404SuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setAttribute('_title', '404 Not Found');

        $this->setupHtml($rd);

        $this->getResponse()->setHttpStatusCode('404');
    }

    public function executeConsole(AgaviRequestDataHolder $rd) {
        $val = $this->getContainer()->getValidationManager();
        foreach($val->getErrors() as $error=>$msg) {
            printf("Error: ".$error." ".$msg."\n");
        }

    }
    
    public function executeJson(AgaviRequestDataHolder $rd) {
        $valmgr = $this->getContainer()->getValidationManager();
        
        $errors = array(
            '404 not found',
            'The requested url '. $this->getContext()->getRequest()->getUrlPath()
            . ' was not found on this server'
        );
        
        foreach ($valmgr->getErrors() as $error=>$msg) {
            $errors[] = $error. ': '. $msg;
        }
        
        return json_encode(array(
            'errors'  => $errors,
            'success' => false
        ));
    }
}