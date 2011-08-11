<?php

/**
 * Providing information about icinga commands to core
 * @author mhein
 *
 */
class Cronks_System_CommandInfoModel extends CronksBaseModel
    implements AgaviISingletonModel {

    /**
     * @var IcingaApiCommandCollection
     */
    private $commandDispatcher = null;

    public function  initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->commandDispatcher = $context->getModel("Commands.CommandDispatcher","Api");

    }

    /**
     * Returns a json parsable structure of a command
     * @param string $name
     * @return array
     */
    public function getCommandInfo($name) {
        $cmd = $this->commandDispatcher->getCommand($name);
        $result = array(
                      "fields"=>array(),
                      "types" => array(),
                      "tk" => $this->getContext()->getModel("System.CommandSender","Cronks")->genTimeKey()
                  );
        $cmd = $this->commandDispatcher->getCommand($name);
        foreach($cmd["parameters"] as $field) {
            $name = $field["alias"];
            $result["fields"][] = $name;
            $result["types"][$name] = $field;
        }
        return $result;
    }

}
