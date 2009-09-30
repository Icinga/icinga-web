<?php

class Cronks_System_ViewProcFilterParamsModel extends ICINGACronksBaseModel
{
	
	/**
	 * our params array
	 * @var array
	 */
	private $params_array = array();
	
	/**
	 * Set the params as an array
	 * 
	 * @param array $p
	 * @return boolean
	 */
	public function setParams(array $p) {
		$this->params_array = $p;
		return true;
	}
	
	/**
	 * This apply all parameters to the worker to 
	 * modify IcingaAPI search filters
	 * 
	 * @param IcingaTemplateWorker $template
	 * @return boolean
	 */
	public function applyToWorker(IcingaTemplateWorker &$template) {
		foreach ($this->params_array as $pKey=>$pVal) {
			$m = array();
			if (preg_match('@^(.+)-value$@', $pKey, $m)) {
				
				// Fieldname (xml field name)
				$name = $m[1];
				
				// The value
				$val = $pVal;
				
				// Operator
				$op = array_key_exists($name. '-operator', $this->params_array) 
					? $this->params_array[ $name. '-operator' ] 
					: null;
					
				// Add a template worker condition
				$template->setCondition($name, $val, $op);
			}
		}
		
		return true;
		
	}
}

?>