<?php
/**
 * Api wrapping agavi model
 *
 * Provides access to preconfigured IcingaApi
 * @deprecated
 * @author mhein
 * @package icinga-web
 * @subpackage icinga
 */
class Api_DeprecatedApiContainerModel extends IcingaWebBaseModel
    implements AgaviISingletonModel {

    const BROADCAST_KEY = '__ALL__';

    /**
     * Used namespaces to gather config from
     * @var array
     */
    private static $configMap = array(
                                    'modules.web.api.file'					=> 'apiFile',
                                    'modules.web.api.class'					=> 'apiClass',
                                    'modules.web.api.interfaces.data'		=> 'configData',
                                    'modules.web.api.interfaces.command'	=> 'configCmd'
                                );

    /**
     * File there the api resides
     * @var string
     */
    private $apiFile		= null;

    /**
     * Class name
     * @var string
     */
    private $apiClass		= null;

    /**
     * Configuration of the connection
     * @var array
     */
    private $configData		= null;

    /**
     * Configuration of command dispatchers
     * @var array
     */
    private $configCmd		= null;

    /**
     *
     * @var IcingaApiConnection
     */
    private $apiData		= null;

    /**
     * Array of IcingaApiCommandDispatcher
     * @var array
     */
    private $apiDispatcher	= array();

    private $instanceDispatcher = array();

    private $errors = array();

    /**
     * (non-PHPdoc)
     * @see lib/agavi/src/model/AgaviModel#initialize($context, $parameters)
     */
    public function initialize(AgaviContext $c, array $p=array()) {
        parent::initialize($c, $p);

        // We need all settings from configuration here
        $this->mapConfig();

        // Notice about missing IcingaApi
        $this->checkClass();

        $this->initConnection();

        $this->initDispatcher();
    }

    /**
     * Iterates through the dispatcher config space
     * and creates dispatcher objects
     * @return boolean
     * @author mhein
     */
    private function initDispatcher() {
        if (isset($this->configCmd) && is_array($this->configCmd)) {

            $this->instanceDispatcher[self::BROADCAST_KEY] = array();

            foreach($this->configCmd as $key=>$interface) {
                if (array_key_exists('enabled', $interface) && $interface['enabled'] === true) {

                    $config = $interface;
                    unset($config['type']);
                    unset($config['enabled']);

                    $type = AppKit::getConstant($interface['type']);

                    $this->apiDispatcher[$key] = IcingaApiConstants::getCommandDispatcher();
                    $this->apiDispatcher[$key]->setInterface($type, $config);


                    $ikey = null;

                    if (isset($config['broadcast']) && $config['broadcast']==true) {
                        $ikey = self::BROADCAST_KEY;
                    }

                    elseif(isset($config['instance'])) {
                        $ikey = $config['instance'];
                    }

                    if (!isset($this->instanceDispatcher[$ikey])) {
                        $this->instanceDispatcher[$ikey] = array();
                    }

                    $this->instanceDispatcher[$ikey][$key] =& $this->apiDispatcher[$key];
                }
            }
        }

        if (count($this->apiDispatcher) && count($this->instanceDispatcher)) {
            return true;
        }

        // Some notice
        // AgaviContext::getInstance()->getLoggerManager()->logWarning('No command dispatcher configured!');
        // throw new AppKitFactoryException('No command dispatcher was configured');
    }

    /**
     * Initiates the IcingaApiConnection
     * @return boolean
     * @throws IcingaApiException
     * @author mhein
     */
    private function initConnection() {
        $c = $this->configData;

        $type = AppKit::getConstant($c['api_type']);
        // if (!$type) throw new AppKitModelException('Could not get api_type \'%s\' for connection', $c['api_type']);

        $capi = array();
        foreach($c as $ckey=>$cdata) {
            if (strpos($ckey, 'config_') === 0) {
                $capi[ substr($ckey, 7) ] = $cdata;
            }
        }

        $this->apiData = IcingaApiConstants::getConnection($type, $capi);

        return true;
    }

    /**
     * Maps module config to our private class vars
     * @throws AppKitModelException
     * @return boolean
     * @author mhein
     */
    private function mapConfig() {
        foreach(self::$configMap as $setting=>$varname) {
            if (AgaviConfig::has($setting)) {
                $this-> { $varname } = AgaviConfig::get($setting, null);
            } else {
                throw new AppKitModelException('IcingaApi setting \'%s\' not configured', $setting);
            }
        }

        return true;
    }

    /**
     * Check the IcingaApi class, includes
     * the file also
     * @return boolean
     * @throws AppKitModelException
     * @author mhein
     */
    private function checkClass() {
        IcingaApiClassUtil::initialize();
    }

    /**
     * Returns the initiated ApiConnecton
     * @return IcingaApiConnection
     * @author mhein
     */
    public function getConnection() {
        return $this->apiData;
    }

    /**
     * Same as getConnection, old style
     * @see Web_Icinga_ApiContainerModel::getConnection()
     * @return IcingaApiConnection
     * @author mhein
     */
    public function API() {
        return $this->getConnection();
    }

    /**
     * Abstracts the API->getConnection(...)->createSearch(ICINGA::...)
     * to a api bound method
     * @return IcingaApiSearch
     * @author mhein
     */
    public function createSearch(array $args) {
        $a = $args;
        $ref = new ReflectionObject($this->getConnection());

        if ($ref->hasMethod('createSearch')) {
            $m = $ref->getMethod('createSearch');
            return $m->invokeArgs($this->getConnection(), $a);
        }

        throw new IcingaApiException("Could not create search (method not found)");
    }

    /**
     * Checks if command dispatcher exists
     * @return boolean
     * @author mhein
     */
    public function checkDispatcher() {
        return (count($this->apiDispatcher) > 0) ? true : false;
    }

    /**
     * Sends a single IcingaApi command definition
     * @param IcingaApiCommand $cmd
     * @return boolean
     * @author mhein
     */
    public function dispatchCommand(IcingaApiCommand &$cmd) {
        return $this->dispatchCommandArray(array($cmd));
    }

    private function getDispatcherByInstance($instance_name) {
        $out = array();

        if (array_key_exists($instance_name, $this->instanceDispatcher)) {
            $out = $this->instanceDispatcher[$instance_name];
        }

        if ($instance_name !== self::BROADCAST_KEY) {
            $out = (array)$this->instanceDispatcher[self::BROADCAST_KEY] + $out;
        }

        return $out;
    }

    /**
     * Same as ::dispatchCommand(). Sends an array
     * of command definitions
     * @see Web_Icinga_ApiContainerModel::getConnection()
     * @param array $arry
     * @return unknown_type
     * @author mhein
     */
    public function dispatchCommandArray(array $arry) {
        $error = false;

        foreach($arry as $command) {

            $instance_name = $command->getCommandInstance();

            $ds = $this->getDispatcherByInstance($command->getCommandInstance());

            if (!count($ds)) {

                $lerror = sprintf('No dispatcher for instance \'%s\'. Could not send command!', $instance_name);

                $this->errors[] = new IcingaApiCommandException($lerror);
                $error = true;

                AgaviContext::getInstance()->getLoggerManager()
                ->log($lerror, AgaviLogger::ERROR);
            } else {

                foreach($ds as $dk=>$d) {
                    try {
                        $d->setCommands(array($command));
                        $d->send();
                    } catch (IcingaApiCommandException $e) {
                        $this->errors[] = $e;
                        $error = true;

                        $this->log('Command dispatch failed on '. $dk. ': '.  str_replace("\n", " ", print_r($d->getCallStack(), true)), AgaviLogger::ERROR);
                    }

                    $d->clearCommands();
                }

            }
        }

        if ($error === true) {
            throw new IcingaApiCommandException('Errors occured try getLastError to fetch a exception stack!');
        }

        return true;

    }

    public function getLastErrors($flush = true) {
        $err = $this->errors;

        if ($flush) {
            $this->errors = array();
        }

        return $err;
    }

}

?>
