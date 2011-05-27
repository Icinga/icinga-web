<?php

class IcingaTemplateXmlReplace {

    /**
     * @var AppKitFormatParserUtil
     */
    private $parser = null;

    private $context = null;

    public function __construct() {

        $this->context = AgaviContext::getInstance();

        $this->parser = new AppKitFormatParserUtil();

        $p =& $this->parser;

        $p->registerNamespace('xmlfn', AppKitFormatParserUtil::TYPE_METHOD);

        $ref = new ReflectionObject($this);

        // Register some methods
        $p->registerMethod('xmlfn', 'author', array(&$this, $ref->getMethod('valueAuthor')));
        $p->registerMethod('xmlfn', 'instance', array(&$this, $ref->getMethod('valueDefaultInstance')));
        $p->registerMethod('xmlfn', 'pagerMaxItems', array(&$this, $ref->getMethod('pagerMaxItems')));
        $p->registerMethod('xmlfn', 'autoRefreshTime', array(&$this, $ref->getMethod('autoRefreshTime')));
    }

    public function replaceValue($content) {

        $content = trim($content);

        if(preg_match('@\$\{([^\}]+)\}@', $content)) {
            return $this->parser->parseData($content);
        }

        elseif(is_numeric($content)) {
            return (float)$content;
        }
        elseif(preg_match('@^(yes|true)$@', $content)) {
            return true;
        }
        elseif(preg_match('@^(no|false)$@', $content)) {
            return false;
        }

        return $content;
    }

    public function replaceKey($content) {

        $content = trim($content);

        if(preg_match('@\$\{([^\}]+)\}@', $content)) {
            return $this->parser->parseData($content);
        }

        // Can't do this later (double parsing ....)
        elseif(strstr($content, '::')) {
            if(defined($content)) {
                $content = AppKit::getConstant($content);
            }
        }

        return $content;

    }

    public function pagerMaxItems() {
        $user = $this->context->getUser();
        return $user->getPrefVal('org.icinga.grid.pagerMaxItems', AgaviConfig::get('modules.cronks.grid.pagerMaxItems', 25));
    }

    public function autoRefreshTime() {
        $user = $this->context->getUser();
        return $user->getPrefVal('org.icinga.grid.refreshTime', AgaviConfig::get('modules.cronks.grid.refreshTime', 300));
    }

    public function valueAuthor() {
        return $this->context->getUser()->getNsmUser()->user_name;
    }

    public function valueDefaultInstance() {
        return 1;
    }



}

?>