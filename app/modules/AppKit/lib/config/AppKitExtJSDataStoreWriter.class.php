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
