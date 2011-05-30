<?php

class AppKitSoapFilterValidator extends AgaviValidator {
    public function validate() {
        $context = $this->getContext();
        $argument = $this->getArgument();
        $data = $this->getData($argument);

        $result = $this->processData($data);


        /*
        		print_r($data);

        		foreach($data->Map as $items) {
        			$item = array();
        			$itemDescriptor = $items->item;
        			if(!is_array($itemDescriptor))
        				$itemDescriptor = $items;

        			foreach($itemDescriptor as $itemPart) {
        				$item[$itemPart->key] = $itemPart->value;
        			}
        			$result[] = $item;
        		}

        		print_r($result);
        	*/
        $this->export($result);
        return true;
    }

    public function processData($data) {
        if (is_array($data)) {
            return $data;
        }

        while (!is_array($data) && $data->item)  {
            $data = $data->item;
        }

        return $data->item;
    }
}
