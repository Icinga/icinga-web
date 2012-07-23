<?php

class Cronks_Provider_CategorySecuritySuccessView extends CronksBaseView {
    
    public function executeJson(AgaviRequestDataHolder $rd) {
        return json_encode(array(
            'success'     => $this->getAttribute('success', false),
            'errors'      => $this->getAttribute('errors', array()),
            'category'    => $this->getAttribute('category'),
            'roles'       => $this->getAttribute('roles'),
            'role_uids'   => $this->getAttribute('role_uids'),
            'principals'  => $this->getAttribute('principals'),
        ));
    }
    
}