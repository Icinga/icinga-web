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


class AppKit_DataProvider_LanguageProviderSuccessView extends AppKitBaseView {
    public function executeJson(AgaviRequestDataHolder $rd) {
        try {
            $context = $this->getContext();
            $tm = $context->getTranslationManager();
            $locales = $tm->getAvailableLocales();
            $localeList = array();
            foreach($locales as $locale) {
                $id = $locale["identifier"];
                $localeList[] = array(
                                    "id"=> $id,
                                    "description" => $locale["parameters"]["description"],
                                    "isCurrent" => $id = $tm->getCurrentLocaleIdentifier()
                                );
            }
            return json_encode(array("success"=>true,"locales"=>$localeList));

        } catch (Exception $e) {
            $this->getResponse()->setHttpStatusCode(500);
            return json_encode(array("errorMessage" => "An exception occured: ".$e->getMessage(),"isBug"=>true));
        }
    }
}

?>