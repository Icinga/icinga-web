<?php

/**
 * Listeners that automatically updates objects with state (like hosts, services)
 * to respect pending states
 *
 * @author jmosshammer
 */
class IcingaPendingStateListener extends Doctrine_Record_Listener {
    public function preHydrate(Doctrine_Event $event) {

        $objects = $event->data;
        echo "!";
        print_r($objects);
    }
    public function postHydrate(Doctrine_Event $event) {

        $objects = $event->data;
        echo "!";
        print_r($objects);
    }    
    public function postDqlSelect(Doctrine_Event $event) {
        
        $objects = $event->data;
        echo "!";
        print_r($objects);
    }
}

?>
