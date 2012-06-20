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


class AppKitExtDataInterface {

    /*
     * Ext data types
     */
    const EXT_TYPE_AUTO     = 'types.AUTO';
    const EXT_TYPE_BOOL     = 'types.BOOL';
    const EXT_TYPE_BOOLEAN  = 'types.BOOLEAN';
    const EXT_TYPE_DATE     = 'types.DATE';
    const EXT_TYPE_FLOAT    = 'types.FLOAT';
    const EXT_TYPE_INT      = 'types.INT';
    const EXT_TYPE_INTEGER  = 'types.INTEGER';
    const EXT_TYPE_NUMBER   = 'types.NUMBER';
    const EXT_TYPE_STRING   = 'types.STRING';

    /*
     * Ext sort types
     */
    const EXT_SORT_DATE     = 'asDate';
    const EXT_SORT_FLOAT    = 'asFloat';
    const EXT_SORT_INT      = 'asInt';
    const EXT_SORT_TEXT     = 'asText';
    const EXT_SORT_UCSTRING = 'asUCString';
    const EXT_SORT_UCTEXT   = 'asUSText';

    /*
     * Doctrine data types
     */
    const DOCTRINE_TYPE_INTEGER     = 'integer';
    const DOCTRINE_TYPE_DECIMAL     = 'decimal';
    const DOCTRINE_TYPE_STRING      = 'string';
    const DOCTRINE_TYPE_CLOB        = 'clob';
    const DOCTRINE_TYPE_FLOAT       = 'float';
    const DOCTRINE_TYPE_ARRAY       = 'array';
    const DOCTRINE_TYPE_BLOB        = 'blob';
    const DOCTRINE_TYPE_GZIP        = 'gzip';
    const DOCTRINE_TYPE_BOOLEAN     = 'boolean';
    const DOCTRINE_TYPE_DATE        = 'date';
    const DOCTRINE_TYPE_TIME        = 'time';
    const DOCTRINE_TYPE_TIMESTAMP   = 'timestamp';
    const DOCTRINE_TYPE_ENUM        = 'enum';

    /*
     * Doctrine 2 extjs sort type
     */
    protected static $doctrineToExtSortType = array(
                self::DOCTRINE_TYPE_INTEGER     => self::EXT_SORT_INT,
                self::DOCTRINE_TYPE_DECIMAL     => self::EXT_SORT_FLOAT,
                self::DOCTRINE_TYPE_STRING      => self::EXT_SORT_TEXT,
                self::DOCTRINE_TYPE_CLOB        => null,
                self::DOCTRINE_TYPE_FLOAT       => self::EXT_SORT_FLOAT,
                self::DOCTRINE_TYPE_ARRAY       => null,
                self::DOCTRINE_TYPE_BLOB        => null,
                self::DOCTRINE_TYPE_GZIP        => null,
                self::DOCTRINE_TYPE_BOOLEAN     => self::EXT_SORT_INT,
                self::DOCTRINE_TYPE_DATE        => self::EXT_SORT_DATE,
                self::DOCTRINE_TYPE_TIME        => self::EXT_SORT_DATE,
                self::DOCTRINE_TYPE_TIMESTAMP   => self::EXT_SORT_DATE,
                self::DOCTRINE_TYPE_ENUM        => null,
            );

    public static function doctrineColumn2ExtSortType($doctrine_column_type, $default=self::DOCTRINE_TYPE_STRING) {
        if (isset(self::$doctrineToExtSortType[$doctrine_column_type])) {
            return self::$doctrineToExtSortType[$doctrine_column_type];
        }

        return $default;
    }
}
