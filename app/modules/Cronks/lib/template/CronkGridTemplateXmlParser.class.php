<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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


class __CronkGridTemplateXmlParserInternalCacheContainer__ {

    public $data = array();
    public $fields = array();
    public $rewrite = null;

}

class CronkGridTemplateXmlParser implements Serializable {

    /**
     * @var DOMDocument
     */
    private $dom = null;
    private $data = array();
    private $fields = array();
    private $ready = false;
    private static $available = array(
        'version', 'datasource', 'meta', 'option', 'fields','decorators'
    );
    private $filename = "";

    /**
     * Object to replace some values
     * @var CronkGridTemplateXmlReplace
     */
    private $rewrite = null;
    private $useCaching = false;
    private $maxCacheTime = 14400;
    private $cachedContent = null;
    private $cacheHit = false;
    private static $registeredExtenders = array();
    private $file = null;

    /**
     * Generic constructor
     * @param string $file
     */
    public function __construct($file = null) {
        if (!$this->loadFromCache($file)) {

            if (file_exists($file)) {
                $this->loadFile($file);
            }

            $this->rewrite = new CronkGridTemplateXmlReplace();
        }
    }
    public function serialize() {
        return serialize(array(
            "data"=>$this->data,
            "fields"=>$this->fields
        ));
    }
    public function unserialize($serialized) {
        $data = unserialize($serialized);

        $this->data = $data["data"];
        $this->fields = $data["fields"];
    }

    /**
     * Inits the dom with a file
     * @param string $file
     * @return boolean
     */
    public function loadFile($file) {
        if (file_exists($file)) {
            $this->file = $file;
            return $this->loadXml(file_get_contents($file));
        }

        throw new CronkGridTemplateXmlParserException('File does not exist');
    }

    /**
     *
     * Allows to manually set the dom node to parse
     * @param DOMDocument    The DomDocument to parse
     * */
    public function setDom($dom) {
        $this->dom = $dom;
    }

    /**
     * inits the dom with a string of xml data
     * @param string $xml
     * @return boolean
     */
    public function loadXml($xml) {
        $this->resetState();
        $this->dom = new DOMDocument();
        $this->dom->preserveWhiteSpace = false;
        $this->dom->loadXML($xml);
        return true;
    }

    /**
     * Reset the parser state to an empty object
     * @return boolean
     */
    public function resetState() {
        $this->dom = null;
        $this->data = array();
        $this->fields = array();
        $this->ready = false;
        return true;
    }

    public function getFields() {
        return $this->fields;
    }

    public function getFieldKeys() {
        return array_keys($this->fields);
    }

    /**
     * Returns an parameter object from a field
     * @param string $name
     * @return AgaviParameterHolder
     */
    public function getFieldByName($name, $type = null) {
        if (array_key_exists($name, $this->fields)) {
            $arry = & $this->fields[$name];

            if ($type !== null) {
                if (array_key_exists($type, $arry)) {
                    $arry = & $arry[$type];
                } else {
                    throw new CronkGridTemplateXmlParserException('Type ' . $type . ' does not exist!');
                }
            }

            return new AgaviParameterHolder($arry);
        }

        // Empty one
        return new AgaviParameterHolder(array());
    }

    /**
     * Return all template data as an array
     * @return array
     */
    public function getTemplateData() {
        return $this->data;
    }

    /**
     * Return a template section as an array
     * @param string $name
     * @return array
     */
    public function getSection($name) {
        return $this->data[$name];
    }

    /**
     * Return named sections as an array
     * @return array
     */
    public function getSections() {
        return array_keys($this->data);
    }
    
    /**
     * Retun true if a section exists
     * @param String $section
     * @return Boolean
     */
    public function hasSection($section) {
        return isset($this->data[$section]);
    }

    /**
     * Returns a parameter object from section
     * @param $name
     * @return AgaviParameterHolder
     */
    public function getSectionParams($name) {
        return new AgaviParameterHolder($this->getSection($name));
    }

