<?php

class AppKit_DataProvider_UserProviderAction extends AppKitBaseAction {
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
    public function getDefaultViewName() {
        return 'Success';
    }

    public function isSecure() {
        return true;
    }

    public function getCredentials() {
        return array('appkit.admin', 'appkit.admin.users');
    }

    public function executeRead(AgaviRequestDataHolder $rd) {
        // We need the execute method to work with parameter od the request!
        return 'Success';
    }

    public function handleError(AgaviRequestDataHolder $rd) {
        // Let the form populate filter display the errors!
        return 'Success';
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        return 'Success';
    }

}

?>