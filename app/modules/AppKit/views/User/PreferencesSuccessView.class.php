<?php

class AppKit_User_PreferencesSuccessView extends AppKitBaseView {
    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $tm = $this->getContext()->getTranslationManager();
        $user = $this->getContext()->getUser()->getNsmUser();
        $this->setAttributeByRef("user",$user);
        $this->setAttribute('title',$tm->_('Preferences for user').' \''.$user->get('user_name').'\'');
        $this->setAttribute('isDemoSystem',$this->getContext()->getUser()->hasCredential('icinga.demoMode'));

    }


}

?>