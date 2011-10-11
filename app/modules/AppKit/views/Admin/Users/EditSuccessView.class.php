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

    /**
     * Helps to determine if specific provider handler is configured or enabled
     *
     * @param type $class 
     * @return boolean
     */
    public function usesProviderClass($class) {
        foreach($this->config as $id=>$cfg) {
            if(!isset($cfg["auth_provider"]) || !isset($cfg["auth_enable"]))
                continue;
            if($cfg["auth_enable"] == false)
                continue;
            if($cfg["auth_provider"] != $class)
                continue;
            return true;
        }
        return false;
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