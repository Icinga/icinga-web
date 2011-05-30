<?php

class Cronks_System_ObjectSearchSuccessView extends CronksBaseView {

    /**
     * Web_Icinga_Cronks_ObjectSearchResultModel
     * @var Web_Icinga_Cronks_ObjectSearchResultModel
     */
    private $model = null;

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Icinga.Cronks.ObjectSearch');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {

        $this->model = $this->getContext()->getModel('System.ObjectSearchResult', 'Cronks');
        $this->model->setQuery($rd->getParameter('q'));

        if ($rd->getParameter('t')) {
            $this->model->setSearchType($rd->getParameter('t'));
        }

        $data = $this->model->getData();

        return json_encode($data);

    }
}

?>