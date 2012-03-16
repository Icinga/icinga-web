<?php

/**
 * Sending commands to icinga from json sources
 * @author mhein
 *
 */
class Cronks_System_CommandSenderModel extends CronksBaseModel {

    const TIME_KEY				= 'V2Pxq9J2GVt1dk6OO0x3'; // Please change this if you need more security!
    const TIME_ALGO				= 'ripemd160';	// Please never change this!!!
    const TIME_VALID			= 5;	// Key is valid 5 minutes

    private $selection			= array();
    private $data				= array();
    private $command			= null;
    private $instances           = array();
    private $timeFields			= array("checktime","endtime","starttime");

    public function  initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
    }

    public function setCommandName($command) {
        $this->command = $command;
    }

    public function setSelection(array $selection) {
        $this->selection = (array)$selection;
    }

    public function setData(array $data) {
        $this->data = $data;
    }

    public function getConsoleInstance($instance) {
        if (!isset($this->instances[$instance])) {
            AppKitLogger::debug("Setting up console for instance %s ",$instance);
            $this->instances[$instance] = $this->getContext()->getModel("Console.ConsoleInterface","Api",
               array(
                   "icingaInstance"=>$instance
                )
            );
        }

        return $this->instances[$instance];

    }

    public function dispatchCommands() {
        $dispatcher = $this->getContext()->getModel("Commands.CommandDispatcher","Api");
        $this->context->getLoggerManager()->log(print_r($this->selection, 1));
        $this->selection = AppKitArrayUtil::uniqueMultidimensional($this->selection);
        $this->context->getLoggerManager()->log(print_r($this->selection, 1));
        AppKitLogger::debug("Trying to send commands, targets: %s , data: %s ",json_encode($this->selection), json_encode($this->data));
        foreach($this->selection as $target) {
            $console = $this->getConsoleInstance($target['instance']);
            $dispatcher->setConsoleContext($console);
            AppKitLogger::debug("Submitting command %s to %s",$this->command,json_encode($target));
            $dispatcher->submitCommand($this->command,array_merge($target,$this->data));
            AppKitLogger::debug("Finished submitting command");
        }

    }

    /**
     * Generate a time key
     * @return string
     */
    public function genTimeKey() {
        $data = strftime('%Y-%d-%H-').(date('i') - (date('i') % self::TIME_VALID));
        $data .= '-'. $this->getContext()->getUser()->getNsmUser()->user_id;
        $data .= '-'. session_id();

        return hash_hmac(self::TIME_ALGO, $data, self::TIME_KEY);
    }

    /**
     * Check the auth agains the input data and the key
     * @param string $command
     * @param string $json_selection
     * @param string $key
     * @return boolean
     */
    public function checkAuth($command, $json_selection, $json_data, $key) {
        $data = $command. '-'. $json_selection. '-'. $json_data;
        $data = utf8_decode($data);
        $test = hash_hmac(self::TIME_ALGO, $data, $this->genTimeKey());

        if ($key === $test) {
            return true;
        }

        return false;
    }



}