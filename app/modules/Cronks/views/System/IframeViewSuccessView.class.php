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


class Cronks_System_IframeViewSuccessView extends CronksBaseView {

    /**
     * @var Cronks_System_IframeUrlModel
     */
    private $url = null;

    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);

        $this->url = $this->getContext()->getModel('System.IframeUrl', 'Cronks');

    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Icinga.Cronks.IframeView');

        $this->url->setBaseUrl($rd->getParameter('url'));

        if ($rd->hasParameter('user') && $rd->hasParameter('password')) {
            $this->url->setUserPassword($rd->getParameter('user'), $rd->getParameter('password'));
        }

        if ($rd->hasParameter('parameterMap')) {
            $this->url->setParamMapArray($rd->getParameter('parameterMap'));
        }

        $this->url->setRequestDataHolder($rd);

        $this->setAttribute('url', (string)$this->url);
    }
}

?>