<?php

interface AppKitFactoryInterface {
    public function initializeFactory(array $parameters=array());
    public function shutdownFactory();
}

?>