<?php

class AppKitSQLConstants {

    const SQL_OP_CONTAIN		= 60;
    const SQL_OP_NOTCONTAIN		= 61;

    const SQL_OP_IS				= 50;
    const SQL_OP_NOTIS			= 51;

    const SQL_OP_LESSTHAN		= 70;
    const SQL_OP_GREATERTHAN	= 71;

    const SQL_OP_LESSOREQUAL	= 80;
    const SQL_OP_GREATEROREQUAL	= 81;

    private static $SQL_OPERATORS = array(
                                        self::SQL_OP_CONTAIN		=> 'LIKE',
                                        self::SQL_OP_NOTCONTAIN		=> 'NOT LIKE',

                                        self::SQL_OP_IS				=> '=',
                                        self::SQL_OP_NOTIS			=> '!=',

                                        self::SQL_OP_LESSTHAN		=> '<',
                                        self::SQL_OP_LESSOREQUAL	=> '<=',

                                        self::SQL_OP_GREATERTHAN	=> '>',
                                        self::SQL_OP_GREATEROREQUAL	=> '>='
                                    );

    private static $ICINGA_OPERATORS = array(
                                           self::SQL_OP_IS				=> IcingaApiConstants::MATCH_EXACT,
                                           self::SQL_OP_NOTIS			=> IcingaApiConstants::MATCH_NOT_EQUAL,
                                           self::SQL_OP_CONTAIN		=> IcingaApiConstants::MATCH_LIKE,
                                           self::SQL_OP_NOTCONTAIN		=> IcingaApiConstants::MATCH_NOT_LIKE,
                                           self::SQL_OP_GREATERTHAN	=> IcingaApiConstants::MATCH_GREATER_THAN,
                                           self::SQL_OP_LESSTHAN		=> IcingaApiConstants::MATCH_LESS_THAN,
                                           self::SQL_OP_GREATEROREQUAL	=> IcingaApiConstants::MATCH_GREATER_OR_EQUAL,
                                           self::SQL_OP_LESSOREQUAL	=> IcingaApiConstants::MATCH_LESS_OR_EQUAL
                                       );

    /**
     * Return a operator by a constant
     * @param integer $op
     * @return string
     */
    public static function getOperator($op) {
        return self::getArrayContent(self::$SQL_OPERATORS, $op);
    }

    /**
     * Return icinga match constants based on regular sql constants
     * @param integer $op
     * @return string
     */
    public static function getIcingaMatch($op) {
        return self::getArrayContent(self::$ICINGA_OPERATORS, $op);
    }

    public static function getInternalDB() {
        return AgaviContext::getInstance()->getDatabaseConnection('icinga_web');
    }
    /**
     * Internal searching for array items
     * @param array $array
     * @param integer $key
     * @return mixed
     */
    private static function getArrayContent(&$array, $key) {
        if (array_key_exists($key, $array)) {
            return $array[ $key ];
        }

        return false;
    }

}
