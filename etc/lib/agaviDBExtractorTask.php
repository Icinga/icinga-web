<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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

class agaviDBExtractorTask extends Task {
    protected $src;
    protected $type;
    protected $user;
    protected $pass;
    protected $db;
    protected $host;
    protected $port;
    protected $toRef;
    
    protected static $__SUPPORTED_DRIVERS = array("mysql","pgsql");
    protected static $__EXPERIMENTAL_DRIVERS = array("pgsql");
    public function setToref($refname) {
        $this->toRef = $refname;
    }
    public function getToRef() {
        return $this->toRef;
    }
    
    public function getSrc() {
        return $this->src;
    }
    public function setSrc($src) {
        if(!file_exists($src))
            throw new BuildException("Invalid Database source.".
                "Please check if ".$src." is a valid path to the icinga databases.xml (located at %icinga-web%/app/config/)");

        $this->src = $src;
    }
    
    public function getType() {
        return $this->type;
    }
    public function setType($type) {
        if(!in_array($type,self::$__SUPPORTED_DRIVERS))
            throw new BuildException("Driver ".$type." is not supported for db-initialize. Please check out etc/schema for database creation scripts and setup the database manually.");
        if(in_array($type,self::$__EXPERIMENTAL_DRIVERS))
            echo "\n**************************WARNING***********************".
                 "\n The selected DB driver has only experimental support! ".
                 "\n This means you *could* experience problems.".
                                 "\n If something goes wrong, you can find the sql scripts under etc/schema".
                     "\n Please check https://dev.icinga.org/projects/icinga-web/issues".
                 "\n if you encounter problems and report a bug! Thank you!".
                 "\n********************************************************\n";
        $this->type = $type;                  
    }
    
    public function getUser() {
        return $this->user;
    }   
    public function setUser($user) {
        $this->user = $user;
    }
    
    public function getPass() {
        return $this->pass;
    }
    public function setPass($pass) {
        $this->pass = $pass;
    } 
    
    public function getDb() {
        return $this->db;
    }
    public function setDb($db) {
        $this->db = $db;
    }
    public function getHost() {
        return $this->host;
    }
    public function setHost($host) {
        $this->host = $host;
    }
    public function getPort() {
        return $this->port;
    }
    public function setPort($port) {
        $this->port = $port;
    }
    
    public function main() {
        $xml = simplexml_load_file($this->getSrc());
        if(!$xml instanceof SimpleXMLElement)
            throw new BuildException("An error occured while parsing database.xml!");       

        $xml->registerXPathNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
        $xml->registerXPathNamespace("default","http://agavi.org/agavi/config/parts/databases/1.0");

        $db = $xml->xpath('//default:database[@name="icinga_web"]//ae:parameter[@name="dsn"]');
        if(!$db || count($db)<1)
            throw new BuildException("Could not find database icinga_web in databases.xml");

        $dsn = (String) $db[0];
        $this->splitDSN($dsn);
        $this->buildRef();
    }
    
    public function splitDSN($dsn) {
        $splitReg = "/(.*):\/\/(.*):(.*)@(.*):(.*)\/(.*)/";
        $parts = array();
        preg_match($splitReg,$dsn,$parts);
        if(count($parts) != 7)
            throw new BuildException("Could not parse dsn ".$dsn);
        
        $this->setType($parts[1]);
        if(!$this->getUser())
            $this->setUser($parts[2]);
        if(!$this->getPass())
            $this->setPass($parts[3]);
        $this->setHost($parts[4]);
        $this->setPort($parts[5]);
        $this->setDb($parts[6]);
    }
    
    public function buildRef() {
        $dsn = $this->getType()."://".$this->getUser().":".$this->getPass()."@".$this->getHost().":".$this->getPort()."/".$this->getDb();
    
        $this->project->setProperty($this->getToRef(),$dsn);
    }
}
