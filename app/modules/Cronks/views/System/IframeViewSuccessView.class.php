<?php

class Cronks_System_IframeViewSuccessView extends CronksBaseView {

    /**
     * @var Cronks_System_IframeUrlModel
     */
    private $url = null;

    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);

        $this->url = $this->getContext()->getModel('System.IframeUrl', 'Cronks');

    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Icinga.Cronks.IframeView');

        $this->url->setBaseUrl($rd->getParameter('url'));

        if ($rd->hasParameter('user') && $rd->hasParameter('password')) {
            $this->url->setUserPassword($rd->getParameter('user'), $rd->getParameter('password'));
        }

        if ($rd->hasParameter('parameterMap')) {
            $this->url->setParamMapArray($rd->getParameter('parameterMap'));
        }

        $this->url->setRequestDataHolder($rd);

        $this->setAttribute('url', (string)$this->url);
    }
}

?>