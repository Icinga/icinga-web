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


class Web_Icinga_ApiSearchSuccessView extends IcingaWebBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {

        $this->setAttribute('_title', 'Icinga.ApiSearch');
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        $searchResult = $rd->getParameter("searchResult");

        $result = array(
                      "result" => $searchResult,
                      "success" => "true"
                  );

        if (false !== $rd->getParameter("withMeta", false)) {
            // Configure ExtJS' JsonReader
            $result["metaData"] = $this->getMetaDataArray($rd);
        }

        $count = $rd->getParameter("searchCount");

        if ($count) {
            $count = array_values($count[0]);
            $result["total"] = $count[0];
        }

        return json_encode($result);
    }

    protected function getMetaDataArray(AgaviRequestDataHolder $rd) {
        $idField = $rd->getParameter("idField",false);
        $columns = $rd->getParameter("columns");

        if ($idField) {
            $metaData["idProperty"] = $idField;
        } else if (count($columns) == 1) {
            $metaData["idProperty"] = $idField = $columns[0];
        }

        if ($idField) {
            foreach($columns as &$column) {
                if ($column = $idField) {
                    $columns[] = array("name"=>"idField","mapping"=>$column);
                }
            }
        }

        $metaData["paramNames"] = array(
                                      'start' => 'limit_start',
                                      'limit' => 'limit'
                                  );
        $metaData["totalProperty"] = "total";
        $metaData["root"] = "result";
        $metaData["fields"] =$columns;
        return $metaData;
    }

    protected function createDOM(AgaviRequestDataHolder $rd) {
        $results = $rd->getParameter("searchResult",null);
        $count = $rd->getParameter("searchCount");
        $DOM = new DOMDocument("1.0","UTF-8");
        $root = $DOM->createElement("results");
        $DOM->appendChild($root);
        foreach($results as $result) {
            $resultNode = $DOM->createElement("result");
            $root->appendChild($resultNode);
            foreach($result as $fieldname=>$field) {
                $node = $DOM->createElement("column");
                $node->nodeValue = $field;

                $name = $DOM->createAttribute("name");
                $name->nodeValue = $fieldname;
                $node->appendChild($name);
                $resultNode->appendChild($node);
            }
        }

        if ($count) {
            $count = array_values($count[0]);
            $node = $DOM->createElement("total");
            $node->nodeValue = $count[0];
            $root->appendChild($node);
        }

        return $DOM;
    }

    public function executeXml(AgaviRequestDataHolder $rd) {
        $DOM = $this->createDOM($rd);
        return $DOM->saveXML();
    }

    public function executeRest(AgaviRequestDataHolder $rd) {
        $xml = $this->createDOM($rd);
        $xsltproc = new XSLTProcessor();
        $xsl = new DOMDocument();
        $xsl->load(AgaviConfig::get('core.module_dir').'/Web/data/results.xslt');
        $xsltproc->importStylesheet($xsl);
        $result = $xsltproc->transformToXML($xml);
        return $result;
    }

    public function executeSimple(AgaviRequestDataHolder $rd) {

    }
}

?>