<?php

class Cronks_bpAddon_eventRequestDelegateAction extends CronksBaseAction
{
	/**
	 * Returns the default view if the action does not serve the request
	 * method used.
	 *
	 * @return     mixed <ul>
	 *                     <li>A string containing the view name associated
	 *                     with this action; or</li>
	 *                     <li>An array with two indices: the parent module
	 *                     of the view to be executed and the view to be
	 *                     executed.</li>
	 *                   </ul>
	 */

	

	public function executeWrite(AgaviRequestDataHolder $rd) {
		$config = $rd->getParameter("config");
		$bp = $rd->getParameter("process");
		$serviceList = array();
		if($rd->getParameter("host",false) == false) {
			$bpConfig = AgaviConfig::get("modules.cronks.bp");
			$abs_file = $bpConfig["paths"]["configTarget"]."/".$config.".conf";
			$parser = $this->getContext()->getModel("bpAddon.bpCfgInterpreter","Cronks",array($abs_file));
			$allProcesses = $parser->parse();
			$processToList = null;
			foreach($allProcesses as $p) {
				if($p->getName() == $bp) {
					$processToList = $p;
					break;
				}
			}
			$serviceList = $this->getServiceList($processToList,$allProcesses);
		} else {
			$service = $this->getContext()->getModel("bpAddon.service","Cronks");
			$service->setServiceName($bp);
			$service->setHostName($rd->getParameter("host"));
			$serviceList = array($service);
		}
		$this->buildFilter($serviceList,$rd);
		return $this->getDefaultViewName($serviceList);

	}

	protected function buildFilter($serviceList,$rd) {
		$ctx = $this->getContext();
		
		$filter = array("type"=>"AND","field"=>array(array("type"=>"OR","field"=>array())));

		foreach($serviceList as $service) {
			$filter["field"][0]["field"][] =
				array(
					"type"=>"AND",
					"field"=>array(
						array(
							"type"=>"atom",
							"field"=>array("SERVICE_NAME"),
							"method"=>array("="),
							"value"=>array($service->getServiceName())
						),
						array(
							"type"=>"atom",
							"field"=>array("HOST_NAME"),
							"method"=>array("="),
							"value"=>array($service->getHostName())
						)
					)
				);
		}

		$rd->setParameter("filters_json", json_encode($filter));

	}

	protected function getServiceList(CronksBaseModel $process,array $processList,&$processes = array()) {
		if($process instanceof Cronks_bpAddon_serviceModel) {
			$processes[] = $process;
		} else {
			foreach($process->getSubProcesses() as $subProcess) {
				$subProcess = trim($subProcess);
				if($subProcess[0] == ":")
					$subProcess = substr($subProcess, 1);
				if(array_key_exists($subProcess, $processList)) {
					$this->getServiceList($processList[$subProcess],$processList,$processes);
				} 
			}
			foreach($process->getServices() as $service) {;
				$processes[] = $service;
			}
		}

		return $processes;
	}

	public function isSecure() {
		return true;
	}

	public function getDefaultViewName()
	{
		return 'Success';
	}
}

?>
