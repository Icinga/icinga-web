<?php

class Api_IcingaInstanceControllerSuccessView extends IcingaApiBaseView {

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);
        return json_encode($this->getAttribute("status",array()));
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        $isWrite = $this->getAttribute("write",false);

        if (!$isWrite) {
            return json_encode(array("instances" => $this->getAttribute("status",array())));
        }

        $errMsg = $this->getAttribute("errorMsg",false);

        if ($errMsg) {
            return json_encode(array("error" => true, "msg" => $errMsg));
        } else {
            return json_encode(array("success" => true));
        }

    }

}

?>
