<?php

class LConf_Backend_LConfExportTaskAction extends IcingaLConfBaseAction
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
	public function execute(AgaviRequestDataHolder $rd) {
		$connManager = AgaviContext::getInstance()->getModel('LDAPConnectionManager','LConf');
		$preflight = $rd->getParameter("preflight",false);
		$satellites = json_decode($rd->getParameter("satellites","[]"),true);
	
		$connectionId = $rd->getParameter("connection_id");
		$connManager->getConnectionsFromDB();
		
		$conn = $connManager->getConnectionById($connectionId);
		$confExporter = AgaviContext::getInstance()->getModel('LConfExporter','LConf');
		try {
			if(!$preflight) {
				$result = $confExporter->exportConfig($conn,$satellites);
				$this->setAttribute("config",$result);
			} else {
				$this->setAttribute("config",$confExporter->getChangedSatellites($conn));
			}
		} catch(Exception $e) {
			$this->setAttribute("error_msg",$e->getMessage());
			return "Error";
		}
		return $this->getDefaultViewName();
	}
	
	public function getDefaultViewName()
	{
		return 'Success';
	}
}

?>
