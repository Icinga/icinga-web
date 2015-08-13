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


class Cronks_Provider_CategoriesSuccessView extends CronksBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Provider.Categories');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        if ($this->hasAttribute('categories')) {
            $categories = $this->getAttribute('categories', array());

            $json = new AppKitExtJsonDocument();
            $json->setAttribute(AppKitExtJsonDocument::ATTR_AUTODISCOVER);
            $json->setMeta(AppKitExtJsonDocument::PROPERTY_ID, 'catid');
            $json->setData($categories);
            $json->setSuccess(true);

            return $json->getJson();
        }
    }
}

?>
