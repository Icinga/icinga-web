<?php

class TemplateExtenderConfigHandler extends AgaviXmlConfigHandler {
        
    const XML_NAMESPACE = 'http://icinga.org/cronks/config/parts/cronks/1.0';
    private $templateExtensions = array();
    public function execute(AgaviXmlConfigDomDocument $document) {
        $code = array();
        $parser = new CronkGridTemplateXmlParser();
        $parser->disableCache();
        $templates = $document->getElementsByTagName("template");   
             
        foreach($templates as $extension) {
            $attrs = $extension->attributes;
     
            $pattern = $attrs->getNamedItem('match-pattern');
    
            if(!$pattern) 
               continue; // we don't have a dtd, so this can happen
            
       
            $parser->setDom($extension);
            $parser->parseTemplate(true);
            
            $code[] = array(
                "pattern" => $pattern->value,
                "data" => $parser->getTemplateData(),
                "fields" => $parser->getFields()
            ); 
        
         
        }
        
        return $this->generate('return '.var_export($code,true));
    }
    
    private function processExtension(DOMNode $node) {
        $pattern = $node->getAttribute("match-pattern");
    
    }
}
