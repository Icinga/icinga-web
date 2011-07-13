<?php


class AppKit_Admin_Users_EditAction extends AppKitBaseAction {
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

    public function executeWrite(AgaviRequestDataHolder $rd) {
        // We need the execute method to work with parameter od the request!
        try {
            Doctrine_Manager::connection()->beginTransaction();

            // Our user model
            $useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');

            $padmin = $this->getContext()->getModel('PrincipalAdmin', 'AppKit');

            // This is our user!
            if ($rd->getParameter('id') == 'new') {
                $user = new NsmUser();
            } else {
                $user = $useradmin->getUserById($rd->getParameter('id'));
            }

            // Update the simple data!
            $useradmin->updateUserData($user, $rd);

            if ($rd->getParameter('password_validate', false) !== false) {
                $useradmin->updateUserPassword($user, $rd->getParameter('password_validate'));
            }

            // Updating the roles
            $useradmin->updateUserroles($user, $rd->getParameter('userroles', array()));

            $padmin->updatePrincipalValueData(
                $user->NsmPrincipal,
                $rd->getParameter('principal_target', array()),
                $rd->getParameter('principal_value', array())
            );

            // Give notice!

            Doctrine_Manager::connection()->commit();
        } catch (Exception $e) {
            try {
                Doctrine_Manager::connection()->rollback();
            } catch (Doctrine_Transaction_Exception $e) {}

            echo $e->getMessage();

        }

        return 'Success';
    }

    public function handleError(AgaviRequestDataHolder $rd) {

        // Let the form populate filter display the errors!
        return 'Success';
    }

}

?>
