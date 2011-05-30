<?php

class Api_Test_RequestTestSuccessView extends IcingaApiBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);
        $DBALMetaManager = $this->getContext()->getModel("DBALMetaManager","Api");
        $DBALMetaManager->switchIcingaDatabase("icinga");
        $this->setAttribute('_title', 'Test.RequestTest');

        $dql = Doctrine_Query::create()->select("alias, hg.alias")->from("IcingaHosts")->innerJoin("IcingaHosts.hostgroups hg");
        print_r($dql->getSqlQuery());
        $arr = $dql->execute(null,Doctrine_Core::HYDRATE_RECORD);
        print_r($this->getContext()->getDatabaseManager()->getDatabase()->getConnection()->getPrefix());
        foreach($arr as $elem) {

            echo "<ol>Host ".$elem->alias;
            foreach($elem->hostgroups as $hg) {
                echo "<li>-".$hg->alias."</li>";
            }
            echo "</ol>";
        }

    }
}

?>