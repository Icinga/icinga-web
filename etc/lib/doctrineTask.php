<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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

/**
 * Abstract base class for all tasks accessing the icinga-web doctrine implementation
 */
class doctrineTask extends Task {

    const DB_NAME = 'icinga_web';

    protected $icingaPath = "";
    protected $modelPath = "app/modules/AppKit/lib/database/models/";
    protected $dsn;
    protected $action;
    protected $targetTable;
    protected $dropOnFinish = false;
    public function setAction($action) {
        $this->action = $action;
    }
    
    public function setIcingapath($path) {
        $this->icingaPath = $path;
    }
    public function setModelpath($path) {
        $this->modelPath = $path;
    }
    public function setTargettable($table) {
        $this->targetTable = $table;
    }
    
    public function setDsn($conn) {
        $this->dsn = $conn;
    }
    
    public function init() {
        // include doctrine
        require_once($this->icingaPath."lib/doctrine/lib/Doctrine.php");
        spl_autoload_register("Doctrine::autoload");
        
        // include agavi (needed for configuration)
        require ($this->icingaPath.'lib/agavi/src/agavi.php');
        require ($this->icingaPath.'app/config.php');
        Agavi::bootstrap('production');
        AppKitAgaviContext::buildVersionString();
    }
    public function dropOnFinish($bool = false) {
        $this->dropOnFinish = true;
    }
    public function main() {
        Doctrine_Manager::connection($this->dsn, self::DB_NAME);
        Doctrine::loadModels($this->modelPath."/generated/");
        Doctrine::setModelsDirectory($this->modelPath."/");
        if($this->action == 'dropDB') {
            $this->dropDB();
        } else if($this->action="truncateTable" && $this->targetTable) {
            Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh()->query(
                "truncate table ".$this->targetTable.";"
            );
        } 
        
    }
    
    public function dropDB() {
        Doctrine::dropDatabases(self::DB_NAME);
    }
}
