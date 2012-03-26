<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LConf_LConfCommandCheckSuccessView
 *
 * @author jmosshammer
 */
class LConf_Backend_LConfCommandCheckSuccessView extends IcingaLConfBaseView {

    public function executeJson(AgaviRequestDataHolder $rd) {
        return json_encode($this->getAttribute("result",
            array("success"=>false,"output" => "Unknown result")
        ));
        
    }
}
