<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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


class Api_ApiSearchErrorView extends IcingaApiBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        return "Invalid Arguments!";
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        $context = $this->getContext();
        $validation = $this->getContainer()->getValidationManager();
        $errorMsg = array("error"=>array());
        foreach($validation->getErrorMessages() as $error) {
            $errorMsg["error"][] =  $error;
        }
        return json_encode($errorMsg);
    }

    public function executeXml(AgaviRequestDataHolder $rd) {
        echo "<?xml version='1.0' encoding='utf-8'?>";
        $validation = $this->getContainer()->getValidationManager();
        foreach($validation->getErrorMessages() as $error) {
            echo "<error><message>".$error['message']."</message></error>";
        }
    }

    public function executeSimple(AgaviRequestDataHolder $rd) {
        echo "Invalid arguments";
    }


    public function executeRest(AgaviRequestDataHolder $rd) {
        echo "Invalid arguments";
    }
}
