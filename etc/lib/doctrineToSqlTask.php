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

require_once(dirname(__FILE__)."/doctrineTask.php");
require_once(dirname(__FILE__)."/dbInitializeTask.php");

class doctrineToSqlTask extends doctrineTask {
    protected $file = 'test.sql';
    protected $rdbms = '';
    
    public function setFile($file) {
        $this->file = $file;
    }
    
    public function setRdbm($rdbms) {
        $this->rdbms = $rdbms;
    }
    
    public function main() {
        
        $order = $this->getInitImportOrder();
        parent::main();
        $conn = Doctrine_Manager::connection($this->rdbms."://fake@fake/127.0.0.1:9876/fake");
        $sql = $this->printSQLHead();
        $sql .= "\n/*           SQL schema defintiion        */\n";
        $sql .= $this->getSchema($conn);
        $sql .= "\n\n/*          Initial data import              */\n ";
        $sql .= $this->getInitImportData($order);
        file_put_contents($this->file."/".$this->rdbms.".sql",$sql);    
    
    }
    
    public function printSQLHead() {
        $sql =  "/*****************************************************/\n".
                "/* Auto generated ".$this->rdbms." SQL Schema file for icinga-web*/\n".
                "/* Creation date: ".date("c")."          */\n".
                "/****************************************************/\n\n";
        return $sql;
    }
    
    public function getSchema($conn) {
        $sql = $conn->export->exportSql($this->modelPath."/");
        $build = '';
        foreach ($sql as $query) {
            $build .= $query.$conn->sql_file_delimiter;
        }
        return $build;
    }

    public function getInitImportData(array $importOrder) {
        $sql = "";
        foreach($importOrder as $insert) {
            $insert = unserialize($insert);
            $record = new $insert["_model_"];
            $tn = $record->getTable()->getTableName();
            unset($insert["_model_"]);
            $sql .="\nINSERT INTO ".$tn." (".implode(",",array_keys($insert)).") VALUES ('".implode("','",array_values($insert))."');";
        }
        return $sql;
    }   
    
    public function getInitImportOrder() {
        // initialize a test db and extract the insert order

        
        $dsn = $this->dsn;
        $testDB = "dbexport_temp";
        $dsntest = preg_replace("/^(.*)\/\w*?$/","$1/".$testDB,$dsn);
        $dbInitTask = new dbInitializeTask();   
    
        $dbInitTask->setDsn($dsntest);
        $dbInitTask->setIcingapath($this->icingaPath);
        $dbInitTask->setModelpath($this->modelPath);
        $dbInitTask->dropOnFinish(true);
        $dbInitTask->setProject($this->getProject());
        $dbInitTask->setLocation($this->getLocation());
        $dbInitTask->perform();
        try {
            $dbInitTask->dropDB();
        } catch(Exception $e) {
            echo "\nDeletion of temporary db dbexport_temp failed. Please remove it manually. \n ".
                 "This is a known bug for postgresql\n";
        }

        return $dbInitTask->insertedData;   
    }
}
