<?php
/**
* Gives access to the access.xml values. 
* Please make sure to not use the AgaviConfig base class for static access, as php < 5.3 does not
* support late static binding and therefore you will access the methdods of the baseclass
**/
class ApiUnknownHostException extends AppKitException {};
class ApiUnknownInstanceException extends AppKitException {};

final class AccessConfig {
    /**
     * @var        array
     */
    private static $config = array();
    private static $configLoaded = false;
    private static $path = null;
    public static function loadConfig() {
        if(self::$configLoaded)
            return;
        self::$config = include AgaviConfigCache::checkConfig(AgaviToolkit::expandDirectives('%core.module_dir%/Api/config/access.xml'));
        self::$configLoaded = true;
    }

    public static function getDefaultHost() {
        self::loadConfig();
        return self::getHostByName(self::$config["defaultHost"]);
    }

    public static function getDefaultHostname() {
        self::loadConfig();
        return self::$config["defaultHost"];
    }

    public static function getHostByName($hostname) {
        self::loadConfig();
        if(is_array($hostname)) {
           $hosts = array();
           foreach($hostname as $hostEntry) {
               if(isset(self::$config["hosts"][$hostEntry]))
                    $hosts[$hostEntry] = self::$config["hosts"][$hostEntry];
           }
           return $hosts;
        }
        if(!is_string($hostname))
            return $hostname;
        if(isset(self::$config["hosts"][$hostname]))
            return self::$config["hosts"][$hostname];
        throw new ApiUnknownHostException("Unknown host ".$hostname.". You must first define it in your access.xml if you want to use it");
    }

    public static function getHostnameByInstance($instance) {
        self::loadConfig();
        if(isset(self::$config["instances"][$instance]))
            return self::$config["instances"][$instance];
        throw new ApiUnknownInstanceException("Unknown instance ".$instance.". You must first define it in your access.xml if you want to use it");
    }
    
    public static function getHostByInstance($instance) {
        self::loadConfig();
        return self::getHostByName(self::getHostnameByInstance($instance)); 
    }
    
    public static function canRead($file,$host) {
        self::loadConfig();
        $hosts = self::getHostByName($host);
        if(isset($hosts["auth"]))
            $hosts = array($hosts);
        foreach($hosts as $host) {
            if(isset($host["r"][$file]) || isset($host["rw"][$file]))
                continue;
            if(in_array($file,$host["r"]) || in_array($file,$host["rw"])
                || in_array(dirname($file)."/*",$host["r"]) || in_array(dirname($file)."/*",$host["rw"]))
                continue;
            return false;
        }
        return true;
    }
    
    public static function canWrite($file,$host) {
        
        $hosts = self::getHostByName($host);
        if(isset($hosts["auth"]))
            $hosts = array($hosts);
        foreach($hosts as $host) {
            if(isset($host["w"][$file]) || isset($host["rw"][$file]))
                continue;
            if (in_array($file,$host["w"]) || in_array($file,$host["rw"])
                || in_array(dirname($file)."/*",$host["w"]) || in_array(dirname($file)."/*",$host["rw"]))
                continue;
            return false;
        }
        return true;
    }

    public static function canExecute($file,$host) {
        $hosts = self::getHostByName($host);
        if(isset($hosts["auth"]))
            $hosts = array($hosts);
        foreach($hosts as $host) {
            if(isset($host["x"][$file]))
                continue;
            if(in_array($file,$host["x"]) || in_array(dirname($file)."/*",$host["x"]))
                continue;
            return false;
        }
        return true;
    }

    public static function expandSymbol($file,$type,$host) {
        $host = self::getHostByName($host);

        if(isset($host[$type][$file]))
            return $host[$type][$file];

        return $file; 
    }

    public static function fromArray(array $data)
    {
        // array_merge would reindex numeric keys, so we use the + operator
        // mind the operand order: keys that exist in the left one aren't overridden
        self::$config = $data + self::$config;
    }
    public static function getAvailableHosts() {
        self::loadConfig();
        $hostnames = array();
        foreach(self::$config["hosts"] as $name=>$host) {
            $hostnames[] = $name;
        }
        return $hostnames;

    }
    
    public static function toArray()
    {
        self::loadConfig();
        return self::$config;
    }

    public static function clear()
    {
        // doesn't apply as everything is read-only per default  
       
    }


}
