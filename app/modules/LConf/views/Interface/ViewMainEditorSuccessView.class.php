<?php

class LConf_Interface_ViewMainEditorSuccessView extends IcingaLConfBaseView
{
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		if($rd->getParameter("connection"))
			$this->setAttribute("start_connection",$rd->getParameter("connection"));
		if($rd->getParameter("dn"))
			$this->setAttribute("start_dn",$rd->getParameter("dn"));
        $ini = AgaviConfig::get("modules.lconf.ldap_object_presets_ini");
        $this->setAttribute("lconf_presets",json_encode(parse_ini_file($ini,true)));

        $this->setAttribute('_title', 'Interface.ViewMainEditor');
	}
	
}

?>