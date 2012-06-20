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
 * @deprecated
 */
class GenericCronkTemplateWorker extends CronkGridTemplateWorker {

    /**
     * @var CronkGridTemplateXmlParser
     */
    private $template        = null;

   

    /**
     * @var IcingaApiConnectionIdo
     */
    private $api            = null;

    /**
     * @var IcingaApiSearchIdo
     */
    private $api_search        = null;

    /**
     * @var IcingaApiSearchIdo
     */
    private $api_count        = null;

    /**
     * Calculated number of results
     * @var integer
     */
    private $result_count    = null;

    /**
     * The size of a page
     * @var integer
     */
    private $pager_limit    = null;

    /**
     * Where the page starts
     * @var integer
     */
    private $pager_start    = null;

    private $sort_orders    = array();

    private $conditions        = array();

    /**
     * User for validating data privileges
     * @var NsmUser
     */
    private $user            = null;

    public function __construct(CronkGridTemplateXmlParser $template, AgaviContext $context, $connection = "icinga") {

        if ($template) {
            $this->setTemplate($template);
        }

        if ($context) {
            $this->setContext($context);
            $this->api = $context->getModel('Icinga.ApiContainer', 'Web')->getConnection();
            $this->user= $context->getUser()->getNsmUser();
        }
    }

    public function setTemplate(CronkGridTemplateXmlParser $template) {
        $this->template = $template;
    }

   

    public function setApi(/*IcingaApi*/ $api) {
        $this->api = $api;
    }

    /**
     * @return CronkGridTemplateXmlParser
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * Returns the icinga data api
     * @return IcingaApiConnectionIdo
     */
    public function getApi() {

        return $this->api;
    }

    public function buildAll() {
        $this->buildDataSource();
    }

    public function fetchDataArray() {
        return $this->getDataAsArray();
    }

    /**
     * Return the number of result rows.
     * @return integer
     */
    public function countResults() {
        $params = $this->getTemplate()->getSectionParams('datasource');

        if ($params->getParameter('countmode', null) === 'none') {
            $this->api_count = null;
            $this->result_count = 0;
        } elseif ($params->getParameter('countmode', null) !== 'simple') {
            if ($this->api_count !== null) {
                $this->api_count->setSearchType(IcingaApiConstants::SEARCH_TYPE_COUNT);

                if (is_array(($fields = $params->getParameter('countfields'))) && count($fields)) {

                    /**
                     * We need to reset the columns here. Count columns differs
                     * from result set columns
                     */
                    $this->api_count->setResultColumns($fields, true);

                    $this->api_count->setResultType(IcingaApiConstants::RESULT_ARRAY);

                    $result  = $this->api_count->fetch();

                    // Try to determine the fields
                    $row = $result->getRow();
                    if ($row !== false) {
                        $fields = array_keys($row);
                        $field = array_shift($fields);
                        $this->result_count = (int)$row[ $field ];
                    }
                }
            }


        }

        return $this->result_count;
    }

    public function setResultLimit($start, $limit) {
        $this->pager_limit = $limit;
        $this->pager_start = $start;
        return true;
    }

    public function setOrderColumn($column, $direction = 'ASC') {
        $this->sort_orders = array();
        return $this->addOrderColumn($column, $direction);
    }

    public function addOrderColumn($column, $direction = 'ASC') {
        if ($this->getApiField($column)) {
            $this->sort_orders[] = array($column, $direction);
            return true;
        }


        return false;

    }

    private function getDataAsArray() {
        if ($this->api_search !== null) {
            $data = array();
            $this->api_search->setResultType(IcingaApiConstants::RESULT_ARRAY);
            $dataSet = $this->api_search->fetch();

            foreach($dataSet as $result) {
                if ($this->result_count === null) {
                    $this->result_count = $result->getResultCount();
                }

                $tmp = $this->rewriteResultRow($result);

                /*
                 * @todo add additional fields and content here
                 */
                $data[] = $tmp;
            }

            return $data;
        }
    }

    

    private function setPrivileges(/*IcingaApiSearchInterface*/ &$search) {
        IcingaPrincipalTargetTool::applyApiSecurityPrincipals($search);
    }


    private function createFilter($preset,$searchGroup,$search) {

        foreach($preset as $type=>$filterElem) {
            if (isset($filterElem["field"])) {
                $searchFilter = $search->createFilter($filterElem["field"],$filterElem["val"],$filterElem["op"]);
                $searchGroup->addFilter($searchFilter);
            } else {
                $type= explode("_",$type);
                $newGroup =  $search->createFilterGroup($type[0]);
                $this->createFilter($filterElem,$newGroup,$search);
                $searchGroup->addFilter($newGroup);
            }
        }

    }

