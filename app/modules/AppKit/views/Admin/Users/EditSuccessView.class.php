<?php

class AppKit_Admin_Users_EditSuccessView extends AppKitBaseView {
    public function executeSimple(AgaviRequestDataHolder $rd) {
        if ($this->getContainer()->getRequestMethod() == "write") {
            return null;
        }

        $this->setupHtml($rd);
        $useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');
        $roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');

        if ($rd->getParameter('id') == 'new') {
            $user = new NsmUser();
            $this->setAttribute('title', 'Create a new user');
            $this->setAttribute('new',true);
        } else {
            $user = $useradmin->getUserById($rd->getParameter('id'));

            $this->setAttribute('title', 'Edit '. $user->givenName());
        }

        $authTypes = $this->getAuthTypes();

        $this->setAttribute('user', $user);
        $this->setAttribute('roles', $roleadmin->getRoleCollection());
        $this->setAttribute('container', $rd->getParameter('container',null));
        $this->setAttribute('authTypes',"[['".implode("'],['",$authTypes)."']]");
        $exec = $this->getContext()->getController()->createExecutionContainer("AppKit","Admin.PrincipalEditor",null,'simple');
        $resp = $exec->execute()->getContent();
        $this->setAttribute("principal_editor",$resp);
    }

    public function getAuthTypes() {
        return array_keys(AgaviConfig::get("modules.appkit.auth.provider"));

    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        try {
            $useradmin = $this->getContext()->getModel('UserAdmin', 'AppKit');
            $roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');

            if ($rd->getParameter('id') == 'new') {
                $user = new NsmUser();
                $this->setAttribute('title', 'Create a new user');
            } else {
                $user = $useradmin->getUserById($rd->getParameter('id'));

                $this->setAttribute('title', 'Edit '. $user->givenName());
            }


            $this->setAttribute('user', $user);
            $this->setAttribute('roles', $roleadmin->getRoleCollection());
            $exec = $this->getContext()->getController()->createExecutionContainer("AppKit","Admin.PrincipalEditor",null,'simple');
            $resp = $exec->execute()->getContent();
            $this->setAttribute("principal_editor",$resp);
        } catch (AppKitDoctrineException $e) {
            $this->getMessageQueue()->enqueue(AppKitMessageQueueItem::Error($e->getMessage()));
            $this->setAttribute('title', 'An error occured');
        }


    }
}

?>