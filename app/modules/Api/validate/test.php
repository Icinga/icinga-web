<?php

$DOM = new DomDocument();
$DOM->load("ApiSearch.xml");

$xpath = new DOMXPath($DOM);
$xpath->registerNamespace("default","http://agavi.org/agavi/config/parts/validators/1.0");
$xpath->registerNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
foreach($xpath->query("//default:validators") as $node)  {
    echo $node->nodeName."\n";
    echo $node->getAttribute("method");
}

?>
