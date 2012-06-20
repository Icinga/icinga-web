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