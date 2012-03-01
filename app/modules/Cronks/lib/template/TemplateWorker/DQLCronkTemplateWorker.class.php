<?php
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
    private $offset = 0;
    private $limit = 0;
    private $order = false;
    private $orderDir = "ASC";
    /**
     *
     * @var IcingaDoctrine_Query
     */
    private $query;

    public function __construct(CronkGridTemplateXmlParser $template, AgaviContext $context) {
        $this->setTemplate($template);
        $this->setContext($context);
        $this->user = $context->getUser()->getNsmUser();
        $view = $this->readDataSourceDefinition();
        $this->parser = $context->getModel("Views.ApiDQLView","Api",array(
            "view" => $view
        ));
        /**
         * @var IcingaDoctrine_Query
         */
        $this->query = $this->parser->getQuery();

    }
    

    public function setTemplate(CronkGridTemplateXmlParser $template) {
        $this->template = $template;
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

    protected function rewriteResultRow($result) {
        foreach($result as $key=>$val) {
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

                    $result[$key] = $this->rewritePerClassMethod($param['model'], $param['method'], $val, $param['arguments'], (array)$raw);
                }
            }
        }

        return $result;
    }

    /**
     * Return the number of result rows.
     * @return integer
     */
    public function countResults() {
        return count($this->result);
    }

    public function setResultLimit($start, $limit) {
        $this->query->limit($limit);
        $this->query->offset($start);
    }

    public function setOrderColumn($column, $direction = 'ASC') {
        $this->query->orderBy($column." ".$direction);
    }

    public function addOrderColumn($column, $direction = 'ASC') {
        $this->query->orderBy($column." ".$direction);
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

        $field = $this->getAliasedTableFromDQL($field);
        $val = str_replace("'","'",$val);
        $this->query->addWhere($field." $operator ".$this->query->getConnection()->quote($val));
    }

    private function getAliasedTableFromDQL($field) {
        $results = array();
        
        if(preg_match_all('/([A-Za-z_\.]+?) AS '.$field.'/i',$this->query->getDql(),$results)) {
            return $results[1][0];

        } else return $field;
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
