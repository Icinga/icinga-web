<?php

/**
 * Prints the meta data of a module to screen
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class moduleMetaPrinterTask extends Task {
	protected $ref;
	
	public function setRefid($ref){
		$this->ref = $ref;
	}
	
	public function getManifest() {
		return $this->ref->getReferencedObject($this->getProject());
	}
	
	public function init() {
	}
	
	public function main() {
		$ref = $this->getManifest();
		echo 
"\n*************************************************". 
"\n* Module : \t\t".$ref->getModuleName().
"\n* Version: \t\t".$ref->getModuleVersion().
"\n* ".$ref->getModuleDescription().
"\n*". 
"\n* Author: \t\t".$ref->getModuleAuthor().
"\n* Company: \t\t".$ref->getModuleCompany().
"\n*************************************************\n\n";
		
	}
}