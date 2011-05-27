<?php

class AppKit_LoginAction extends AppKitBaseAction {
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


    public function executeRead(AgaviRequestDataHolder $rd) {
        $user = $this->getContext()->getUser();

        // Try http basic auth if it is configured
        if ($user->isAuthenticated() === false && AppKitFactories::getInstance()->getFactory('AuthProvider') instanceof AppKitAuthProviderHttpBasic) {
            try {
                $user->doLogin('__DUMMY__', '__DUMMY__');
                $this->setAttribute('redirect', 'appkit');
                return 'Success';
            } catch (AppKitSecurityUserException $e) {
                $this->getMessageQueue()->enqueue(AppKitMessageQueueItem::Error($e->getMessage()));
                return 'Input';
            }
        }

        if ($user->isAuthenticated()) {
            return 'Success';
        }

        return 'Input';
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        $username = $rd->getParameter('username');
        $password = $rd->getParameter('password');

        $user = $this->getContext()->getUser();

        try {
            $user->doLogin($username, $password);
            return 'Success';
        } catch (AppKitSecurityUserException $e) {
            // $this->setAttribute('error', $e->getMessage());
            $this->getMessageQueue()->enqueue(AppKitMessageQueueItem::Error($e->getMessage()));
            return 'Input';
        }

        return 'Input';
    }

    public function handleError(AgaviRequestDataHolder $rd) {
        return 'Input';
    }
}

?>