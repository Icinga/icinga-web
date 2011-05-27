<?php

class AppKit_Ext_ApplicationStateAction extends AppKitBaseAction {
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

    public function handleError(AgaviRequestDataHolder $rd) {
        return $this->getDefaultViewName();
    }

    public function executeRead(AgaviParameterHolder $rd) {
        return $this->getDefaultViewName();
    }

    public function executeWrite(AgaviParameterHolder $rd) {

        $cmd = $rd->getParameter('cmd', 'read');
        $provider = $this->getContext()->getModel('Ext.ApplicationState', 'AppKit');

        switch ($cmd) {

            case 'write':
                if (($data = $rd->getParameter('data', null))) {
                    $provider->writeState($data);
                }

                break;

            case 'read':
            default:

                break;
        }

        return $this->getDefaultViewName();
    }
}

?>