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
    private $excludeCVs = array();
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
                'regex'    => '^(HOST|SERVICE)_(LONG_)?OUTPUT$',
                'targets'  => array('host', 'service'),
                'method'   => 'rewritePluginOutput',
                'optional' => true
            ),
            array(
                'regex'    => 'CUSTOMVARIABLE_VALUE$',
                'targets'  => 'all',
                'method'   => 'rewriteCustomvariables',
                'optional' => false
            ),
            array(
                'regex'    => '_URL$',
                'targets'  => array('host', 'service'),
                'method'   => 'rewriteUrl',
                'optional' => false
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

                    if ($rewriter['optional'] && $this->getParameter('optional', false) == false)
                        continue;

                    if ($rewriter['targets'] != "all" && in_array(
                            $this->getParameter('target'),
                            $rewriter['targets']) === false) {
                        continue;
                    }

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
                        $result[$idx][$field] = $this->$method($row[$field],$field,$row);
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
    private function rewritePluginOutput($val,$field,$row) {
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
     * Exclude customvariables defined in api.exclude_customvars  (see #3183)
     *
     * @param mixed $val
     * @param string $field
     * @param array $row
     */
    private function rewriteCustomvariables($val,$field,$row) {
        $namefield = str_replace("VALUE","NAME",$field);

        // If no excludes given (fixes #3279)
        if (!is_array($this->excludeCVs)) {
            return $val;
        }

        if(!isset($row[$namefield])) { // Fallback case: No name given, no c
            return "";
        } else if(in_array($row[$namefield],$this->excludeCVs)) {
            return "";
        }
        return $val;
    }

    public function rewriteUrl($val, $field, $row) {
        static $expander = null;

        if ($expander === null) {
            /** @var Api_MacroExpanderModel $expander */
            $expander = $this->context->getModel('MacroExpander', 'Api');
        }

        $objectId = 0;

        foreach ($row as $key => $value) {
            if (preg_match('/OBJECT_ID$/i', $key)) {
                $objectId = $value;
                break;
            }
        }

        if ($objectId > 0 && $val) {
            return $expander->expandByObjectId($objectId, $val);
        }

        return $val;
    }

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);

        if ($this->getParameter('target', false) === false) {
            throw new AppKitModelException('Parameter "target" is mandatory!');
        }

        $this->excludeCVs = AgaviConfig::get('modules.api.exclude_customvars');
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
