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

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DQLCronkTemplateWorkerclass
 *
 * @author jmosshammer
 */
class DQLCronkTemplateWorker extends CronkGridTemplateWorker {
    private $template;

    private $user;
    private $parser;
    private $resultMap = array();
    private $connection;

    /**
     *
     * @var IcingaDoctrine_Query
     */
    private $query;

    public function __construct(CronkGridTemplateXmlParser $template, AgaviContext $context, $connection = "icinga") {
        $this->setTemplate($template);
        $this->setContext($context);
        $this->user = $context->getUser()->getNsmUser();
        $this->connection = $connection;
        $view = $this->readDataSourceDefinition();
        $source = $template->getSection("datasource");
        
        $this->parser = $context->getModel("Views.ApiDQLView","Api",array(
            "view" => $view,
            "parameters" => isset($source["parameters"]) ? $source["parameters"] : array(),
            "connection" => $connection
        ));
        /**
         * @var IcingaDoctrine_Query
         */
        $this->query = $this->parser->getQuery();

    }
    

    public function setTemplate(CronkGridTemplateXmlParser $template) {
        $this->template = $template;
        $this->fetchResultMapFromTemplate();
    }

    public function getTemplate() {
        return $this->template;
    }

    public function buildAll() {
    }

    public function fetchDataArray() {
        $this->result = $this->parser->getResult();
        foreach($this->result as &$row) {
            $row = $this->rewriteResultRow($row);
        }
        return $this->result;
    }

    private function fetchResultMapFromTemplate() {
        $this->resultMap = array();
        $fields = $this->template->getFields();
        if(is_array($fields)) {
            foreach($fields as $id=>$subValues) {
                if(isset($subValues["datasource"]) &&
                    isset($subValues["datasource"]["field"])) {
                    $rewrite = $subValues["datasource"]["field"];

                    if(!isset($this->resultMap[$rewrite]))
                        $this->resultMap[$rewrite] = array($id);
                    else
                        $this->resultMap[$rewrite][] = $id;
                }
            }
        }
    }

    protected function rewriteResultRow($result) {

        $rewrittenResult = array();
        foreach($result as $key=>$val) {
            $rewrittenResult[$key] = $val;
            if(isset($this->resultMap[$key])) {
                foreach($this->resultMap[$key] as $rewritten) {
                    $rewrittenResult[$rewritten] = $val;
                }
            }
        }
        foreach($rewrittenResult as $key=>$val) {

            $meta = $this->getTemplate()->getFieldByName($key, 'display');
            if (($param = $meta->getParameter('userFunc')) || ($param = $meta->getParameter('phpFunc'))) {
                if (!isset($param['model'])) {
                    continue;
                }

                if ($param['model'] && $param['method']) {
                    if (!array_key_exists('arguments', $param) && !isset($param['arguments'])) {
                        $param['arguments'] = array();
                    } elseif (!is_array($param['arguments'])) {
                        $param['arguments'] = array();
                    }

                    $rewrittenResult[$key] = $this->rewritePerClassMethod($param['model'], $param['method'], $val, $param['arguments'], (array)$rewrittenResult);
                }
            }
        }
        return $rewrittenResult;
    }

    /**
     * Return the number of result rows.
     * @return integer
     */
    public function countResults() {
        $ds = $this->template->getSection("datasource");
        if(!isset($ds["countmode"]) || in_array($ds["countmode"],array("false","none","null")))
            return;
        if($this->connection != "icinga")
            $this->getContext()->getModel("DBALMetaManager","Api")
                ->switchIcingaDatabase($this->connection);
        return $this->query->count();
    }

    public function setResultLimit($start, $limit) {
        $this->query->limit($limit);
        $this->query->offset($start);
    }

    public function setOrderColumn($column, $direction = 'ASC') {
        $this->query->orderBy($this->aliasToColumn($column)." ".$direction);
    }

    public function addOrderColumn($column, $direction = 'ASC') {
        $this->query->addOrderBy($this->aliasToColumn($column)." ".$direction);
    }

    public function aliasToColumn($field) {
        $break = false;
        foreach($this->resultMap as $fieldId=>$vals) {
            foreach($vals as $datasource) {
                if($datasource == $field) {
                    $field = $fieldId;
                    $break = true;
                    break;
                }
            }
            if($break) {
                break;
            }
        }

        return $this->parser->getAliasedTableFromDQL($field);
    }

    
    /**
     * Add a condition by a defined xml field
     * @param string $field
     * @param mixed $val
     * @param integer $op
     * @return string the id of the condition (for deletion)
     */
    public function setCondition($field, $val, $op = null) {
        if($op === null)
            $op = AppKitSQLConstants::SQL_OP_IS;
        $operator = AppKitSQLConstants::getOperator($op);
        
        if ($op == AppKitSQLConstants::SQL_OP_CONTAIN || $op == AppKitSQLConstants::SQL_OP_NOTCONTAIN) {
            if (strpos($val, '*') === false) {
                $val = '%'. $val. '%';
            } else {
                $val = str_replace('*', '%', $val);
            }
        }
        
        
        if($op == AppKitSQLConstants::SQL_OP_IN || $op == AppKitSQLConstants::SQL_OP_NOT_IN) {
            $val = "(".$val.")";
        } else 
            $val = str_replace("'","'",$val);
        $this->parser->addWhere($field, $operator,$val);
        
    }
    
    public function getTemplateFilterField($field) {
        /*
         * Use override field if some special has done in the view we
         * can not filter on it
         */
        $filter = $this->template->getFieldByName($field, 'filter');
        if ($filter->hasParameter('field')) {
            $field = $filter->getParameter('field');
        } else {
            $field = $this->aliasToColumn($field);
        }
        return $field;
    }
    
    public function getDQLQueryObject() {
        return $this->parser->getQuery();    
    }
    
    public function getView() {
        return $this->parser;
    }
    
    private function readDataSourceDefinition() {
        $tpl = $this->getTemplate();
        AppKitLogger::verbose("Reading data definition from template (data : %s)",$tpl->getTemplateData());
        $source = $tpl->getSection("datasource");

        if(!isset($source["target"])) {
            AppKitLogger::fatal("Invalid template: Can't find datasource target!");
            throw new AgaviException("Invalid template, no datasource target given");
        }
        $target = $source["target"];

        return $target;
        
    }

}
