<?php

class AppKit_Admin_Users_RemoveAction extends AppKitBaseAction {
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

    public function executeWrite(AgaviRequestDataHolder $rd) {
        try {
            Doctrine_Manager::connection()->beginTransaction();
            $useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');
            $padmin = $this->getContext()->getModel('PrincipalAdmin', 'AppKit');
            $ids = $rd->getParameter("user_id",false);
            foreach($ids as $id) {
                $user = $useradmin->getUserById($id);

                if (!$user) {
                    continue;
                }

                $useradmin->removeUser($user);
            }
            Doctrine_Manager::connection()->commit();
        } catch (Exception $e) {
            try {
                Doctrine_Manager::connection()->rollback();
            } catch (Doctrine_Transaction_Exception $e) {}

            echo $e;
            echo $e;
        }

        return 'Success';
    }

    public function getCredentials() {
        return array('appkit.admin', 'appkit.admin.users');
    }
}

?>