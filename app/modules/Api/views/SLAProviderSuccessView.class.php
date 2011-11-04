<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SLAProviderSuccessView
 *
 * @author jmosshammer
 */
class Api_SLAProviderSuccessView extends IcingaApiBaseView {

    public function executeJson(AgaviRequestDataHolder $rd) {
        $result = $this->getAttribute("result");
        return json_encode($result->toArray());
    }

   
}

?>
