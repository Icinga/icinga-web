<?php
class AppKitMenuConfigHandler extends AgaviXmlConfigHandler {
    private $document = null;
    private $xpath;
    private $menuDefinition = null;

    const XML_NAMESPACE = 'http://icinga.org/icinga/config/global/menu/1.0';

    public function execute(AgaviXmlConfigDomDocument $document) {
        $this->document = $document;
        $this->menuDefinition = new AppKitLinkedList();
        $this->context = AgaviContext::getInstance();
        $document->setDefaultNamespace(self::XML_NAMESPACE, 'm');
        $this->setupXPath();
        $this->fetchMenudefinition();
        return $this->generate('return '.var_export($this->menuDefinition->toArray(),true));
    }

    private function setupXPath() {
        $this->xpath = new DOMXPath($this->document);
        $this->xpath->registerNamespace("m",self::XML_NAMESPACE);
        $this->xpath->registerNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
    }

    private function fetchMenuDefinition() {
        $this->getMenuPoints();
        $this->reorderMenu();
    }

    private function getMenuPoints() {
        $xpath = $this->xpath;
        $menus = $xpath->query("//ae:configurations/ae:configuration/m:menu");
        foreach($menus as $menupoint) {
            $this->menuDefinition->push($this->parseMenuPoint($menupoint));
        }

    }

    private function parseMenuPoint(DOMNode $menupoint) {
        $def = array();
        $def["id"] = $menupoint->attributes->getNamedItem("id")->value;
        $this->parseMenuProperties($def,$menupoint);

        return $def;
    }




    private function reorderMenu() {
        $menu = $this->menuDefinition;
        foreach($menu as $id=>$menuPoint) {
            if (!isset($menuPoint["preferposition"])) {
                continue;
            }

            $pos = $menuPoint["preferposition"];
            $menu->offsetUnset($id);
            unset($menuPoint["preferposition"]);

            if ($pos == "last") {
                $menu->push($menuPoint);
            } else if ($pos == "first") {
                $menu->unshift($menuPoint);
            } else {
                $splitted = explode(":",$pos);

                if (count($splitted) == 2) {
                    if ($splitted[0] == 'before') {
                        $menu->offsetUnshift($splitted[1],$menuPoint);
                    }

                    if ($splitted[0] == 'after') {
                        $menu->offsetPush($splitted[1],$menuPoint);
                    }
                }
            }



        }

    }

    private function parseMenuProperties(&$def,$menu) {
        if (!$menu->hasChildNodes()) {
            return;
        }

        $properties = $menu->childNodes;

        foreach($properties as $property) {
            $val = trim($property->nodeValue);

            switch ($property->nodeName) {
                case 'route':
                    $def["target"] = array(
                                         "url" => $this->context->getRouting()->gen($val)
                                     );

                case 'url':
                    if (!isset($def["target"]))
                        $def["target"] = array(
                                             "url" => $val
                                         );

                    $attributes = $property->attributes;
                    foreach($attributes as $attribute=>$value) {
                        if ($value->value) {
                            $def["target"][$attribute] = trim($value->value);
                        }
                    }
                    break;

                case 'items':
                    $def['items'] = array();
                    $items = $property->childNodes;
                    foreach($items as $item) {
                        if (!$item->hasChildNodes()) {
                            continue;
                        }

                        $subMenu = array();
                        $this->parseMenuProperties($subMenu,$item);
                        $def['items'][] = $subMenu;
                    }
                    break;

                default:
                    if ($val && $property->nodeType != XML_TEXT_NODE) {
                        $def[$property->nodeName] = $val;
                    }
            }
        }
    }

}
