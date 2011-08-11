<?php

class CronkGridTemplateDisplayTest extends CronkGridTemplateDisplay {

    public static function getInstance() {
        return parent::getInstance(__CLASS__);
    }

    public function Test($val, AgaviParameterHolder $method_params) {
        return $method_params->getParameter('val'). ': '. $val;
    }

}

?>