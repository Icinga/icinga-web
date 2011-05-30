<?php
abstract class IcingaApiDoctrineRecord extends Doctrine_Record {

    public function __construct($table = null, $isNewEntry = false) {
        $dbalmm = AgaviContext::getInstance()->getModel("Api_DBALMetaManagerModel","Api");
        $db = $dbalmm->getCurrentDB();
        Doctrine_Manager::getInstance()->bindComponent($this->get, $db);
        parent::__construct($table, $isNewEntry);
    }
}
?>
