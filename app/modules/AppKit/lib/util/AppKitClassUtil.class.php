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
 * Handling class issues
 * @author mhein
 *
 */
class AppKitClassUtil {

    /**
     * Quick to create a class instance
     * @param string $class_name
     * @param mixed $arg1
     * @return StdClass
     */
    public static function createInstance($class_name, $arg1=null) {
        $args = func_get_args();
        $class = array_shift($args);

        if (!class_exists($class)) {
            throw new AppKitClassUtilException($class. ' does not exist');
        }

        $ref = new ReflectionClass($class);

        if ($ref->isInstantiable()) {
            return $ref->newInstanceArgs($args);
        } else {
            throw new AppKitClassUtilException($class. ' is not instantiable');
        }

    }

}

class AppKitClassUtilException extends AppKitException { }