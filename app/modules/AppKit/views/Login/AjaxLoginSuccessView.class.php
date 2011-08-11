<?php

class AppKit_Login_AjaxLoginSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        if ($this->getContext()->getUser()->isAuthenticated() !== true) {
            $this->getResponse()->setHttpStatusCode('403');
        }

        $this->setAttribute('message', false);
        $message = AgaviConfig::get('modules.appkit.auth.message', false);

        if ($rd->has('cookies', 'icinga-web-loginname') && AgaviConfig::get('modules.appkit.auth.behaviour.store_loginname', false)===true) {
            $this->setAttribute('username', $rd->get('cookies', 'icinga-web-loginname'));
        }

        if ($message !== false && is_array($message)) {
            if (isset($message['show']) && $message['show']==true) {

                if (isset($message['include_file']) && file_exists($message['include_file'])) {
                    $text = file_get_contents($message['include_file']);
                } else {
                    $text = isset($message['text']) ? $message['text'] : null;
                }

                if ($text) {
                    $text = AppKitAgaviUtil::replaceConfigVars($text);
                }

                $this->setAttribute('message', true);
                $this->setAttribute('message_text', $text);
                $this->setAttribute('message_title', $message['title']);
                $this->setAttribute('message_expand_first', isset($message['expand_first']) ? (bool)$message['expand_first'] : false);
            }
        }
    }

    public function executeJson(AgaviRequestDataHolder $rd) {

        $authenticated = false;
        $errors = array();
        $user = $this->getContext()->getUser();

        if ($this->getAttribute('authenticated', false) === true && $user->isAuthenticated() && $this->getAttribute('executed', false) === true) {
            $authenticated = true;
        } else {
            $errors['username'] = 'Login failed!';
            $this->getResponse()->setHttpStatusCode('403');
        }

        return json_encode(array(
                               'success'		=> $authenticated,
                               'errors'		=> $errors
                           ));

    }
}

?>