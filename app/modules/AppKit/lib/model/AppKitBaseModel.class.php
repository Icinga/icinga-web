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
 * The base model from which all AppKit module models inherit.
 */
class AppKitBaseModel extends AgaviModel {

    protected $parameters = array();

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->clearParameters();
        $this->setParameters($parameters);
    }

    // -------------------
    // Parameter interface

    /**
     * Clears the parameter array
     * @return boolean
     */
    protected function clearParameters() {
        $this->parameters = array();
        return true;
    }

    /**
     * Returns the whole struct of parameters
     * @return array
     */
    protected function &getParameters() {
        return $this->parameters;
    }

    /**
     * Returns an parameter or the default
     * value if not exist
     * @param string $name
     * @param mixed $default
     */
    protected function &getParameter($name, $default=null) {

        if (isset($this->parameters[$name]) || array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }

        try {
            return AgaviArrayPathDefinition::getValue($name, $this->parameters, $default);
        } catch (InvalidArgumentException $e) {
            return $default;
        }
    }

    /**
     * Checks if a parameter exists
     * in the array
     * @param string $name
     * @return mixed
     */
    protected function hasParameter($name) {

        if (isset($this->parameters[$name]) || array_key_exists($name, $this->parameters)) {
            return true;
        }

        try {
            return AgaviArrayPathDefinition::hasValue($name, $this->parameters);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Sets a value for a named parameter
     * @param string $name
     * @param mixed $value
     */
    protected function setParameter($name, $value) {
        $this->parameters[$name] = $value;
    }

    /**
     * Sets a reference to a named parameter
     * @param string $name
     * @param mixed $value
     */
    protected function setParameterByRef($name, &$value) {
        $this->parameters[$name] =& $value;
    }

    /**
     * Resets the parameter array to new values
     * @param array $parameters
     */
    protected function setParameters(array $parameters) {
        $this->parameters = $parameters + $this->parameters;
    }

    protected function log($arg1) {
        $args = func_get_args();
        return AppKitAgaviUtil::log($args);
    }

    public function appendParameter($name, $value) {
        if (!isset($this->parameters[$name]) || !is_array($this->parameters[$name])) {
            settype($this->parameters[$name], 'array');
        }

        $this->parameters[$name][] = $value;
    }

}
