<?php

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

    /**
     * Workarround for the missing get_called_class and late static binding
     * @return string
     */
    public static function getCalledClass() {
        $bt = debug_backtrace();
        $l = 0;

        do {
            $l++;
            $lines = file($bt[$l]['file']);
            $callerLine = $lines[$bt[$l]['line']-1];
            preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/',
                       $callerLine,
                       $matches);
        } while ($matches[1] == 'parent' && $matches[1]);

        return $matches[1];
    }

}

class AppKitClassUtilException extends AppKitException { }

?>