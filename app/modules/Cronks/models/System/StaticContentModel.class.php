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


/**
 * Model for tactical overview initial data views
 * @author mhein
 *
 */
class Cronks_System_StaticContentModel extends CronksBaseModel {

    private static $arrayNodes      = array('filter', 'filterchain');
    private static $indexAttributes = array('id', 'name');

    private $api = null;

    /**
     * @var DOMDocument
     */
    private $dom = null;

    private $xmlData = array();
    private $templateFile = null;

    /**
     * @var Cronks_System_StaticContentTemplateModel
     */
    private $templateObject = null;

    public function  initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
    }

    public function setTemplateFile($templateFile) {
        $modules = AgaviConfig::get("org.icinga.modules",array());
        foreach($modules as $name=>$path) {
            if(file_exists($path."/config/templates/".$templateFile.'.xml')) {
                $templateFile = $path."/config/templates/".$templateFile.'.xml';
            }
        }
        
        if (!file_exists($templateFile)) {
            
            $this->templateFile = sprintf(
                                      '%s/%s.xml',
                                      AgaviConfig::get('modules.cronks.xml.path.to'),
                                      $templateFile
                                  );
        } else {
            $this->templateFile = $templateFile;
        }

        AppKitFileUtil::fileExists($this->templateFile);

        $this->dom = new DOMDocument('1.0', 'utf-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->load($this->templateFile);

        $this->xmlData = $this->convertDom(
                             $this->dom->getElementsByTagName('template')->item(0)
                         );
    }

    private function convertDom(DOMElement $element) {

        $data = array();

        if ($element->hasChildNodes()) {
            $count = 0;
            foreach($element->childNodes as $child) {
                if ($child->nodeType == XML_ELEMENT_NODE) {

                    $index = $this->getDomIndex($child, $count);

                    if ($this->hasChildren($child)) {
                        $data[$index] = $this->convertDom($child);
                    } else {
                        $data[$index] = $child->textContent;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Returns index value from the dom element
     * @param DOMElement $element
     * @param integer $fake Fake array counter for loop sequence
     * @return mixed
     * @author mhein
     */
    private function getDomIndex(DOMElement &$element, &$fake=0) {
        static $c = 0;

        $index = $this->namedIndex($element);

        if (!$index && $this->arrayNode($element)) {
            $index = $fake++;
        }

        elseif(!$index) {
            $index = $element->nodeName;
        }
        return $index;
    }

    /**
     * Returns an index of the dom element
     * @param DOMElement $element
     * @return mixed
     * @author mhein
     */
    private function namedIndex(DOMElement &$element) {
        foreach(self::$indexAttributes as $attr) {
            if ($element->hasAttribute($attr)) {
                return $element->getAttribute($attr);
            }
        }
        return false;
    }

    /**
     * Tests if the node contains array to provide an
     * array like index
     * @param DOMElement $element
     * @return boolean
     * @author mhein
     */
    private function arrayNode(DOMElement &$element) {
        return in_array($element->parentNode->nodeName, self::$arrayNodes);
    }

    /**
     * checks whether XML node for child nodes
     * @param   DOMElement      $element            element to check for child nodes
     * @return  boolean                             true if element has children otherwise false
     * @author  Christian Doebler <christian.doebler@netways.de>
     */
    private function hasChildren(DOMElement &$element) {
        $hasChildren = false;

        if ($element->hasChildNodes()) {
            foreach($element->childNodes as $node) {
                if ($node->nodeType == XML_ELEMENT_NODE) {
                    $hasChildren = true;
                    break;
                }
            }
        }

        return $hasChildren;
    }

    private function &getDatasources() {
        return $this->xmlData['datasources'];
    }

    private function &getTemplates() {
        return $this->xmlData['template_code'];
    }

    private function &getChain() {
        return $this->xmlData['filterchain'];
    }

    /**
     *
     * @param string $tplName
     * @param array $args
     * @return Cronks_System_StaticContentTemplateModel
     */
    public function &getTemplateObj() {

        if ($this->templateObject === null) {
            $this->templateObject = $this->getContext()->getModel('System.StaticContentTemplate', 'Cronks', array(
                                        'templates'     => $this->getTemplates(),
                                        'datasources'   => $this->getDatasources(),
                                        'chain'         => $this->getChain(),
                                        'rparam'        => $this->getParameter('rparam', array())
                                    ));
        }

        return $this->templateObject;
    }

    public function renderTemplate($tplName, array $args=array()) {
        return $this->getTemplateObj()->renderTemplate($tplName, $args, false, true);
    }

    public function getTemplateJavascript() {
        return $this->getTemplateObj()->jsGetCode(false);
    }

    /**
     * @deprecated
     */
    public function parseTemplate($tplName, array $args=array()) {
        return $this->renderTemplate($tplName, $args);
    }


}

?>
