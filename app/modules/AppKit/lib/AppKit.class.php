<?php
// This file is part of the ICINGAAppKit
// (c) 2009 Netways GmbH
//
// ICINGAAppKit is a framework build on top of Agavi to
// develop applications with the same context as modules
//
// For the complete license information please take a look
// into the LICENSE file in the root directory. This file
// was distributed with the source package.

/**
 * This is the AppKit masterclass. Mainly used for inheritance
 * rules and holding the bootstrab method to init the framework
 * before agavi starts.
 *
 * @package     ICINGAAppKit
 * @subpackage  AppKit
 *
 * @author      Marius Hein
 * @copyright   Authors
 * @copyright   Netways GmbH
 *
 * @version     $Id$
 *
 */

class AppKit {

    /**
     * @var string
     */
    private static $class_dir = null;

    /**
     * Returns a constantvalue represented by a string input
     *
     * @param string $value
     * @return mixed
     * @author Marius Hein
     */
    public static function getConstant($value) {
        $value = preg_replace('@^CONST::@', null, $value);

        if (defined($value)) {
            return constant($value);
        }

        throw new AppKitException("Constant '$value' not defined!");
    }

    /**
     * Create an instance of a class by function arguments
     * @param string $class_name
     * @param mixed $arg1
     * @return StdClass
     * @throws AppKitException
     * @author Marius Hein
     */
    public static function getInstance($class_name, $arg1=null) {
        $args = func_get_args();
        $class_name = array_shift($args);
        return self::getInstanceArray($class_name, $args);
    }

    /**
     * Create an instance of a class by array arguments
     * @param string $class_name
     * @param array $args
     * @return StdClass
     * @throws AppKitException
     * @author Marius Hein
     */
    public static function getInstanceArray($class_name, array $args = array()) {
        $ref = new ReflectionClass($class_name);

        if ($ref->isInstantiable()) {
            return $ref->newInstanceArgs($args);
        }

        throw new AppKitException('Class '. $class_name. ' is not instantiable!');
    }

    /**
     * Delayed debug method, write test data to the queue to display within the site
     * after one request
     *
     * @param mixed $mixed
     * @return boolean
     */
    public static function debugOut($mixed) {
        $queue = AppKitFactories::getInstance()->getFactory('MessageQueue');

        if ($queue instanceof AppKitQueue) {
            $args = func_get_args();

            if (count($args) == 1) {
                $args = $args[0];
            }

            ob_start();
            var_dump($args);
            $data = ob_get_clean();
            $queue->enqueue(AppKitMessageQueueItem::Debug($data));
        }

        return true;
    }

    /**
     * A centralized anchor to get version information
     * @return string
     */
    public static function getVersion() {
        return AgaviConfig::get('org.icinga.version.release');
    }
}

?>