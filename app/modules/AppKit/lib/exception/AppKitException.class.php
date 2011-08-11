<?php
/**
 * Custom exception class to use printf formats
 * @author mhein
 */
class AppKitException extends AgaviException {

    /**
     * Customized constructor
     * @param $mixed
     */
    public function __construct($mixed) {
        $args = func_get_args();

        if (AppKitStringUtil::detectFormatSyntax($mixed)) {
            $format = array_shift($args);

            parent::__construct(vsprintf($format, $args));
        } else {

            call_user_func_array(array($this,'Exception::__construct'),array($mixed,$this->getCode()));
        }
    }

}