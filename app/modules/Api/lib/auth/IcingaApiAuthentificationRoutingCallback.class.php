<?php

class IcingaApiAuthentificationRoutingCallback extends AgaviRoutingCallback {
    
    /**
     * @var AppKitSecurityUser
     */
    private $user = null;
    
    public function initialize(AgaviContext $context, array &$route) {
        parent::initialize($context, $route);
        $this->user = $this->context->getUser();
    }
    
    public function onMatched(array &$parameters, AgaviExecutionContainer $container) {
        $validation = $container->getValidationManager();
        
        $errors = array ();
        
        if (isset($parameters['authkey'])) {
            try {
                $this->user->doAuthKeyLogin($parameters['authkey']);
            } catch (AgaviSecurityException $e) {
                $errors[] = 'Log in failed by authkey';
            }
        }
        
        try {
            if($this->user->getNsmUser()->getTarget('IcingaCommandRo')) {
                $errors[] = 'Commands are disabled for this user';
            }
        } catch (AppKitDoctrineException $e) {
            // PASS
        }
        
        if (!$this->user->hasCredential("appkit.api.access") && !$this->user->hasCredential("appkit.user")) {
            $errors[] = 'User has insufficient rights to access the api';
        }
        
        if (count($errors)) {
            $container->setAttributeByRef('errors', $errors, 'org.icinga.api.auth');
            $container->setAttribute('success', false, 'org.icinga.api.auth');
        }
        $container->setAttribute('success', false, 'org.icinga.api.auth');
        return true;
    }
}