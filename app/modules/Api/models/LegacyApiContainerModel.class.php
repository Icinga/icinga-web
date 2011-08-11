<?php
class LegacyApiFactory {

}

/**
 * Api wrapping agavi model
 *
 * Provides access to preconfigured IcingaApi
 * @deprecated
 * @author mhein
 * @package icinga-web
 * @subpackage icinga
 */
class Api_LegacyApiContainerModel extends IcingaWebBaseModel
    implements AgaviISingletonModel {
    /**
     *
     * @var IcingaApiConnection
     */
    private $apiData		= null;

    public function initialize(AgaviContext $c, array $p=array()) {
        parent::initialize($c, $p);
        $this->apiData = $this->getContext()->getModel("Store.LegacyLayer.IcingaApi","Api",array("connection"=>"icinga"));
    }

    /**
    * Returns the initiated ApiConnecton
    * @return IcingaApiConnection
    * @author mhein
    */
    public function getConnection() {
        return $this;
    }

    /**
     * Same as getConnection, old style
     * @see Web_Icinga_ApiContainerModel::getConnection()
     * @return IcingaApiConnection
     * @author mhein
     */
    public function API() {
        return $this;//->getConnection();
    }

    /**
     * Abstracts the API->getConnection(...)->createSearch(ICINGA::...)
     * to a api bound method
     * @return IcingaApiSearch
     * @author mhein
     */
    public function createSearch() {

        return $this->getContext()->getModel("Store.LegacyLayer.IcingaApi","Api",array("connection"=>"icinga"));
        ;
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


