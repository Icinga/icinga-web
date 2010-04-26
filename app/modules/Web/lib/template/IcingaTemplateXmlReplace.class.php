<?php

class IcingaTemplateXmlReplace {
	
	/**
	 * @var AppKitFormatParserUtil
	 */
	private $parser = null;
	
	public function __construct() {
		$this->parser = new AppKitFormatParserUtil();
		
		$p =& $this->parser;
		
		$p->registerNamespace('xmlfn', AppKitFormatParserUtil::TYPE_METHOD);
		
		$ref = new ReflectionObject($this);
		
		// Register some methods
		$p->registerMethod('xmlfn', 'author', array(&$this, $ref->getMethod('valueAuthor')));
		$p->registerMethod('xmlfn', 'instance', array(&$this, $ref->getMethod('valueDefaultInstance')));

	}
	
	public function replaceValue($content) {
		
		$content = trim($content);
		
		if (preg_match('@\$\{([^\}]+)\}@', $content)) {
			return $this->parser->parseData($content);
		}
		
		elseif (is_numeric($content)) {
			return (float)$content;	
		}
		elseif (preg_match('@^(yes|true)$@', $content)) {
			return true;
		}
		elseif (preg_match('@^(no|false)$@', $content)) {
			return false;
		}
		
		return $content;
	}
	
	public function replaceKey($content) {
		
		$content = trim($content);
		
		if (preg_match('@\$\{([^\}]+)\}@', $content)) {
			return $this->parser->parseData($content);
		}
		
		// Can't do this later (double parsing ....)
		elseif (strstr($content, '::')) {
			if (defined($content)) {
				$content = AppKit::getConstant($content);
			}
		}
		
		return $content;
		
	}
	
	public function valueAuthor() {
		return AgaviContext::getInstance()->getUser()->getNsmUser()->user_name;
	}
	
	public function valueDefaultInstance() {
		return 1;
	}
	
}

?>