<?php

class CronkGridTemplateLayout {

    /**
     * @var AgaviExecutionContainer
     */
    private $container = null;

    /**
     * @var CronkGridTemplateWorker
     */
    private $worker = null;

    /**
     * @var AgaviParameterHolder
     */
    private $params = null;

    public function __construct(AgaviExecutionContainer  $agaviContainer = null) {
        if ($agaviContainer !== null) {
            $this->setContainer($agaviContainer);
        }

        $this->initClass();
    }

    public function setContainer(AgaviExecutionContainer $container) {
        $this->container =& $container;
    }

    /**
     * Returns the agavi execution container
     * @return AgaviExecutionContainer
     */
    protected function getContainer() {
        return $this->container;
    }

    public function setWorker(CronkGridTemplateWorker $worker) {
        $this->worker =& $worker;
    }

    /**
     * Returns the template worker engine
     * @return CronkGridTemplateWorker
     */
    protected function getWorker() {
        return $this->worker;
    }

    public function setParameters(AgaviParameterHolder $rd) {
        $this->params =& $rd;
    }

    /**
     * Returns the request params
     * @return AgaviParameterHolder
     */
    protected function getParameters() {
        return $this->params;
    }

    protected function initClass() {

    }

    public function getLayoutContent() {
        return 'NOT IMPLEMENTED';
    }

    public function createExecutionContainer($module, $action, AgaviRequestDataHolder  &$rd) {
        return $this->getContainer()->createExecutionContainer(
                   $module, $action, $rd
               );
    }
}

class CronkGridTemplateLayoutException extends AppKitException { }

?>