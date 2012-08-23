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


class Cronks_System_CronkLoaderSuccessView extends CronksBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {

        $this->setAttribute('title', 'Icinga.CronkLoader');

        $tm = $this->getContext()->getTranslationManager();

        try {

            $model = $this->getContext()->getModel('Provider.CronksData', 'Cronks');

            $crname = $rd->getParameter('cronk');
            
            /*
             * Allow external initialization of cronk stubs
             */
            $parameters = $rd->getParameter('p', array());
            
            if ($model->hasCronk($crname)) {
                $cronk = $model->getCronk($crname);
                
                if (array_key_exists('ae:parameter', $cronk) && is_array($cronk['ae:parameter'])) {
                    
                    $cronk['ae:parameter'] = $this->rebuildComplexData($cronk['ae:parameter']);
                    
                    $parameters = (array)$cronk['ae:parameter']
                                  + $parameters
                                  + array('module' => $cronk['module'], 'action' => $cronk['action']);
                }

                if (array_key_exists('state', $cronk) && isset($cronk['state'])) {
                    $parameters['state'] = $cronk['state'];
                }

                return $this->createForwardContainer($cronk['module'], $cronk['action'], $parameters, 'simple', 'write');
            } else {
                return $tm->_('Sorry, cronk "%s" not found', null, null, array($crname));
            }
        } catch (Exception $e) {
            return $tm->_('Exception thrown: %s', null, null, array($e->getMessage()));
        }

        return 'Some strange error occured';
    }
    
    /**
     * Decodes complex vars from cronk builder
     * @param array $params
     * @return array
     */
    private function rebuildComplexData(array $params) {
        foreach($params as $key=>&$val) {
            if (is_string($val)) {
                $test = json_decode($val, true);
                if ($test !== null) {
                    $val = $test;
                }
            }
        }
        
        return $params;
    }
}

?>
