<?php

class AppKitExtDataInterface {

    /*
     * Ext data types
     */
    const EXT_TYPE_AUTO		= 'types.AUTO';
    const EXT_TYPE_BOOL		= 'types.BOOL';
    const EXT_TYPE_BOOLEAN	= 'types.BOOLEAN';
    const EXT_TYPE_DATE		= 'types.DATE';
    const EXT_TYPE_FLOAT	= 'types.FLOAT';
    const EXT_TYPE_INT		= 'types.INT';
    const EXT_TYPE_INTEGER	= 'types.INTEGER';
    const EXT_TYPE_NUMBER	= 'types.NUMBER';
    const EXT_TYPE_STRING 	= 'types.STRING';

    /*
     * Ext sort types
     */
    const EXT_SORT_DATE		= 'asDate';
    const EXT_SORT_FLOAT	= 'asFloat';
    const EXT_SORT_INT		= 'asInt';
    const EXT_SORT_TEXT		= 'asText';
    const EXT_SORT_UCSTRING	= 'asUCString';
    const EXT_SORT_UCTEXT	= 'asUSText';

    /*
     * Doctrine data types
     */
    const DOCTRINE_TYPE_INTEGER		= 'integer';
    const DOCTRINE_TYPE_DECIMAL		= 'decimal';
    const DOCTRINE_TYPE_STRING		= 'string';
    const DOCTRINE_TYPE_CLOB		= 'clob';
    const DOCTRINE_TYPE_FLOAT		= 'float';
    const DOCTRINE_TYPE_ARRAY		= 'array';
    const DOCTRINE_TYPE_BLOB		= 'blob';
    const DOCTRINE_TYPE_GZIP		= 'gzip';
    const DOCTRINE_TYPE_BOOLEAN		= 'boolean';
    const DOCTRINE_TYPE_DATE		= 'date';
    const DOCTRINE_TYPE_TIME		= 'time';
    const DOCTRINE_TYPE_TIMESTAMP	= 'timestamp';
    const DOCTRINE_TYPE_ENUM		= 'enum';

    /*
     * Doctrine 2 extjs sort type
     */
    protected static $doctrineToExtSortType = array(
                self::DOCTRINE_TYPE_INTEGER		=> self::EXT_SORT_INT,
                self::DOCTRINE_TYPE_DECIMAL		=> self::EXT_SORT_FLOAT,
                self::DOCTRINE_TYPE_STRING		=> self::EXT_SORT_TEXT,
                self::DOCTRINE_TYPE_CLOB		=> null,
                self::DOCTRINE_TYPE_FLOAT		=> self::EXT_SORT_FLOAT,
                self::DOCTRINE_TYPE_ARRAY		=> null,
                self::DOCTRINE_TYPE_BLOB		=> null,
                self::DOCTRINE_TYPE_GZIP		=> null,
                self::DOCTRINE_TYPE_BOOLEAN		=> self::EXT_SORT_INT,
                self::DOCTRINE_TYPE_DATE		=> self::EXT_SORT_DATE,
                self::DOCTRINE_TYPE_TIME		=> self::EXT_SORT_DATE,
                self::DOCTRINE_TYPE_TIMESTAMP	=> self::EXT_SORT_DATE,
                self::DOCTRINE_TYPE_ENUM		=> null,
            );

    public static function doctrineColumn2ExtSortType($doctrine_column_type, $default=self::DOCTRINE_TYPE_STRING) {
        if (isset(self::$doctrineToExtSortType[$doctrine_column_type])) {
            return self::$doctrineToExtSortType[$doctrine_column_type];
        }

        return $default;
    }
}
