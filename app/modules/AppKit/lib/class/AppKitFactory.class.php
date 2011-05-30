<?php

abstract class AppKitFactory extends AgaviParameterHolder implements AppKitFactoryInterface {

    public function initializeFactory(array $parameters=array()) {
        $this->setParameters($parameters);
        return true;
    }

    public function shutdownFactory() {
        return true;
    }

}

?>