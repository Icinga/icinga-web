<?php

class Api_ApiDataStoreProviderAction extends IcingaApiBaseAction implements IAppKitDataStoreProviderAction
{
	/**
	 * Returns the default view if the action does not serve the request
	 * method used.
	 *
	 * @return     mixed <ul>
	 *                     <li>A string containing the view name associated
	 *                     with this action; or</li>
	 *                     <li>An array with two indices: the parent module
	 *                     of the view to be executed and the view to be
	 *                     executed.</li>
	 *                   </ul>
	 */
	public function getDefaultViewName()
	{
		return 'Success';
	}


    public function getDataStoreModel() {
       /* return array(
           /* array(
                "module" => "Api",
                model" => "Store.IcingaApiDataStore"
            ), array(
                "module" => "Api",
                "model" => "Store.IcingaApiDataStore"
            ), array(
                "module" => "Api",
                "model" => "Store.IcingaApiDataStore"
            ),*/ return array(
                "module" => "Api",
                "model" => "Store.HostStore"
            );
       // );
    }
}

?>
