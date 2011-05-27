<?php

class AppKit_MessageQueueAggregatorModel extends AppKitBaseModel
    implements AgaviISingletonModel {

    private $type = 0;

    public function setType($type) {
        $this->type = $type;
        return true;
    }

    public function getMessageItems() {
        return $this->buildMessageItemArray();
    }

    private function buildMessageItemArray() {
        $i=0;
        $out = array();
        $count = $this->getMessageQueue()->count();

        while ($i<=$count && ($item = $this->getMessageQueue()->dequeue())) {
            if ($item->getType() & $this->type) {
                $out[] = $item;
            } else {
                $this->getMessageQueue()->enqueue($item);
            }

            $i++;
        }

        return $out;
    }
}

?>