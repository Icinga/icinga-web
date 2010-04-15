<?php

require_once "phing/Task.php";
require_once "manifestStore.php";

class ManifestTranslationExtractorTask extends Task {
 private $file = null;
    private $toFile = null;
	private $xmlObject = null;
	
    public function setFile($str) {
        $this->file = $str;
    }
    public function setTofile($target) {
    	$this->toFile = $target;
    }
    public function setXMLObject(DOMDocument $xml) {
    	$this->xmlObject = $xml;
    }
	public function getFile() {
		return $this->file;
	}
	public function getTofile() {
		return $this->toFile;
	}
    public function getXMLObject() {
    	return $this->xmlObject;
    } 
    
    public function init() {
		 
    }
	
    public function main() {
    	$file = $this->getFile();
    	$DOM = new DOMDocument("1.0","UTF-8");
    	$DOM->load($file);
		$this->setXMLObject($DOM);
		$this->extractTranslation();
	}
	
	public function extractTranslation() {
		$translationXML = new DOMDocument("1.0","UTF-8");
		$translationXML->load($this->project->getUserProperty("PATH_Icinga")."app/config/translation.xml");
		
		$xPathTrans = new DOMXPath($translationXML);
		$xPathTrans->registerNamespace("ae","http://agavi.org/agavi/config/global/envelope/1.0");
		$xPathTrans->registerNamespace("default","http://agavi.org/agavi/config/parts/translation/1.0");
		
		$manifest = $this->getXMLObject();
		$items = $manifest->getElementsByTagName("Translator")->item(0);
		if(!$items)
			return false;
			
		$ResultDOM = new DOMDocument("1.0","UTF-8");
		$rootNode = $ResultDOM->createElementNS("http://agavi.org/agavi/config/parts/translation/1.0","TranslationData");
		$ResultDOM->appendChild($rootNode);
		
		$locales = $ResultDOM->createElementNS("http://agavi.org/agavi/config/parts/translation/1.0","available_locales");
		$rootNode->appendChild($locales);

		$translators = $ResultDOM->createElementNS("http://agavi.org/agavi/config/parts/translation/1.0","translators");
		$rootNode->appendChild($translators);	
		
		foreach($items->childNodes as $child) {
			switch($child->nodeName) {
				case 'Locale':
					$element = $ResultDOM->createElementNS("http://agavi.org/agavi/config/parts/translation/1.0","available_locale");
						$element->setAttribute("identifier",$child->getAttribute("id"));
					$param = $ResultDOM->createElement("ae:parameter",$child->nodeValue);
						$param->setAttribute("name",$child->nodeValue);
					$element->appendChild($param);
					$locales->appendChild($element);
					break;
				case 'Domain':
					$name = $child->nodeValue;
					$source = $xPathTrans->query("//default:translator[@domain='".$name."']")->item(0);
					if(!$source)
						throw new BuildException("Didn't find translation domain ".$name);
					$node = $ResultDOM->importNode($source,true);
					$translators->appendChild($node);
					break;
			}
		}
		
		$ResultDOM->appendChild($rootNode);
		$ResultDOM->formatOutput = true;
		
		$ResultDOM->save($this->getTofile());
	}	

}