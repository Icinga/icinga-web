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

/**
 * Extender for SQL view templates, add our UserObjectId credential to the 
 * query
 * @author mhein
 * @package IcingaWeb
 * @subpackage Api
 * @since 1.8.0
 */
class Api_Views_Extender_UserObjectIdExtenderModel extends IcingaBaseModel
    implements DQLViewExtender, AgaviISingletonModel {
    
    /**
     * Interface method. Configure our Doctrine UserObjectId filter and
     * modify the query
     * 
     * @param IcingaDoctrine_Query $query
     * @param array $params
     */
    public function extend(IcingaDoctrine_Query $query,array $params) {
        $filter = $this->getContext()->getModel('Filter.UserObjectId', 'Api', array(
            'target_fields' => $params['target_fields']
        ));
        
        $query->addFilter($filter);
    }
}