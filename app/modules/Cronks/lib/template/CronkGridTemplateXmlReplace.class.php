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


class CronkGridTemplateXmlReplace {

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

        if (preg_match('@\$\{([^\}]+)\}@', $content)) {
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

        if (preg_match('@\$\{([^\}]+)\}@', $content)) {
            return $this->parser->parseData($content);
        }

        // Can't do this later (double parsing ....)
        elseif(strstr($content, '::')) {
            if (defined($content)) {
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
