<?php

/**
 * The base model from which all Cronks module models inherit.
 */
class CronksBaseModel extends AppKitBaseModel {
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        IcingaDoctrineDatabase::resetCurrentConnection();
    }
}

?>