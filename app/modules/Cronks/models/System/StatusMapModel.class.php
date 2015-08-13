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

/**
 * Data provider for the status map
 *
 * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
 */
class Cronks_System_StatusMapModel extends CronksBaseModel {


    private $connectionName = null;
    private $tm = false;

    private $scriptTemplate = '<a class="icinga-link" \
        subgrid="icinga-service-template:host:%1$s:%2$s">%3$s</a>';

    private $tableRowTemplate = '<tr><td>%s</td><td>%s</td></tr>';
    private $tableTemplate = '<table>%s</table>';

    /**
     * (non-PHPdoc)
     * @see lib/agavi/src/model/AgaviModel#initialize($context, $parameters)
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->tm = $this->getContext()->getTranslationManager();

        $this->connectionName =
            isset($parameters["connection"]) ?
            $parameters["connection"] : "icinga";

    }

    /**
     * fetches hosts ans re-structures them to support processing of parent-child relationships
     * @return  array                                   hosts in parent-child relation
     */
    public function getParentChildStructure() {

        $hosts = array();
        $hostReferences = array();

        $idPrefix = sha1(microtime()) . '-';

        $hostView = $this->getContext()->getModel("Views.ApiDQLView","Api",array(
            "view" => "TARGET_STATUSMAP_HOST",
            "connection" => $this->connectionName
        ));
        // fetch all hosts
        $apiResHosts = $hostView->getResult();

        $hostWithParents = array();
        $row = array(
            'HOST_OBJECT_ID' => 0,
            'HOST_CURRENT_STATE'  => '-1',
            'HOST_NAME'    => 'Icinga Monitoring Process'
        );

        $root = array(
              'id'      => $idPrefix . '-1',
              'name'    => 'Icinga',
              'data'    => array("relation"=>$row),
              'children'    => array(),
          );

        foreach($apiResHosts as $row) {

            if ($row['HOST_IS_PENDING'] == '1') {
                $row['HOST_CURRENT_STATE'] = "99";
            }

            $objectId = $idPrefix . $row['HOST_OBJECT_ID'];

            $hosts[$objectId] = array(
                'id'        => $objectId,
                'name'      => $row['HOST_NAME'],
                'data'      => array("relation"=>$row),
                'children'  => array(),
                'parent' => $row['HOST_PARENT_OBJECT_ID']
            );
            if($row['HOST_PARENT_OBJECT_ID'] != -1) {
                $hostWithParents[$objectId] = $idPrefix.$row['HOST_PARENT_OBJECT_ID'];
            }
        }

        // connect childs to parents
        foreach($hostWithParents as $objectId=>$parentId) {
            if(isset($hosts[$parentId]))
               $hosts[$parentId]["children"][] = &$hosts[$objectId];
            else // if parent node is missing, connect to top level
                $hosts[$objectId]["parent"] = -1;
        }
        // connect top level nodes to root
        foreach($hosts as $obj) {
            if($obj["parent"] == -1)
                $root["children"][] = $obj;
        }

        return $root;

    }

    /**
     * wraps up additional host information in html table
     * @param   array       $hostData               information for a certain host
     * @return  string                              host information as html table
     * @author  Christian Doebler <christian.doebler@netways.de>
     */
    private function getHostDataTable($hostData) {
        $hostTable = null;
        $hostObjectId = false;
        foreach($hostData as $key => $value) {
            if ($key == 'HOST_OBJECT_ID') {
                $hostObjectId = $value;
                continue;
            }

            switch ($key) {
                case 'HOST_CURRENT_STATE':
                    $value = IcingaHostStateInfo::Create($value)->getCurrentStateAsText();
                    break;

                case 'HOST_NAME':
                    $value = sprintf($this->scriptTemplate, $hostObjectId, $value, $value);
                    break;
            }

            $hostTable .= sprintf($this->tableRowTemplate, $this->tm->_($key), $value);
        }
        $hostTable = sprintf($this->tableTemplate, $hostTable);
        return $hostTable;
    }
}
