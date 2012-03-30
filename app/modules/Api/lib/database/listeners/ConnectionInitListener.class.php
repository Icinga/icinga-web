<?php

class ConnectionInitListener extends Doctrine_EventListener {
    private $initConnectionSql = null;
    private $dateFormat = null;
    public function  __construct($dateFormat = null, $initConnectionSQL = null) {
        $this->dateFormat = $dateFormat;
        $this->initConnectionSql = $initConnectionSQL;
    }
    public function postConnect(Doctrine_Event $event)  {
        $invoker = $event->getInvoker();
        if(!$invoker instanceof Doctrine_Connection) {
            AppKitLogger::warn("Couldn't call ConnectionListenerHook, no connection found");
            return;
        }
        if($this->initConnectionSql !== null) {
            AppKitLogger::verbose("Executing connection init command for connection %s : %s",
                $invoker->getName(),
                $this->initConnectionSql
            );
        }
        $invoker->setDateFormat($this->dateFormat);
        $invoker->execute($this->initConnectionSql);
    }
}