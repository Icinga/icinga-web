<?php

class IcingaApiAuthentificationRoutingCallback extends AgaviRoutingCallback {
    
    const INSUFFICIENT_MSG = 'User has insufficient rights to access the api';
    
    /**
     * @var AppKitSecurityUser
     */
    private $user = null;
    
    public function initialize(AgaviContext $context, array &$route) {
        parent::initialize($context, $route);
        $this->user = $this->context->getUser();
    }
    
    private function checkAuthorisation() {
        
        if (!$this->user->isAuthenticated()) {
            return false;
        }
        
        if (!$this->user->hasCredential("appkit.api.access") && !$this->user->hasCredential("icinga.user")) {
            return false;
        }
        
        return true;
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
        
        if ($this->checkAuthorisation() == false) {
            $errors[] = self::INSUFFICIENT_MSG;
        }
        
        if (count($errors)) {
            $container->setAttributeByRef('errors', $errors, 'org.icinga.api.auth');
            $container->setAttribute('success', false, 'org.icinga.api.auth');
            return false;
        }
        
        $container->setAttribute('success', true, 'org.icinga.api.auth');
        return true;
    }
    
    public function onNotMatched(AgaviExecutionContainer $container) {
        $garbage = array();
        return $this->onMatched($garbage, $container);
    }
}