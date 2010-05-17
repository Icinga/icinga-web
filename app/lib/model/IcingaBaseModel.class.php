<?php

/**
 * The base model from which all project models inherit.
 */
class IcingaBaseModel extends AgaviModel {
	
	protected $parameters = array ();
	
	public function initialize(AgaviContext $context, array $parameters = array()) {
		parent::initialize($context, $parameters);
		$this->parameters = $parameters;
	}
	
}

?>