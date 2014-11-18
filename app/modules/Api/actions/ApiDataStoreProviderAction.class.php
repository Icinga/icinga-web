<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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


class Api_ApiDataStoreProviderAction extends IcingaApiBaseAction
    implements IAppKitDataStoreProviderAction, IAppKitDispatchableAction {

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
    public function getDefaultViewName() {
        return 'Success';
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        $params = $rd->getParameters();
        foreach($params as $key=>$param) {

            if (!is_string($param)) {
                continue;
            }

            $json = json_decode($param,true);

            if ($json) {
                $rd->setParameter($key,$json);
            }
        }

        $model = $this->getDataStoreForTarget($params["target"],$rd);

        if (!$model) {
            return "Error";
        }

        $result = $model->execRead();
        $r = $this->getContext()->getModel("Store.DataStoreResult","Api",array("model"=>$model));
        $r->parseResult($result,$rd->getParameter("fields",array()));
        $this->setAttribute("result",$r);


        return $this->getDefaultViewName();
    }
    public function getDataStoreForTarget($target,AgaviRequestDataHolder $rd) {
        foreach($this->getDataStoreModel() as $model) {
            if ($model["id"] == $target) {
                return $this->getContext()->getModel($model["model"],$model["module"],array("request" => $rd));
            }
        }
        return null;
    }
    public function getDataStoreModel() {
        return array(
                   /* array(
                        "module" => "Api",
                        model" => "Store.IcingaApiDataStore"
                    ), array(
                        "module" => "Api",
                        "model" => "Store.IcingaApiDataStore"
                    ),*/ array(
                       "id" => "Hosts",
                       "module" => "Api",
                       "model" => "Store.HostStore"
                   ),array(
                       "id" => "Services",
                       "module" => "Api",
                       "model" => "Store.HostStore"
                   )
               );
    }
}

?>
