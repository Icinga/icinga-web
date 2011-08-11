<?php

/**
 * Implement string parsing logic based on a generic syntax and
 * namespaces with different data
 * @author mhein
 *
 */
class AppKitFormatParserUtil {
    const TYPE_DATA			= 1;
    const TYPE_ARRAY		= 2;
    const TYPE_METHOD		= 3;
    const TYPE_CLASS		= 4;

    private $namespaces		= array();
    private $data			= array();
    private $default		= null;

    public function __construct() {

    }

    public function registerNamespace($name, $type) {
        if (!$this->namespaceExists($name)) {
            $this->namespaces[$name] = $type;
            return true;
        }

        throw new AppKitFormatParserUtilException('Namespace exists already!');
    }

    public function namespaceExists($name) {
        return (isset($this->namespaces[$name]) && array_key_exists($name, $this->namespaces));
    }

    public function registerData($namespace, &$data) {

        if (!$this->namespaceExists($namespace)) {
            throw new AppKitFormatParserUtilException('Namespace does not exists');
        }

        $params = func_get_args();
        $namespace = array_shift($params);
        $data = array_shift($params);
        $data2 = array_shift($params);

        if ($data2) {
            $this->data[$namespace][$data] = $data2;
        }

        elseif($data) {
            $this->data[$namespace] = $data;
        }

        return true;
    }

    public function registerMethod($namespace, $name, array $callback) {
        if (!$this->namespaceExists($namespace)) {
            throw new AppKitFormatParserUtilException('Namespace does not exists');
        }

        if ($this->getNamespaceType($namespace) !==  self::TYPE_METHOD) {
            throw new AppKitFormatParserUtilException('Namespace %s is not suitable for methods!', $namespace);
        }

        $this->data[$namespace][$name] =& $callback;
    }

    public function setDefault($val) {

    $this->default = $val;
    }

    /**
     * Extract arguments fn.test('arg1', arg2, "arg number 3")
     * @param string $string
     * @return array
     */
    private function extractArgs($string) {
        // Extract some arguments
        $mar = array();
        $args = array();

        if (preg_match_all('@\(([^\)]+)\)@', $string, $mar, PREG_SET_ORDER)) {

            $args = preg_split('@([\'"]*)\s*,\s*\\1@', $mar[0][1]);

            foreach($args as $k=>$v) {
                $args[$k] = preg_replace('@^([\'"]*)|([\'"]*)$@', '', $v);
            }
        }

        return $args;
    }

    /**
     * Cleanup argument parts from the last part
     * @param array $parts
     * @return array
     */
    private function cleanPartsArray(array $parts) {
        $last = array_pop($parts);
        $last = preg_replace('@\([^\)]+\)$@', '', $last);
        $parts[] = $last;
        return $parts;
    }

    private function callMethod(array &$cb, array $args = array()) {
        if (count($cb) == 2) {

            // Check of we can call this without problems ....
            if ($cb[1] instanceof ReflectionMethod && $cb[0] instanceof $cb[1]->class) {
                return $cb[1]->invokeArgs($cb[0], $args);
            }
        }

        throw new AppKitFormatParserUtilException('$cb - callback should contain 2 elements (check here again! Not all features are complete)');
    }

    public function parseData($format) {
        $m = array();
        $begin = $format;

        if (preg_match_all('@\$\{([^\}]+)\}@', $format, $m, PREG_SET_ORDER)) {
            foreach($m as $match) {
                $parts = explode('.', $match[1]);
                $namespace = array_shift($parts);

                $args = $this->extractArgs($match[1]);

                if (count($args)) {
                    $parts = $this->cleanPartsArray($parts);
                }

                $replace = null;
                $data = $this->getData($namespace);

                switch ($this->getNamespaceType($namespace)) {

                    case self::TYPE_ARRAY:
                        if (count($parts) == 1) {

                            if (isset($data[$parts[0]])) {
                                $replace = $data[$parts[0]];
                            }
                        }

                        break;

                    case self::TYPE_METHOD:
                        if (isset($data[$parts[0]])) {
                            $replace = $this->callMethod($data[$parts[0]], $args);
                        }

                        break;

                    default:

                    if ($match[1] == '*' && $this->default !== null) {
                                $replace = $this->default;
                            }

                        // Do not warn about missing replaces, maybe another parser could replace this
                        // @todo check if we should log this!
                        // if ($replace == null) $replace = '((('. $match[0].' not implemented!!!)))';
                        break;

                }

                if (isset($replace)) {
                    $format = preg_replace('@'. preg_quote($match[0]). '@', $replace, $format);
                }

            }
        }

        return $format;

    }

    private function getNamespaceType($namespace) {
        if ($this->namespaceExists($namespace)) {
            return $this->namespaces[$namespace];
        }

        return null;
    }

    private function getData($namespace) {
        if ($this->namespaceExists($namespace) && isset($this->data[$namespace])) {
            return $this->data[$namespace];
        }

        return null;
    }

}

class AppKitFormatParserUtilException extends AppKitException {}
