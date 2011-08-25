<?php

class IcingaDoctrineDatabase extends AppKitDoctrineDatabase {
    private $use_retained = false;
    public function initialize(AgaviDatabaseManager $databaseManager, array $parameters = array()) {
        parent::initialize($databaseManager, $parameters);
        
        if ($this->getParameter('use_retained')) {
            $this->use_retained = true;
        }
    }
    
    public function useRetained() {
        return $this->use_retained;
    }
}

?>
