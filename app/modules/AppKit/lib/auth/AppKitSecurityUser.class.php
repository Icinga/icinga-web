<?php

/**
 * AppKit implementation of the agavi role based security user
 * @author mhein
 * @author jmosshammer
 *
 */
class AppKitSecurityUser extends AgaviRbacSecurityUser {

    /**
     * Value to use database role system
     * @var string
     */
    const ROLES_SOURCE_DB = 'DB';

    /**
     *
     * Value to use XML based roles
     * @var string
     */
    const ROLES_SOURCE_XML = 'XML';

    /**
     * Static value for object paraneter holds our NsmUser object
     * @var string
     */
    const USEROBJ_ATTRIBUTE = 'userobj';
    
    /**
     * Attribute name of the currentProvider
     * @var string
     */
    const AUTHPROVIDER_ATTRIBUTE = 'currentProvider';

    /**
     * List of roles applies to this user
     * @var array
     */
    protected $role_names = array();

    /**
     * Which source of roles we want to use for out user?
     * Change this to its appropriate constante to change.
     * @var string
     */
    private static $role_source = self::ROLES_SOURCE_DB;

    /**
     * (non-PHPdoc)
     * @see AgaviRbacSecurityUser::getRoles()
     */
    public function  getRoles() {
        if (count($this->role_names) <= 0) {
            foreach($this->getNsmUser()->NsmRole as $role) {
                $this->role_names[$role->role_id] = $role->role_name;
                $this->addParentRoles($role);                
            }

        }

        return $this->role_names;
    }

    private function addParentRoles(NsmRole $role) {
        if($role->hasParent()) {
            $p = $role->getParent();
            $this->role_names[$p->role_id] = $p->role_name;
            $this->addParentRoles($p);
        }
    }
    
    /**
     * Shortcut method to authenticate user with auth key
     * @param string $key
     */
    public function doAuthKeyLogin($key) {
        $this->doLogin($key,$key);
    }

    /**
     * Login method, uses the AppKitAuthProvider to determine if this is correct
     * @param string $username
     * @param string $password
     * @param boolean $isHashedPassword This is not needed at the moment, maybe for cookie autologin later
     * @return boolean if the login was successfull
     * @throws AppKitSecurityUserException
     * @author Marius Hein
     */
    public function doLogin($username, $password, $isHashedPassword=false) {

        $dispatcher = $this->getContext()->getModel('Auth.Dispatch', 'AppKit');

        try {

            $user = $dispatcher->doAuthenticate($username, $password);

            if ($user instanceof NsmUser && $user->user_id>0) {
                // Start from scratch
                $this->clearCredentials();

                // Set authenticated
                $this->setAuthenticated(true);

                // Load the corresponding db (Nsm-) user into the session
                $this->loadUserAttribute($user);

                // Grant related roles
                $this->applyDoctrineUserRoles($user);
                
                $this->setAttribute('currentProvider', $dispatcher->getCurrentProvider()->getName());
                
                // Give notice
                $this->getContext()->getLoggerManager()
                ->log(sprintf('User %s (%s) logged in!', $username, $user->givenName()), AgaviLogger::INFO);

                return true;

            }

        } catch (AgaviSecurityException $e) {
            // Log authentification failure
            $this->getContext()->getLoggerManager()->log(sprintf('Userlogin by %s failed!', $username), AgaviLogger::ERROR);

            // Rethrow
            throw $e;
        }

    }

    /**
     * Initiate the logout
     * @return boolean
     * @throws AppKitException
     * @author Marius Hein
     */
    public function doLogout() {
        $this->clearCredentials();
        $this->setAuthenticated(false);

        $this->getContext()->getLoggerManager()
        ->log(sprintf('User %s (%s) logged out!', $this->getAttribute('userobj')->user_name, $this->getAttribute('userobj')->givenName()), AgaviLogger::INFO);

        return true;
    }

    /**
     * This adds attributes to our session (At the moment only the NsmUser)
     * @param NsmUser $user
     * @return true always!
     * @author Marius Hein
     */
    private function loadUserAttribute(NsmUser &$user) {
        $this->setAttributeByRef('userobj', $user);
        return true;
    }

    /**
     * Applying the roles the the agavi rbac struct
     * @param NsmUser $user
     * @return boolean always true
     * @author Marius Hein
     */
    private function applyDoctrineUserRoles(NsmUser &$user) {
        if (self::$role_source == self::ROLES_SOURCE_XML) {
            foreach($user->NsmRole as $role) {
                $this->grantRole($role->role_name);

            }
        } else {
            $this->getCredentialsFromDB($user);
        }

        return true;
    }

    /**
     * Adding credential from database to the rbac user
     * @param NsmUser $user
     */
    private function getCredentialsFromDB(NsmUser &$user) {
        foreach($user->NsmRole as $role) {
            $this->roles[] = $role;
            $next = $role;
            $this->addCredentialsFromRole($role);

            while ($next->hasParent()) {
                $next = $next->getParent();
                $this->addCredentialsFromRole($next);
                
                $this->roles[] = $next;

            }
            
        }
        
        foreach($user->getTargets("credential") as $credential) {
            $this->addCredential($credential->get("target_name"));

        }
   
    }

    /**
     * Adding credentials from role
     * @param NsmRole $role
     */
    private function addCredentialsFromRole(NsmRole &$role) {
        foreach($role->getTargets('credential') as $credential) {
            $this->addCredential($credential->get('target_name'));
            
        }
    }

    /**
     * Returns the doctrine user object
     * @return NsmUser
     * @throws AppKitDoctrineException
     * @author Marius Hein
     */
    public function getNsmUser($noThrow = false) {
        $user =& $this->getAttribute(self::USEROBJ_ATTRIBUTE);

        if ($user instanceof NsmUser) {
            return $user;
        }

        if (!$noThrow) {
            throw new AppKitDoctrineException('User attribute is not a NsmUser!');
        }
    }

    /**
     * reduced call for setPref within NsmUser
     * @param string $key
     * @param mixed $val
     * @param boolean $overwrite
     * @param boolean $blob
     * @return mixed
     * @author Marius Hein
     */
    public function setPref($key, $val, $overwrite = true, $blob = false) {
        return $this->getNsmUser()->setPref($key, $val, $overwrite, $blob);
    }

    /**
     * reduced call for getPrefVal within NsmUser
     * @param string $key
     * @param mixed $default
     * @param boolean $blob
     * @return mixed
     * @author Marius Hein
     */
    public function getPrefVal($key, $default=null, $blob = false) {
        return $this->getNsmUser()->getPrefVal($key, $default, $blob);
    }

    /**
     * All user preferences at once
     * @return array List of the preferences
     */
    public function getPreferences() {
        return $this->getNsmUser()->getPreferences();
    }

    /**
     * reduced call for delPref within NsmUser
     * @param string $key
     * @return boolean
     * @author Marius Hein
     */
    public function delPref($key) {
        return $this->getNsmUser()->delPref($ley);
    }

    /**
     * (non-PHPdoc)
     * @see AgaviRbacSecurityUser::loadDefinitions()
     */
    protected function loadDefinitions() {
        if (self::$role_source == self::ROLES_SOURCE_XML) {
            parent::loadDefinitions();
        }

    }


}

class AppKitSecurityUserException extends AppKitException {}
