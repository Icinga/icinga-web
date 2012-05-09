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
 * 
 * Class to parse parantheses from smallest to biggest on
 * @author mhein
 *
 */
class AppKitStringParanthesesParser extends ArrayObject {
    
    const STRIP_PARANTHESES = 1;
    
    /**
     * String to parse
     * @var string
     */
    private $string = null;
    
    /**
     * Constructor, only callable method
     * @param string to parse $string
     */
    public function __construct($string, $type=null) {
        $this->string = $string;
        parent::__construct($this->parse($this->string, $type == self::STRIP_PARANTHESES ? true : false));
    }

    /**
     * Grabs all parantheses together and exstract the string, start
     * with the smallest set
     * @param string $string
     * @param integer $offset
     * @return array
     */
    public function parse($string, $strip = false) {
        $m = array();
        $packages = array();
        if (preg_match_all('/([()])/', $string, $m, PREG_OFFSET_CAPTURE)) {
            $markers = array ();
            foreach ($m[1] as $a) {
                if ($a[0] === '(') {
                    $markers[] = $a[1];
                } elseif ($a[0] === ')') {
                    $mark = array_pop($markers);
                    $val = substr($string, $mark, ($a[1]-$mark));
                    if ($strip == true) {
                        $val = preg_replace('/^\(+|\)+$/', '', $val);
                    }
                    $packages[] = $val;
                }
            }
        }
        
        if ($strip == true && count($packages)) {
            $packages = array_unique($packages);
        }
        
        return $packages;
    }
}