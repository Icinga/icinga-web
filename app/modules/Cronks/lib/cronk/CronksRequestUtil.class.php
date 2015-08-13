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
 * Dealing with cronk requests in agavi templates: Interface
 * between PHP and JS
 * @author mhein
 *
 */
class CronksRequestUtil {

    /**
     * Default list of parameters used in a cronk action
     * @var array
     */
    protected static $requestAttributes = array(
            'cmpid', 'parentid', 'stateuid', 'state'
                                          );

    /**
     * Creates the right data structure to call the cronk script interface
     * @param AgaviRequestDataHolder $rd
     * @return array
     */
    public static function makeRequestDataStruct(AgaviRequestDataHolder $rd) {
        $data = array();
        foreach(self::$requestAttributes as $attr) {
            if ($rd->hasParameter($attr)) {
                $data[$attr] = $rd->getParameter($attr);
            }
        }
        return $data;
    }

    /**
     * Create the right data structore for the cronk script interface as json
     * @param AgaviRequestDataHolder $rd
     * @return string
     */
    public static function makeRequestDataJson(AgaviRequestDataHolder $rd) {
        return json_encode(self::makeRequestDataStruct($rd));
    }

    /**
     * Outputs the right data structure for the cronk script interface as json
     * @param AgaviRequestDataHolder $rd
     * @return null nothing
     */
    public static function echoJsonString(AgaviRequestDataHolder $rd)  {
        echo self::makeRequestDataJson($rd);
    }

}

?>
