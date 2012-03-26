<?php

class LConf_Backend_modifyFilterSuccessView extends IcingaLConfBaseView
{
	public function executeJson(AgaviRequestDataHolder $rd) {
		try {
			$params = json_decode($rd->getParameter("filters"),true);
			$id = @$params["filter_id"];	
			$name = @$params["filter_name"];
			$json = @$params["filter_json"];
			$action = $rd->getParameter("xaction");
			$mgr = $this->getContext()->getModel("LDAPFilterManager","LConf");
			
			switch($action) {
				case 'create':
					if(!$name || !$json)
						throw new Exception("Invalid parameters");
					$mgr->addFilter($name,$json);		
					break;								
				case 'update':
					if(!$id  || !$json)
						throw new Exception("Invalid parameters");
					$mgr->modifyFilter($id,$json,$name);
					break;
				case 'destroy':
					$id = $params;
					if(!$id)
						throw new Exception("Invalid parameters");
					
					if(!is_array($id))
						$id = array($id);

					$mgr->removeFilters($id);
					break;
					
			}
			return '{success:true}';
		} catch(Exception $e) {
			$this->getResponse()->setHttpStatusCode('500');
			return $e->getMessage();
		}
		
	}
	
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Backend.modifyFilter');
	}
}

?>