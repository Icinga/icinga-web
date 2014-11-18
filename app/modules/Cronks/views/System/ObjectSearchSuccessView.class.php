<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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


class Cronks_System_ObjectSearchSuccessView extends CronksBaseView {

    /**
     * Web_Icinga_Cronks_ObjectSearchResultModel
     * @var Web_Icinga_Cronks_ObjectSearchResultModel
     */
    private $model = null;

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Icinga.Cronks.ObjectSearch');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {

        $this->model = $this->getContext()->getModel('System.ObjectSearchResult', 'Cronks');
        $this->model->setQuery($rd->getParameter('q'));

        if ($rd->getParameter('t')) {
            $this->model->setSearchType($rd->getParameter('t'));
        }

        $data = $this->model->getData();

        return json_encode($data);

    }
}

?>