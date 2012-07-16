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


class Cronks_Provider_CronksAction extends CronksBaseAction {

    /**
     * @var Cronks_Provider_CronksDataModel
     */
    private $cronks = null;

    /**
     * @var NsmUser
     */
    private $user = null;

    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);

        $this->user = $this->getContext()->getUser()->getNsmUser();

        $this->cronks = $this->getContext()->getModel('Provider.CronksData', 'Cronks');
    }

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

    public function executeRead(AgaviParameterHolder $rd) {
        $cronks = $this->cronks->getCronks();

        $this->setAttributeByRef('cronks', $cronks);

        return $this->getDefaultViewName();
    }

    public function executeWrite(AgaviParameterHolder $rd) {
        try {
            if ($rd->getParameter('xaction') == 'write') {
    
                $cronk_record = $this->cronks->createCronkRecord($rd->getParameters());
    
                $cronk_record->save();
            }
    
            elseif($rd->getParameter('xaction') == 'delete') {
                try {
                    $this->cronks->deleteCronkRecord($rd->getParameter('cid'), $rd->getParameter('name'));
                } catch (Exception $e) {
                    $this->appendAttribute('errors', $e->getMessage());
                }
            }
            else {
                $cronks = $this->cronks->getCronks();
    
                $this->setAttributeByRef('cronks', $cronks);
            }
        } catch(AppKitModelException $e) {
            $this->setAttribute('errors', $e->getMessage());
        }

        return $this->getDefaultViewName();
    }

    public function isSecure() {
        return true;
    }

    public function getCredentials() {
        return array('icinga.user');
    }

    public function handleError(AgaviRequestDataHolder $rd) {
        return $this->getDefaultViewName();
    }
}

?>