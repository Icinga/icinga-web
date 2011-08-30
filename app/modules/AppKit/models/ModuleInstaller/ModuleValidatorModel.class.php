<?php
// Exception tree
class ModuleValidationException extends AppKitException {}
class ModuleFolderDoesNotExistException extends ModuleValidationException {}
class ModuleFolderPermissionException extends ModuleValidationException {}

class AppKit_ModuleInstaller_ModuleValidatorModel extends AppKitBaseModel {
    protected $modulename;
    protected $dir; 
    protected $moduleDom;
    protected $hasAccessXML = false;
    protected $hasDatabasesXML = false;
    protected $hasMenuXML = false;


    public function validateModule($modulename) {
        $this->modulename = $modulename;
        $this->moduleDom  = new AgaviXmlConfigDomDocument();
        $this->checkModuleFilesystemConsistency();
        $this->checkModuleConfig();
        $this->checkVersion();
        $this->checkIncludes();
    }

    protected function checkModuleFilesystemConsistency() {
        $dir = AgaviToolKit::literalize("%core.module_dir/".$this->modulename);
        if(!is_dir($dir))
            throw new ModuleFolderDoesNotExistException("Module folder for $modulename couldn't be found at ".$dir);
        if(!is_dir($dir."/config"))
            throw new ModuleFolderDoesNotExistException("Config folder for $modulename couldn't be found at ".$dir);
        if(!is_readable($dir."/config/module.xml") && !is_readable($dir."/config/module.xml.in"))
            throw new ModuleFolderPermissionException("Couldn't read module.xml (or module.xml.in)");

        $this->dir = $dir;
 
        // get properties of this module
        if(is_readable($dir."/config/menu.xml"))
            $this->hasMenuXML = true;
        if(is_readable($dir."/config/access.xml"))
            $this->hasAccessXML = true;
        if(is_readable($dir."/config/databases.xml"))
            $this->hasDatabasesXML = true;
    }       
    

    protected function checkModuleConfig() {

    }

    protected function checkVersion() {

    }
    
}
