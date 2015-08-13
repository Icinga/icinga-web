<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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
 * Class to create viewable configuration items of icinga
 * @author mhein
 *
 */
class Config_ConfigValuesModel extends IcingaConfigBaseModel {
    const HIDDEN_VALUE = '**HIDDEN_ENTRY**';
    
    /**
     * Creates an array of readable configuration items
     * @return multitype:string mixed
     */
    public function getValuesForDisplay() {
        $out = array();
        $values = AgaviConfig::toArray();
        ksort($values);
        foreach ($values as $k=>$v) {
            if (preg_match('/password|passwd/i', $k)) {
                $out[] = array (
                    'key' => $k,
                    'value' => self::HIDDEN_VALUE
                );
            } else {
                $out[] = array (
                    'key' => $k,
                    'value' => $this->getValuesDump($v)
                );
            }
        }
        return $out;
    }
    
    private function getValuesDump($var) {
        if (ob_start()) {
            var_dump($var);
            return htmlspecialchars(ob_get_clean());
        }
    }
    
}

?>
