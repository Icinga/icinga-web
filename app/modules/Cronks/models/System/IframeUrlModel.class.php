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


/**
 * URLs in iframes handles here
 * @author mhein
 *
 */
class Cronks_System_IframeUrlModel extends CronksBaseModel {

    private $baseURl = null;

    private $user = null;

    private $pass = null;

    private $params = array();

    /**
     * @var AgaviRequestDataHolder
     */
    private $rd = null;

    public function setBaseUrl($baseUrl) {
        $this->baseURl = $baseUrl;
    }

    public function setUserPassword($user, $password) {
        $this->user = $user;
        $this->pass = $password;
    }

    public function setParamMapArray(array $paramMap) {
        $this->params = $paramMap + $this->params;
    }

    public function setRequestDataHolder(AgaviRequestDataHolder $rd) {
        $this->rd = $rd;
    }

    private function glueTogether() {

        $u = (string)$this->baseURl;

        if (count($this->params)) {

            $params = array();

            foreach($this->params as $target=>$source) {
                $m = array();

                if (preg_match('/^_(\w+)\[([^\]]+)\]$/', $source, $m)) {
                    $source = $this->rd->get(strtolower($m[1]), $m[2]);
                }

                if ($source) {
                    $params[] = sprintf('%s=%s', $target, urlencode($source));
                }
            }

            if (strpos($u, '?') !== false) {
                $u .= '&'. implode('&', $params);
            } else {
                $u .= '?'. implode('&', $params);
            }

        }

        if ($this->user && $this->pass) {
            $u = str_replace('://', sprintf('://%s:%s@', $this->user, $this->pass), $u);
        }

        return $u;
    }

    public function __toString() {
        return $this->glueTogether();
    }
}