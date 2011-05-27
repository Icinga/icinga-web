<?php

class AppKit_DataProvider_IconProviderAction extends AppKitBaseAction {
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

    public function execute(AgaviRequestDataHolder $rd) {

        $model = $this->getContext()->getModel('IconFiles', 'AppKit', array(
                'path'	=> $rd->getParameter('path'),
                'query'	=> $rd->getParameter('query')
                                               ));

        $this->setAttributeByRef('filemodel', $model);

        return 'Success';
    }

    public function handleError(AgaviRequestDataHolder $rd) {
        return 'Success';
    }
}

?>