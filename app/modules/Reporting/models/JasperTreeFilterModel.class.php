<?php

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