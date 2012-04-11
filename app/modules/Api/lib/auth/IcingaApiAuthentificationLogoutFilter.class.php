<?php

class IcingaApiAuthentificationLogoutFilter extends AgaviFilter implements AgaviIActionFilter {
    /**
     * If authkey was used, do a logout after executing
     * 
     * (non-PHPdoc)
     * @see AgaviIFilter::execute()
     */
    public function execute(AgaviFilterChain $filterChain, AgaviExecutionContainer $container) {
        $filterChain->execute($container);
        $flag = (bool)$container->getAttribute('flag', 'org.icinga.api.auth', false);
        $user = $container->getContext()->getUser();
        
        if ($flag === true && $user->isAuthenticated()) {
            $user->doLogout();
            session_destroy(); // Remove session from database
        }
    }
}