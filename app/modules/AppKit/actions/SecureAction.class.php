<?php

class AppKit_SecureAction extends AppKitBaseAction {
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

    public function execute(AgaviRequestDataHolder $rd) {
        $this->setAttributes($this->getContainer()->getAttributes('org.agavi.controller.forwards.secure'));

        // Okay log this abuse!
        $msg = new AgaviLoggerMessage(sprintf(
                                          'Access with sufficient privileges to %s (%s) by %s (%s)',
                                          $this->getAttribute('requested_action'),
                                          $this->getAttribute('requested_module'),
                                          $this->getContext()->getUser()->getAttribute('userobj')->user_name,
                                          $this->getContext()->getUser()->getAttribute('userobj')->givenName()
                                      ), AgaviLogger::WARN);

        $this->getContext()->getLoggerManager()->log($msg);

        // Return an unauthorized http response
        $this->getContainer()->getResponse()->setHttpStatusCode(401);

        return $this->getDefaultViewName();
    }
}

?>