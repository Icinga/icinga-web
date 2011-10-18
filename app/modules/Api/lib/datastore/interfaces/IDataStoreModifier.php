<?php
/**
* Interface used to modify datastores, eg. with filtering, sorting, etc.
* (Public) Methods defined in Modifiers will be automatically accessible via
* the DataStore class being modified.
*
* Here's an simple example that always adds a where clause with a certain value
*  to a query:
* <code>
* class Test implements IDataStoreModifier {
*     private $user; // the user to filter for
*     public setUser($u) {
*         $this->user = $u;
*     }
*     public getUser($u) {
*         return $u;
*     }
*     public function handleArgument($name,$value) {
*         // only accept the onlyForUser parameter, ignore others
*         if($name == "onlyForUser") {
*             $this->setUser($value);
*         }
*     }
*     // tell the client side that the filter parameter is called "onlyForUser"
*     // this value will only be exported, you have to use support it in your
*     // client-side code
*     public function() getMappedArguments() {
*         return array("filter" => "onlyForUser");
*     }
*
*     // Identify this modifiert, its parameters, etc. for the client
*     // This value will be exported to a json which can be read by the client
*     public function __getJSDescriptor() {
*         return array(
*             "type"   => "userFilter",
*             "params" => $this->getMappedArguments()
*         );
*     }
*     // this is the core function that will be called with a prepared query
*     public function modify(&$o) {
*         $o->andWhere("user_name = ?",$this->getUser);
*         // yes, that's all
*     }
* }
*
* // A datastore implementing this modifier can now directly set the user via
* // $ds->setUser("john_doe");
* </code>
*
* @package Icinga_Api
* @category DataStore
* @author Jannis Mosshammer <jannis.mosshammer@netways.de>
*/
interface IDataStoreModifier {
    /**
    * Entry point for the DataStore. Takes arguments with name and
    * value and sets up the modifer (or throws exceptions if it fails)
    * @param    String      The name of the argument to handle
    * @param    String      The value of the argument to handle
    * @throws   AppKitException If any error occurs, a sublcass of AppKitException
    *                           Will be thrown
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function handleArgument($name,$value);

    /**
    * Returns an array of argument names that will be handled by this modifier
    *
    * @return   Array       An array of argument names
    */
    public function getMappedArguments();

    /**
    * This is called by the datastore class in order to modify the query/datasource
    * by this modifier. $o is a reference to any object you like, but it's likely
    * that you want to use @see IcingaDoctrine_Query objects.
    *
    * @param    Mixed       A reference to the object that should be modified
    *
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
    public function modify(&$o);

    /**
    * Returns an array of information which will be used by the clients datastore
    * to handle the stores abilities
    *
    * @return Array         An array that describes this moodifier for the client side
    * @internal 
    * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
    **/
//    public function __getJSDescriptor();
}
