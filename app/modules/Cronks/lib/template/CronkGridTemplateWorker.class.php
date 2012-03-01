<?php

abstract class CronkGridTemplateWorker {
    
    abstract public function __construct(CronkGridTemplateXmlParser $template, AgaviContext $context);
    
    abstract public function setContext(AgaviContext $context);

    abstract public function setTemplate(CronkGridTemplateXmlParser $template);

    abstract public function getTemplate();

    abstract public function buildAll();

    abstract public function fetchDataArray();

    /**
     * Return the number of result rows.
     * @return integer
     */
    abstract public function countResults();

    abstract public function setResultLimit($start, $limit);

    abstract public function setOrderColumn($column, $direction = 'ASC');

    abstract public function addOrderColumn($column, $direction = 'ASC');

    /**
     * Add a condition by a defined xml field
     * @param string $field
     * @param mixed $val
     * @param integer $op
     * @return string the id of the condition (for deletion)
     */
    abstract public function setCondition($field, $val, $op = null);

    /**
     *
     * TODO: API CALL CHANGE
     * @param IcingaApiResult$result
     * @return ArrayObject
     */
    protected function addAdditionalFieldResults(/*IcingaApiResult*/ $result) {
        $out = new ArrayObject();
        $ds = $this->getTemplate()->getSection('datasource');

        if (isset($ds['additional_fields']) && is_array($ds['additional_fields'])) {
            $row = new ArrayObject($result->getRow());

            foreach($ds['additional_fields'] as $name=>$resname) {
                if ($row->offsetExists($resname)) {
                    $out[ $name ] = $row[ $resname ];
                }
            }

        }

        return $out;
    }

    /**
     * TODO: API CALL CHANGE
     * @param IcingaApiResult $result
     * @return ArrayObject
     */
    protected function rewriteResultRow($result) {
        $row = new ArrayObject($result->getRow());
        $out = new ArrayObject();

        foreach($this->getTemplate()->getFields() as $key=>$field) {

            $meta = $this->getTemplate()->getFieldByName($key, 'display');
            $data = $this->getFieldData($row, $key);

            $out[$key] = $data;

        }

        // Adding additional fields
        $out = array_merge((array)$out, (array)$this->addAdditionalFieldResults($result));

        unset($row);

        $raw = (array)$out;
        foreach($out as $key=>$val) {
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

                    $out[$key] = $this->rewritePerClassMethod($param['model'], $param['method'], $val, $param['arguments'], (array)$raw);
                }
            }
        }

        return $out;
    }

    protected function getFieldData(&$row, $field) {
        $datasource = $this->getTemplate()->getFieldByName($field, 'datasource');

        if ($datasource->getParameter('field')) {
            if (array_key_exists($datasource->getParameter('field'), $row)) {
                return $row[$datasource->getParameter('field')];
            }
        }

        return null;
    }


    protected function rewritePerClassMethod($model, $method, $data_val, array $params = array(), array $row = array()) {
        list($module, $model) = explode('.', $model, 2);
        $modelObject = $this->context->getModel($model, $module);
        return $modelObject->$method($data_val, new AgaviParameterHolder($params), new AgaviParameterHolder($row));
    }

    protected function getApiField($field_name) {
        return $this->getTemplate()->getFieldByName($field_name, 'datasource')->getParameter('field');
    }
}

class CronkGridTemplateWorkerException extends AppKitException { }

?>
