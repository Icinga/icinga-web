<?php

class AppKit_Login_AjaxLoginAction extends AppKitBaseAction {
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

    public function executeRead(AgaviRequestDataHolder $rd) {
        return $this->getDefaultViewName();
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        $username = $rd->getParameter('username');
        $password = $rd->getParameter('password');
        $do = $rd->getParameter('dologin');

        $this->setAttribute('authenticated', false);
        $this->setAttribute('executed', false);

        if ($do) {

            $this->setAttribute('executed', true);
            $user = $this->getContext()->getUser();

            try {
                $user->doLogin($username, $password);
                /*
                 * Behaviour for blocking whole access if no icinga access
                 */
                //if(!$user->hasCredential("icinga.user")) {
                //    $user->doLogout();
                //    $this->setAttribute('authenticated', false);
                //}
                
                $this->setAttribute('authenticated', true);
            } catch (AgaviSecurityException $e) {
                $this->setAttribute('authenticated', false);
            }

        }

        return $this->getDefaultViewName();
    }

    public function handleError(AgaviRequestDataHolder $rd) {
        return $this->getDefaultViewName();
    }
}

?>
