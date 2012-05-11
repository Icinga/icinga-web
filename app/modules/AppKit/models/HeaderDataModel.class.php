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


class AppKit_HeaderDataModel extends AppKitBaseModel
    implements AgaviISingletonModel {
    const TYPE_CSS_RAW      = 'css_raw';
    const TYPE_CSS_FILE     = 'css_files';
    const TYPE_META         = 'meta_tags';
    const TYPE_JS_RAW       = 'javascript_inline';
    const TYPE_JS_FILE      = 'javascript_files';

    const INSERT_FIRST      = 'insert';
    const INSERT_PUSH       = 'push';

    private $data = array(
                        self::TYPE_CSS_RAW      => array(),
                        self::TYPE_CSS_FILE     => array(),
                        self::TYPE_META         => array(),
                        self::TYPE_JS_RAW       => array(),
                        self::TYPE_JS_FILE      => array(),
                    );

    public function addCssData($name, $data) {
        return $this->addFileToStore(self::TYPE_CSS_RAW, $name, $data);
    }

    public function getCssData() {
        return $this->data[self::TYPE_CSS_RAW];
    }

    public function addCssFile($file, $insert_type = self::INSERT_PUSH) {
        return $this->addFileToStore(self::TYPE_CSS_FILE, $file, $file, $insert_type);
    }

    public function getCssFiles() {
        return array_values($this->data[self::TYPE_CSS_FILE]);
    }

    public function addJsData($name, $data) {
        return $this->addFileToStore(self::TYPE_JS_RAW, $name, $data);
    }

    public function getJsData() {
        return $this->data[self::TYPE_JS_RAW];
    }

    public function addJsFile($file, $insert_type = self::INSERT_PUSH) {
        return $this->addFileToStore(self::TYPE_JS_FILE, $file, $file, $insert_type);
    }

    public function getJsFiles() {
        return array_values($this->data[self::TYPE_JS_FILE]);
    }

    public function addMetaTag($name, $value) {
        return $this->addFileToStore(self::TYPE_META, $name, $value);
    }

    public function getMetaTags() {
        return $this->data[self::TYPE_META];
    }

    private function addFileToStore($type, $name, $file, $insert_type = self::INSERT_PUSH) {
        if (array_key_exists($type, $this->data)) {

            if ($type == self::TYPE_CSS_FILE || $type == self::TYPE_JS_FILE) {
                if (!preg_match('@^'. preg_quote(AgaviConfig::get('org.icinga.appkit.web_path')). '@', $file)) {
                    $file = AgaviConfig::get('org.icinga.appkit.web_path').$file;
                }
            }

            $this->data[$type][$name] = $file;
            return true;
        }

        return false;
    }
}
