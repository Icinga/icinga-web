<?php

class Cronks_System_ViewProcFilterParamsModel extends ICINGACronksBaseModel
{
	
	private $params_array = array();
	
	public function setParams(array $p) {
		$this->params_array = $p;
	}
	
	public function applyToWorker(IcingaTemplateWorker &$template) {
		foreach ($this->params_array as $pKey=>$pVal) {
			$m = array();
			if (preg_match('@^(.+)-value$@', $pKey, $m)) {
				$name = $m[1];
				$val = $pVal;
				$op = array_key_exists($name. '-operator', $this->params_array) 
					? $this->params_array[ $name. '-operator' ] 
					: null;
					
				$template->setCondition($name, $val, $op);
			}
		}
		
	}
}

?>