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


class ModuleNotFoundException extends AppKitException {};

class AppKit_ModuleInstallerModel extends AppKitBaseModel {
    private $versionInDB;
    private $modulename;
    private $isUpdate = true;
    private $db;

    private function getModuleVar($var) {
        return AgaviConfig::get('modules.'.$this->modulename.'.'.$var);
    }

    private function startTransaction() {
        $this->db->beginTransaction();  
    }
    private function commit() {
        $this->db->commit();
    }
    private function rollback() {
        $this->db->rollback();
    }

    private function initDB() {
        $this->db = $this->getContext()->getDatabaseManager()->getDatabase('icinga_web')->getConnection(); 
    }
    
    public function executeInstallation($modulename) {
        $this->modulename = $modulename;
        $version = $this->getModuleVar("version");
        if(!$version)
            throw new ModuleNotFoundException('Module '.$modulename.' couldn\'t be found');
       
        $this->initDB(); 
        $this->startTransaction();
        try {   
            $this->fetchModuleEntryFromDB();
            $this->commit();
        } catch(Exception $e) {
            $this->rollback();  
        }
       
    }

    private function fetchModuleEntryFromDB() {
 
        $query = $db->createQuery();
     
        $this->versionInDB = $query->select('*')
            ->from('ModuleInfo m')
            ->where('m.name = ?',$this->modulename)
            ->execute(null,Doctrine::HYDRATE_RECORD);
        
        if(empty($result)) {
            $this->isUpdate = false;
            $this->createNewModule();    
        }
        $this->checkVersion();
    }

    private function createNewModule() {
        $this->versionInDB = new ModuleInfo(); 
        $this->versionInDB->name = $this->modulename;
        $this->versionInDB->description = $this->getModuleVar("description");
        $this->versionInDB->disabled = 1; // disable first
        $this->versionInDB->version = "0.0.0";
        $this->versionInDB->installed = new Date();
        $this->versionInDB->last_update = new Date(); 
    }

    
}
