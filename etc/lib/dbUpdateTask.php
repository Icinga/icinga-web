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

require_once(dirname(__FILE__)."/doctrineTask.php");

class dbUpdateTask extends doctrineTask {
    
    
    public function main() {
        parent::main();
        $this->checkIfDBExists();
        $this->checkVersion();
        $altered = $this->updateStructure();
        $this->updateVersion();
    }
    
    public function checkVersion() {
        $vers = Doctrine_Core::getTable("NsmDbVersion")->findBy("vers_id",1)->getFirst();
        if(!$vers instanceof NsmDbVersion) {
            echo "No version number found, proceeding anyway.\n";
            return true;
        }
        $thisVersion = NsmDbVersion::getInitialData();
        $newVersion = $thisVersion[0]["version"];
        if($vers->get("version") >= $newVersion) {
            echo "Current version is equal or higher than the version you provided, no update needed";
            throw new BuildException("No update performed");
        }
        return true;
    }
    
    public function checkIfDBExists() {
        try {
            Doctrine_Manager::connection()->connect();
        } catch(Exception $e) {
            echo "\nCritical error: DB connection failed: ".$e."\n";
            throw new BuildException("Unable to connect to database");
        }
    }
    
    public function updateStructure() {
        echo "\nUpdating DB Structure\n";
        $models = Doctrine::loadModels($this->modelPath,Doctrine_Core::MODEL_LOADING_AGGRESSIVE,"Nsm");
        $allAltered = array();
        foreach(Doctrine::getLoadedModels() as $model) {
            $class = new $model;
            $altered = array();
            //test
            $cols = $class->getTable()->getColumns();
            $tn = $class->getTable()->getTableName();
        
            $conn = Doctrine_Manager::connection();
            foreach($cols as $name=>$columns) {
                try {
                    $conn->export->alterTable($tn,array(
                        add=>array($name=>$columns)
                    ));
                    $altered[] = $name;
                    echo "Added column ".$name." to ".$model."\n";
                } catch(Exception $e) {
                }
            }

            $allAltered[$model] = $altered;
        }   
        return $allAltered;
    }

    protected function updateVersion() {
        $thisVersion = NsmDbVersion::getInitialData();
        $newVersion = $thisVersion[0]["version"];
        
        $vers = Doctrine_Core::getTable("NsmDbVersion")->findBy("vers_id",1)->getFirst();
        if(!$vers instanceof NsmDbVersion) { 
            $vers = new NsmDbVersion();
            $vers->set("vers_id",1);
        }
        $vers->set("version",$newVersion);
        $vers->save();
    }
}
