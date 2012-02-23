<?php

class IcingaDoctrineDatabase extends AppKitDoctrineDatabase {

    const CONNECTION_ICINGA = 'icinga';

    private $use_retained = false;
    
    /**
     * When working with icinga objects and multiple addon databases
     * this method ensures that you're working on the right space!
     */
    public static function resetCurrentConnection() {
        Doctrine_Manager::getInstance()->setCurrentConnection(self::CONNECTION_ICINGA);
    }
    
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
