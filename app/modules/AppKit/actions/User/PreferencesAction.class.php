<?php

class AppKit_User_PreferencesAction extends AppKitBaseAction {
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

    public function isSecure() {
        return true;
    }

    public function getCredentials() {
        return array('icinga.user');
    }

    public function executeRead(AgaviRequestDataHolder $rd) {
        return "Success";
    }

    public function executeWrite(AgaviRequestDataHolder $rd) {
        $user = $this->getContext()->getUser();

        if (!$user) {
            throw new AppKitException("User doesn't exist!");
        }

        if (($pass = $rd->getParameter("newPass",false))) {
            $nsm = $user->getNsmUser();
            $nsm->updatePassword($pass);
            $nsm->save();
        }

        else {

            $key = $rd->getParameter("upref_key", false);
            $batch = $rd->getParameter('params',false);

            if ($key) {

                $val = $rd->getParameter("upref_val");
                $isLong = $rd->getParameter("isLong",false);

                if ($val && !$rd->getParameter("remove",false)) {
                    $this->setPreference($user,$key,$val,$isLong);
                } else if ($rd->getParameter("remove")) {
                    $user->getNsmUser()->delPref($key);
                }
            } else if ($batch) {
                foreach($batch as $preference) {
                    $this->setPreference($user,	$preference["upref_key"],
                                         $preference["upref_val"],$preference["isLong"]);
                }
            }

        }

        return "Success";
    }
    public function setPreference($user, $key,$val,$isLong) {
        $user->getNsmUser()->setPref($key,$val,true,false);
        //$this->getContext()->getLoggerManager()->("User %s changed %s to %s",$user->getNsmUser()->get("user_name"), $key,$val);
    }
}

?>