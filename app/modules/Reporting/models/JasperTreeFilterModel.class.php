<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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


class Reporting_JasperTreeFilterModel extends ReportingBaseModel {

    const TYPE_PROPERTY = 'property';
    const TYPE_DESCRIPTOR = 'descriptor';

    private $__filters = array();

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
    }

    public function addFilter($type, $field, $regex) {
        $this->__filters[] = array(
                                 'type'    => $type,
                                 'field'   => $field,
                                 'regex'   => $regex
                             );
    }

    public function matchDescriptor(JasperResourceDescriptor &$rd) {

        if (count($this->__filters)==0) {
            return true;
        }

        foreach($this->__filters as $filter) {
            $val = null;

            switch ($filter['type']) {
                case self::TYPE_DESCRIPTOR:
                    $val = $rd->getResourceDescriptor()->getParameter($filter['field'], false);
                    break;

                case self::TYPE_PROPERTY:
                    $val = $rd->getProperties()->getParameter($field['field'], false);
                    break;
            }

            if (preg_match($filter['regex'], $val) == false) {
                return false;
            }

        }

        return true;
    }
}

?>