    /**
     * Start parsing the template
     * @return boolean
     */
    public function parseTemplate($ignoreExtensions = false) {
        if ($this->cacheHit) {
            return true;
        }

        if (!$this->dom instanceof DOMNode) {
            // throw new CronkGridTemplateXmlParserException('DOMDocument not ready!');
        }

        $storage = array();

        // Parse the template structure
        $this->parseDom($this->domRoot(), $storage);
        // Move the data to its place
        $this->fields = $storage['fields'];
        unset($storage['fields']);

        $this->data = $storage;
        unset($storage);


        if (!$ignoreExtensions)
            $this->extendTemplate();

        // Check data  
        if (count($this->fields) && count($this->data)) {
            $this->cacheContent($this->file);
            return true;
        }

        //throw new CronkGridTemplateXmlParserException('Empty xml!');

        return false;
    }

    private function extendTemplate() {
        $filename = "";
        if (is_object($this->file))
            $filename = basename($this->file->getFilename(), ".xml");
        if (is_string($this->file))
            $filename = basename($this->file, ".xml");

        if (empty(self::$registeredExtenders))
            self::$registeredExtenders = include AgaviConfigCache::checkConfig(AgaviToolkit::expandDirectives('%core.module_dir%/Cronks/config/templateExtensions.xml'));

        foreach (self::$registeredExtenders as $handler) {
            if (preg_match("/" . $handler["pattern"] . "/i", basename($filename)))
                $this->applyExtender($handler);
        }
    }
    
    /**
     * Merge all data together from extenders
     * @param array $extender
     * @return null
     */
    private function applyExtender(array $extender) {
        
        $this->data = array_merge_recursive($this->data, $extender["data"]);
        
        // If no fields defined, just skip the following
        if (!(is_array($extender["fields"]) && count($extender["fields"])>0)) {
            return;
        }
        
        foreach ($extender["fields"] as $fieldname => $field) {

            if (!isset($field['preferPosition'])) {
                $this->fields[$fieldname] = $field;
                continue;
            }
            $splitted = explode(":", $field['preferPosition']);
            if (count($splitted) != 2) {
                $this->fields[$fieldname] = $field;
                continue;
            }

            if (!isset($this->fields[$splitted[1]])) {
                $this->fields[$fieldname] = $field;
                continue;
            }
            $newKeys = array();
            // get index of keys
            
            foreach ($this->fields as $key => $existing) {
                if ($key != $splitted[1]) {
                    if(!isset($newKeys[$key]))
                        $newKeys[$key] = $existing;
                } else {
                    switch ($splitted[0]) {
                        case 'before':
                            $newKeys[$fieldname] = $field;
                            if(!isset($newKeys[$key]))
                                $newKeys[$key] = $existing;
                            break;
                        case 'after':
                        default:
                            if(!isset($newKeys[$key]))
                                $newKeys[$key] = $existing;
                            $newKeys[$fieldname] = $field;
                    }
                }
            }
            $this->fields = $newKeys;
        }
    }

    /**
     * Returns the root node
     * @return DOMElement
     */
    private function domRoot() {
        $root = null;
        if ($this->dom->nodeName == "template")
            return $this->dom;
        if ($root === null) {
            $root = $this->dom->getElementsByTagName('template')->item(0);
        }

        return $root;
    }

    private function elementHasElementChilds(DOMElement &$element) {
        if ($element->hasChildNodes()) {
            foreach ($element->childNodes as $node) {
                if ($node->nodeType == XML_ELEMENT_NODE) {
                    return true;
                }
            }
        }
    }

    /**
     * Detects constants within parameter names and resolve values
     * @param string $name
     * @return mixed
     */
    private function rewriteParamName($name) {
        if (strstr($name, '::')) {

            if (defined($name)) {
                $name = AppKit::getConstant($name);
            }
        }

        return $name;
    }

