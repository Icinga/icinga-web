<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}

    $username = $tm->_('Guest');
    $auth = false;

    if ($us->isAuthenticated()) {
        $username = $us->getNsmUser()->givenName();
        $auth = true;
        $pref = $us->getPreferences();
     
        $pref["author_name"] = $us->getNsmUser()->user_name;
    }
    else {
        $pref = new stdClass();
    }


?>

<script type="text/javascript">
Ext.onReady(function() {
     
    <?php if ($auth === true) { ?>
    AppKit.onReady(function() {
        AppKit.setPreferences(<?php echo json_encode($pref); ?>);
    });
    <?php } ?>

    // Default ajax timeout
    Ext.Ajax.timeout = Number(<?php echo AgaviConfig::get('modules.appkit.ajax.timeout', 120000); ?>);
        
    (function() {
        var pub = {};
        var _LA = AppKit.util.Layout;

        var navbar = new AppKit.util.AppKitNavBar({
            logoutURL:      '<?php echo $ro->gen("modules.appkit.logout", array('logout' => 1)); ?>',
            preferenceURL:  '<?php echo $ro->gen("my.preferences"); ?>',
            username:       '<?php echo addslashes($username); ?>',
            hasAuth:        <?php echo ($auth == true) ? 'true' : 'false' ?>,
            menuData:       <?php echo $t['json_menu_data']; ?> 
        }); 
        _LA.addTo(navbar,null,'north'); 
    })();
    
});
</script>
