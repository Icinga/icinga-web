<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Web_Icinga_ApiSimpleDataProviderSuccessView extends IcingaWebBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'IcingaApiSimpleDataProvider');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {

        // init
        $jsonData = array(
                        'result'	=> array(
                            'count'	=> 0,
                            'data'	=> array(),
                        ),
                    );
        $model = $this->getContext()->getModel('Icinga.ApiSimpleDataProvider', 'Web');

        $srcId = $rd->getParameter('src_id');
        $filter = $rd->getParameter('filter');

        $result = $model->setSourceId($srcId)->setFilter($filter)->fetch();

        $jsonData['result']['data'] = $result;

        // store final count and convert
        $jsonData['result']['count'] = count($jsonData['result']['data']);

        if(($template = $model->getTemplateCode()) !== false) {
            $jsonData['result']['template'] = $template;
        }

        $jsonDataEnc = json_encode($jsonData);

        return $jsonDataEnc;

    }

}

?>