    private function buildDataSource() {
        if ($this->api_search === null) {
            $params = $this->getTemplate()->getSectionParams('datasource');

            $search = $this->getApi()->createSearch();

            // our query target
            $search->setSearchTarget(AppKit::getConstant($params->getParameter('target')));

            if ($params->getParameter('filterPresets')) {
                foreach($params->getParameter('filterPresets') as $type=>$preset) {
                    $searchGroup = $search->createFilterGroup($type);
                    $this->createFilter($preset,$searchGroup,$search);
                    $this->conditions[] = $searchGroup;
                }
            }

            // setting the orders

            // Order by

            // Overwrite default orders
            foreach($this->collectOrders() as $order) {
                $search->setSearchOrder($order[0], $order[1]);
            }

            // Restrictions
            if (is_array($this->conditions) && count($this->conditions) > 0) {
                foreach($this->conditions as $condition) {
                    if ($condition instanceof IcingaApiSearchFilter) {
                        $search->setSearchFilter($condition['field'], $condition['val'], $condition['op']);
                    } else {
                        $search->setSearchFilter($condition);
                    }
                }
            }

            $this->setPrivileges($search);

            // Clone our count query
            if ($params->getParameter('countmode', null) !== 'none') {
                $this->api_count = clone $search;
            }

            // Groupby fields
            $gbf = $this->getGroupByFields();

            if (is_array($gbf) && count($gbf)>0) {
                $search->setSearchGroup($gbf);
            }

            // the result columns
            $search->setResultColumns($this->collectColumns());

            // limits
            if (is_numeric($this->pager_limit) && is_numeric($this->pager_start)) {
                $search->setSearchLimit($this->pager_start, $this->pager_limit);
            }

            elseif($params->getParameter('limit', null)) {
                $search->setSearchLimit(0, $params->getParameter('limit'));
            }

            $this->api_search =& $search;

        }

        return true;
    }

    private function getGroupByFields() {
        static $fields = null;

        if ($fields === null) {
            $db = $this->getTemplate()->getSectionParams('datasource');
            $fields = $db->getParameter('groupby', array());
        }

        return $fields;
    }

    private function collectOrders() {
        $fields = array();

        if (count($this->sort_orders)) {
            foreach($this->sort_orders as $order) {
                $params = $this->getTemplate()->getFieldByName($order[0], 'order');
                $fields[] = array(
                                $params->getParameter('field', $this->getApiField($order[0])),
                                $order[1] ? $order[1] : 'ASC'
                            );
            }
        } else {
            foreach($this->getTemplate()->getFieldKeys() as $key) {
                $params = $this->getTemplate()->getFieldByName($key, 'order');

                if ($params->getParameter('enabled') && $params->getParameter('default')) {
                    $fields[] = array(
                                    $params->getParameter('field', $this->getApiField($key)),
                                    $params->getParameter('order', 'ASC')
                                );
                }
            }
        }

        return $fields;
    }

    private function collectColumns() {
        $fields = array();

        // The regular fields
        foreach($this->getTemplate()->getFieldKeys() as $key) {
            $fields[ $this->getApiField($key) ] = false;
        }

        // Additional fields
        $ds = $this->getTemplate()->getSection('datasource');

        if (isset($ds['additional_fields']) && is_array($ds['additional_fields'])) {
            $fields = array_merge($fields, array_flip($ds['additional_fields']));
        }

        return array_keys($fields);
    }

    private function getAdditionalFilterFields() {
        static $fields = null;

        if ($fields === null) {
            $ds = $this->getTemplate()->getSection('datasource');

            if (isset($ds['additional_filter_fields']) && is_array($ds['additional_filter_fields'])) {
                $fields = $ds['additional_filter_fields'];
            } else {
                $fields = array();
            }
        }

        return $fields;
    }

    /**
     * Add a condition by a defined xml field
     * @param string $field
     * @param mixed $val
     * @param integer $op
     * @return string the id of the condition (for deletion)
     */
    public function setCondition($field, $val, $op = null) {
        if ($op === null) {
            $op = AppKitSQLConstants::SQL_OP_IS;
        }

        $id = 'c-'. AppKitRandomUtil::genSimpleId(15);

        $filter = $this->getTemplate()->getFieldByName($field, 'filter');
        $database = $this->getTemplate()->getFieldByName($field, 'datasource');
        $new_field = null;

        $ff = $this->getAdditionalFilterFields();

        if (array_key_exists($field, $ff) == true) {
            $new_field = $ff[$field];
        }

        elseif($filter->getParameter('field', null)) {
            $new_field = $filter->getParameter('field');
        }
        else {
            $new_field = $database->getParameter('field');
        }

        if (!$new_field) {
            throw new CronkGridTemplateWorkerException('Could not determine the icinga api field for '. $field);
        }

        // Add or replace some asterix within count
        if ($op == AppKitSQLConstants::SQL_OP_CONTAIN || $op == AppKitSQLConstants::SQL_OP_NOTCONTAIN) {
            if (strpos($val, '*') === false) {
                $val = '%'. $val. '%';
            } else {
                $val = str_replace('*', '%', $val);
            }
        }

        $new_op = AppKitSQLConstants::getIcingaMatch($op);

        if ($new_op == false) {
            throw new CronkGridTemplateWorkerException('No existing icinga search match operator found!');
        }

        $this->conditions[ $id ] = array(
                                       'val'    => $val,
                                       'field'    => $new_field,
                                       'op'    => $new_op
                                   );

        return $id;
    }

    public function setUser(NsmUser $u) {
        $this->user =& $u;
    }


}


