<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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
