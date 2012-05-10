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
 * Model to rewrite array record structures (written for Api module to rewrite
 * result arrays).
 * 
 * This class internally registers rewrite methods based on regular expressions
 * to match key values in the record and apply the methods to the keyvalues.
 * @package Icinga_Api
 * @author mhein
 * @since 1.7.0
 */
class Api_Result_OutputRewriteModel extends IcingaApiBaseModel {
    
    /**
     * Static array of rewrite strings. Used to set icinga status classes
     * to specific states
     * @var array
     */
    private static $states = array (
        'ok', 'warning', 'critical', 'unknown', 'up', 'down', 'unreachable'
    );
    
    /**
     * Registration of rewrite methods
     * @todo If needed allow external filling
     * @var array
     */
    private static $rewriters = array(
            array(
                    'regex' => '^(HOST|SERVICE)_(LONG_)?OUTPUT$',
                    'method' => 'rewritePluginOutput'
            )
    );
    
    /**
     * Maps the methods to array key names
     * @param array $res
     * @return array Ready mapped rewriter description
     */
    private function buildRewriterMatch(array $res) {
        $out = array ();
        if (count($res)) {
            $record = $res[0];
            foreach ($record as $field=>$value) {
                foreach (self::$rewriters as $rewriter) {
                    if (preg_match('@'. $rewriter['regex']. '@', $field)) {
                        
                        if (!isset($out[$field])) {
                            $out[$field] = array();
                        }
                        
                        $out[$field][] = $rewriter['method'];
                    }
                }
            }
        }
        
        return $out;
    }
    
    /**
     * Rewrite the values based on predefined internal methods
     * @param array $result
     * @param array $rewriters
     * @return array Rewritten array values
     */
    private function doRewrites(array $result, array $rewriters) {
        foreach ($result as $idx=>$row) {
            foreach ($rewriters as $field=>$methods) {
                if (isset($row[$field])) {
                    foreach ($methods as $method) {
                        $result[$idx][$field] = $this->$method($row[$field]);
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Method to rewrite plugin output (simple and long version) to respect
     * line breaks in HTML also colourize state markers with the color
     * if state
     * @param string $val
     * @return string New value
     */
    private function rewritePluginOutput($val) {
        $new_val = str_replace('\\n', '<br />', $val);
        
        foreach(self::$states as $state) {
            $new_val = preg_replace(
                '@(\['. $state. '\])@i',
                sprintf('<span class="icinga-status-%1$s">\1</span>', $state),
                $new_val
            );
        }
        
        return $new_val;
    }
    
    /**
     * Interface for consuming components (e.g. ApiSearchAction)
     * @param array $result
     */
    public function rewrite(array $result) {
        $rewriters = $this->buildRewriterMatch($result);
        if (count($rewriters)) {
            $result = $this->doRewrites($result, $rewriters);
        }
        return $result;
    }
}