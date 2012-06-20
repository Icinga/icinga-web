<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


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
