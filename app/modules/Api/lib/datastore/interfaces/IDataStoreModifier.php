<?php
/**
* Interface used to modify datastores, eg. with filtering, sorting, etc.
*
* @author Jannis Mosshammer <jannis.mosshammer@netways.de>
*/
interface IDataStoreModifier 
{
    /**
    * Entry point for the DataStore. Takes arguments with name and 
    * value and sets up the modifer (or throws exceptions if it fails)
    * @param    String      The name of the argument to handle
    * @param    String      The value of the argument to handle
    * @throws   AppKitException If any error occurs, a sublcass of AppKitException
    *                           Will be thrown
    **/
    public function handleArgument($name,$value);
    
    /**
    * Returns an array of argument names that will be handled by this modifier
    *
    * @return   Array       An array of argument names
    */
    public function getMappedArguments();

    /**
    * Returns an array of information which will be used by the clients datastore
    * to handle the stores abilities
    *
    * @return Array         An array that describes this moodifier for the client side 
    **/
    public function __getJSDescriptor();
}
