<?php
class ConsoleInterfaceMock implements IcingaConsoleInterface {
    public function getHostName() {
        return "testmock_host";
    } 
    
    public function getAccessDefinition() {
        return array();
    }
    
    public function exec(IcingaConsoleCommandInterface $c) {
        return true;
    }
}

class CommandDispatcherTest extends PHPUnit_Framework_TestCase {

    public function testModelCreation() {
        $mock = new ConsoleInterfaceMock();
        $model = AgaviContext::getInstance()->getModel("Commands.CommandDispatcher","Api",array(
            "console" => $mock
        ));    
        
    }
}
