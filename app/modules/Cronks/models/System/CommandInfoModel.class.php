<?php

class Cronks_System_CommandInfoModel extends CronksBaseModel
implements AgaviISingletonModel
{

	/**
	 * @var IcingaApiCommandCollection
	 */
	private $command_collection = null;

	public function  initialize(AgaviContext $context, array $parameters = array()) {
		parent::initialize($context, $parameters);
		$this->command_collection = IcingaApi::getCommandCollection();
	}
	
	/**
	 * Returns a json parsable structure of a command
	 * @param string $name
	 * @return array
	 */
	public function getCommandInfo($name) { 
		$d = array ();
		
		$d['fields'] = $this->command_collection->getCommandFields($name);
		
		$d['types'] = array ();
		foreach ($d['fields'] as $f) {
			$d['types'][$f] = $this->command_collection->getCommandFieldDefinition($f);
		}
		
		// We need some generic time specific authentification source
		$sender = $this->getContext()->getModel('System.CommandSender', 'Cronks');
		$d['tk'] = $sender->genTimeKey();
		
		return $d;
	}
	
}

?>