<?php

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