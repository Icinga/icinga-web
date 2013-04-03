<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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