    private function parseDom(DOMElement $element, array &$storage) {


        if ($element->hasChildNodes()) {
            foreach ($element->childNodes as $child) {

                if ($child->nodeType == XML_ELEMENT_NODE) {
                    $index = '__BAD_INDEX';

                    if ($child->hasAttribute('name')) {
                        $index = $this->rewrite->replaceKey($child->getAttribute('name'));
                    } elseif ($child->nodeName == 'parameter') {
                        $index = count($storage);
                    } else {
                        $index = $child->nodeName;
                    }

                    if ($this->elementHasElementChilds($child)) {
                        $storage [$index] = array();

                        $this->parseDom($child, $storage [$index]);
                    } else {

                        // Substitute boolean or numbers, ...
                        $storage [$index] = $this->rewrite->replaceValue($child->textContent);
                    }
                }
            }
        }
    }

    public function getHeaderArray() {
        $header = array();
        foreach ($this->getFieldKeys() as $field) {
            $params = $this->getFieldByName($field, 'display');

            if ($params->getParameter('visible') == true) {
                $header[$field] = $params->getParameter('label', $field);
            }
        }
        return $header;
    }

    /** Caching functions * */
    private function initCaching() {
        $cfg = AgaviConfig::get('modules.cronks.templates');

        /*
          if (isset($cfg['use_caching'])) {
          $this->useCaching = $cfg['use_caching'];
          }

          if (isset($cfg['cache_dir'])) {
          $this->cache_dir = $cfg['cache_dir'];
          }

          if (!$this->cache_dir) {
          $this->useCaching = false;
          }
         */

        $this->useCaching = false;
    }

    private function getCacheFilename($file) {
        $file = "template_" . md5($file);

        $cached = $this->cache_dir . '/' . $file;

        if (!file_exists($this->cache_dir)) {
            AgaviToolkit::mkdir($this->cache_dir);
        }

        return $cached;
    }

    private function loadFromCache($file = null) {
        $this->initCaching();

        if ($file == null || !$this->useCaching) {
            return false;
        }

        $this->readCached($file);

        if (!$this->cachedContent instanceof __CronkGridTemplateXmlParserInternalCacheContainer__) {
            return false;
        }

        $this->data = $this->cachedContent->data;
        $this->fields = $this->cachedContent->fields;
        $this->cacheHit = true;

        return true;
    }

    private function readCached($file) {
        $cached = $this->getCacheFilename($file);

        if (file_exists($cached) && is_readable($cached)) {
            // check cache date
            if (time() - filemtime($cached) > $this->maxCacheTime) {
                return null;
            }

            $this->cachedContent = unserialize(file_get_contents($cached));
        }

        return null;
    }

    private function cacheContent($file) {

        if (!$this->useCaching) {
            return false;
        }

        $cached = $this->getCacheFilename($file);
        $cacheDir = dirname($cached);

        if (file_exists($cached) && !is_writeable($cached)) {
            return;
        }

        if (!is_dir($cacheDir) || !is_writeable($cacheDir)) {
            return;
        }

        $container = new __CronkGridTemplateXmlParserInternalCacheContainer__();
        $container->data = $this->data;
        $container->fields = $this->fields;

        file_put_contents($cached, serialize($container));
    }

    public function disableCache() {
        $this->useCaching = false;
    }

    public function removeRestrictedCommands() {
        $data = $this->data;
        if (!isset($data["option"]))
            return;
        if (!isset($data["option"]["commands"]))
            return;
        if(!isset($data["option"]["commands"]["items"]))
            return;
        $items = $data["option"]["commands"]["items"];
        if (!is_array($items))
            return;
        $config = include AgaviConfigCache::checkConfig(AgaviToolkit::expandDirectives('%core.module_dir%/Api/config/icingaCommands.xml'));
        $toRemove = array();
        foreach ($items as $cmd_name => $cmd_def) {
            if (!isset($config[$cmd_name])) {
                $toRemove[] = $cmd_name;
                continue;
            }
            if (!$config[$cmd_name]["isSimple"])
                $toRemove[] = $cmd_name;
        }
        foreach ($toRemove as $removeItem)
            unset($data["option"]["commands"]["items"][$removeItem]);
        $this->data = $data;
    }

}

class CronkGridTemplateXmlParserException extends AppKitException {
    
}
