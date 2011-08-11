<?
/**
* IAppKitDataStoreProviderAction
* Tags an action as an provider for DataStores, which will be automatically parsed
* for client-side use
*
*
**/
interface IAppKitDataStoreProviderAction {
    /**
    * Returns the class-name of the datastore to use
    **/
    public function getDataStoreModel();
}
