<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LConfCommandCheckAction
 *
 * @author jmosshammer
 */
class LConf_Backend_LConfCommandCheckAction extends IcingaLConfBaseAction {
    public function executeRead(AgaviRequestDataHolder $rd) {
        return $this->executeWrite($rd);
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        $connectionId = $rd->getParameter("connectionId");
        $cmdLine = $rd->getParameter("commandline");
        $tokens = json_decode($rd->getParameter("tokens","{}"),true);

        $model = $this->getContext()->getModel("LConfCheckTest","LConf");
        
        $this->setAttribute("result", $model->testCheck($cmdLine,$tokens));

        return "Success";
    }

    private function getCommandLine($connectionId,$checkDn) {
        $ctx = $this->getContext();
        $ctx->getModel("LDAPClient","LConf");
        $client = LConf_LDAPClientModel::__fromStore($connectionId,$ctx->getStorage());
        $node = $client->getNodeProperties($checkDn);
        foreach($node as $attribute=>$value) {
            if(preg_match("/.*?commandline/i",$attribute))
                return $value[0];
        }
    }

    public function getCredentials() {
	    return array("lconf.user","lconf.testcheck");
	}
    public function isSecure() {
        return true;
    }
}