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


class Cronks_ColumnDisplay_FormatModel extends CronksBaseModel implements AgaviISingletonModel {

    private static $duration_map = array(
    'w' => 604800,
    'd' => 86400,
    'h' => 3600,
    'm' => 60,
    's' => 1
    );

    /**
     * You can give a format to return a custom string
     * @param $val
     * @param $method_params
     * @param $row
     * @return unknown_type
     */
    public function formatTemplate($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {

        $parser = new AppKitFormatParserUtil();
        $parser->setDefault($val);

        $parser->registerNamespace('field', AppKitFormatParserUtil::TYPE_ARRAY);
        $parser->registerData('field', $row->getParameters());

        return $parser->parseData($method_params->getParameter('format', '${*}'));
    }

    public function formatDate($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        $source = $method_params->getParameter('source');
        $format = $method_params->getParameter('format');

        if (!$format) {
            return $val;
        }

        $date = null;

        if ($source && !preg_match('@iso@', $source)) {

        } else {
            $date = strtotime($val);
        }

        return date($format, $date);
    }

    public function agaviDateFormat($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        $check = strtotime($val);
        if ($check <= 0) {
            return '(null)';
        }
        return $this->context->getTranslationManager()->_d($val, $method_params->getParameter('domain', 'date-tstamp'));
    }

    public function durationString($val, AgaviParameterHolder $method_params, AgaviParameterHolder $row) {
        if (($date = strtotime($val)) > 0) {
            $diff = time()-$date;

            if ($diff > 0) {
                $out = array();
                foreach(self::$duration_map as $k=>$v) {
                    $m = $diff%$v;

                    if ($diff==$m) {
                        continue;
                    } else {
                        $out[] = floor($diff/$v).$k;
                        $diff = $m;
                    }

                    if ($m==0) {
                        break;
                    }

                }
                return implode(' ', $out);
            }
        }

        return '';
    }
}