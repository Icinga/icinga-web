<?php
/**
* Thrown when an datastore invalid datastore was createdd
*
* @package Icinga_Api
* @category DataStore
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
class DataStoreValidationException extends AppKitException {};

/**
* Thrown when an datastore actions without permission will be called
*
* @package Icinga_Api
* @category DataStore
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/
class DataStorePermissionException extends AppKitException {};

/**
* Abstract base class for all datastores, which handles method decoration via
* modifiers, permission/credential validation for each action.
* @abstract
* @package Icinga_Api
* @category DataStore
*
*
* @author Jannis Moßhammer <jannis.mosshammer@netways.de>
**/

abstract class AbstractDataStoreModel extends IcingaBaseModel {
    protected $modifiers = array();
    protected $requestParameters = array();

    /**
    * Base read function for reading from the dataSource
    * @return mixed     The request result
    * @access private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function doRead() {
        if (!$this->hasPermission($this->canRead())) {
            throw new DataStorePermissionException("Store ".get_class($this)." is not readable");
        }

        return $this->execRead();
    }

    /**
    * Base insert function for the dataSource
    * @return mixed     The request result
    * @access private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function doInsert($valueDescriptor) {
        if (!$this->hasPermission($this->canInsert())) {
            throw new DataStorePermissionException("Store ".get_class($this)." is not insertable");
        }

        return $this->execWrite($valueDescriptor);
    }

    /**
    * Base update function for the dataSource
    * @return mixed     The request result
    * @access private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function doUpdate($idDescriptor) {
        if (!$this->hasPermission($this->canUpdate())) {
            throw new DataStorePermissionException("Store ".get_class($this)." is not updateable");
        }

        return $this->execUpdate($idDescriptor);
    }

    /**
    * Base delete function for the dataSource
    * @return mixed     The request result
    * @access private
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function doDelete($valueDescriptor) {
        if (!$this->hasPermission($this->canDelete())) {
            throw new DataStorePermissionException("Store ".get_class($this)." is not deleteable");
        }

        return $this->execDelete($valueDescriptor);
    }

    /**
    * Inserts data to this data source
    * Overwrite this function with your custom insert function
    *
    * @param mixed  An object describing the new dataset
    * @return mixed Result or null
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function execInsert($valueDescriptor) {}

    /**
    * Reads data from this data source
    * Overwrite this function with your custom read function
    *
    * @return mixed Result or null
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function execRead() {}

    /**
    * Deletes data from this data source
    * Overwrite this function with your custom delete function
    * @param mixed      An object/array/id for the source to delete
    * @return mixed     Result or null
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function execDelete($idDescriptor) {}

    /**
    * Updates data in this data source
    * Overwrite this function with your custom read function
    * @param mixed  An object/array that describes what to update
    * @return mixed Result or null
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function execUpdate($updateDescriptor) {}

    /**
    * Register a new modifier for this store. Should be done statically on @see setupModifier
    * @param    String  The modifier to add (module path)
    * @param    String  The module where the modifier lies
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function registerStoreModifier($modifier, $module= null) {
        if ($modifier instanceof IDataStoreModifier) {
            $this->modifiers[] = $modifier;
        } else {
            $this->modifiers[] = $this->context->getModel($modifier,$module);
        }
    }

    /**
    * Store modifiers must be defined and added here
    * via the @see registerStoreModifier method.
    * Example:
    * <code>
    *   protected function setupModifiers() {
    *       $this->registerStoreModifier('Store.Modifiers.StorePaginationModifier','Api');
    *       $this->registerStoreModifier('Store.Modifiers.StoreSortModifier','Api');
    *       parent::setupModifiers();
    *   }
    * </code>
    *
    *
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function setupModifiers() {

        foreach($this->requestParameters as $parameter=>$value) {
            foreach($this->modifiers as $modifier) {
                $modifier->handleArgument($parameter,$value);
            }
        }
    }

    public function getModifiers() {
        return $this->modifiers;
    }
    /**
    * Initializes the module and calls setupModifers if parameters doesn't contain "noStoreModifierSetup"
    * @param    AgaviContext    The context of the agavi instance
    * @param    Array           An array of submitted parameters
    *                               - AgaviRequestDataHolder request : the request information
    *                               - noStoreModifierSetup : Don't setup modifiers (add when callin parent::initialize)
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function initialize(AgaviContext $context,array $parameters = array()) {
        parent::initialize($context,$parameters);
        $this->defaultInitialize($context,$parameters);
    }

    protected function defaultInitialize(AgaviContext $context,array $parameters = array()) {
        if (isset($parameters["request"])) {
            $this->requestParameters = $parameters["request"]->getParameters();
        }

        if (!isset($parameters["noStoreModifierSetup"])) {
            $this->setupModifiers();
        }

    }

    /**
    * Applies all modifiers to the passed object (may be a Doctrine_Query, a PDO
    * Statement, a sql string.
    * @param $object
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function applyModifiers(&$object) {
        foreach($this->getModifiers() as $mod) {
            $mod->modify($object);
        }
    }

    /**
    * Checks if the current user has this permission
    * @param true|false|String|Array True/False to directly set permission, or an array of agavi credentials to check the user against
    * @return boolean
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    protected function hasPermission($perm) {
        if ($perm === true) {
            return true;
        }

        if ($perm === false) {
            return false;
        }

        $ctx = AgaviContext::getInstance();
        $user = $ctx->getUser();

        if (!$user) {
            return false;
        }

        if (!is_array($perm)) {
            $perm = array($perm);
        }

        foreach($perm as $p) {
            if ($user->hasCredential($p)) {
                return true;
            }
        }
        return false;
    }

    /**
    * Returns true or false whether read can be performed. Override with a custom
    * credential check if you need to.
    *
    * @return boolean or Array with credentials
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function canRead() {
        return true;
    }

    /**
    * Returns true or false whether insert can be performed. Override with a custom
    * credential check if you need to.
    *
    * @return boolean  or Array with credentials
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function canInsert() {
        return false;
    }

    /**
    * Returns true or false whether update can be performed. Override with a custom
    * credential check if you need to.
    *
    * @return boolean  or Array with credentials
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function canUpdate() {
        return false;
    }

    /**
    * Returns true or false whether delete can be performed. Override with a custom
    * credential check if you need to.
    *
    * @return boolean  or Array with credentials
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function canDelete() {
        return false;
    }

    /**
    * Delegates unknown method calls to the modifiers and thereby extends the
    * store by the modifiers methods
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function __call($method,$argument) {
        $found = false;
        foreach($this->getModifiers() as $mod) {
            if (method_exists($mod,$method)) {
                $found = true;
                return call_user_func_array(array($mod,$method),$argument);
            }
        }

        if (!$found) {
            throw new BadMethodCallException("Call to unknown method $method");
        }
    }
}
?>
