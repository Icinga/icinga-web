<?php

class IcingaTemplateDisplayMonitoring extends IcingaTemplateDisplay {

    public static function getInstance() {
        return parent::getInstance(__CLASS__);
    }

    public function serviceStatus($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        return (string)IcingaServiceStateInfo::Create($val)->getCurrentStateAsHtml();
    }

    public function hostStatus($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        return (string)IcingaHostStateInfo::Create($val)->getCurrentStateAsHtml();
    }

    public function icingaConstants($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        $type = $method_params->getParameter('type');
        $ref = new ReflectionClass('IcingaConstantResolver');

        if (($m = $ref->getMethod($type))) {
            if ($m->isPublic() && $m->isStatic()) {
                return  $this->getAgaviTranslationManager()->_($m->invoke(null, $val));
            }
        }

        return $val;
    }

    public function icingaComments($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {

        $instance_id = $row->getParameter($method_params->getParameter('instance_id_field', 'instance_id'), 0);
        $object_id = $row->getParameter($method_params->getParameter('object_id_field', 'object_id'), 0);

        if ($instance_id && $object_id) {
            $res = $this->getApi()->createSearch()
                   ->setResultType(IcingaApi::RESULT_ARRAY)
                   ->setSearchFilter('COMMENT_OBJECT_ID', $object_id, IcingaApi::MATCH_EXACT)
                   ->setSearchFilter('COMMENT_INSTANCE_ID', $instance_id, IcingaApi::MATCH_EXACT)
                   ->setResultColumns(array('COMMENT_ID'))
                   ->setSearchTarget(IcingaApi::TARGET_COMMENT)
                   ->setSearchType(IcingaApi::SEARCH_TYPE_COUNT)
                   ->fetch();

            $row = $res->getRow();

            if ($row['COUNT_COMMENT_ID']>0) {
                $id = sprintf('%s-%d', 'comment-object-id', $object_id);
                return (string)AppKitXmlTag::create('div', $object_id)
                       ->addAttribute('id', $id)
                       ->addAttribute('class', $method_params->getParameter('class', 'icinga-icon-comment icon-24 icinga-link icinga-notext'));
            }
        }

        return null;
    }

    public function icingaDowntimeType($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        static $classes = array(
                              1	=> 'icinga-icon-service',
                              2	=> 'icinga-icon-host'
                          );

        return sprintf('<div class="icon-16 %s"></div>', $classes[$val]);
    }

    public function icingaDowntimeRunning($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        static $classes = array(
                              1	=> 'icinga-icon-status',
                              0	=> 'icinga-icon-status-busy'
                          );

        return sprintf('<div class="icon-16 %s"></div>', $classes[$val]);
    }
}

?>