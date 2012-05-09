<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


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