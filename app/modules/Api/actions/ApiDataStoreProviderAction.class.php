<?php

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
