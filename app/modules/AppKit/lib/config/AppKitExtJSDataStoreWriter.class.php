<?php

class AppKitExtJSDataStoreWriter {
    private $jsParts = array();

    public function write(array $descriptor, $outfile) {

        foreach($descriptor as $store) {
            $this->createStore($store);
        }
        $js = "Ext.ns('Icinga.Api');\n\n";
        foreach($this->jsParts as $part) {
            $js .= $part;
        }

        file_put_contents($outfile,$js);
    }

    private function createStore(array $store) {

        $rewritten = array();

        foreach($store as $key=>$elem) {
            if (is_array($elem)) {
                $rewritten[$key] = $this->rewriteArray($elem);
            } else {
                $rewritten[$key] = $elem;
            }
        }

        $this->jsParts[] = "Ext.ns('Icinga.Api')['".$store["module"]."_".$store["action"]."'] = ".json_encode($rewritten)."\n\n";

    }
    private function rewriteArray($arr) {
        $rewritten = array();

        foreach($arr as $key=>$elem) {
            if (is_numeric($key)) {
                $rewritten[$elem["type"]] = $elem;
            }
        }

        return $rewritten;
    }
}
