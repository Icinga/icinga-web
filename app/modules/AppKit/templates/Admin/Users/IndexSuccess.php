<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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
?>
<script type="text/javascript">
Ext.onReady(function() {

    var userManager = new AppKit.Admin.UserManager({
        userProviderURI: '<?php echo $ro->gen("modules.appkit.data.users")?>',
        roleProviderURI: '<?php echo $ro->gen("modules.appkit.data.groups")?>',
        taskURI: '<?php echo $ro->gen("modules.appkit.admin.tasks.control") ?>',
        authTypes: <?php echo json_encode(array_keys(AgaviConfig::get("modules.appkit.auth.provider"))); ?>,
        availablePrincipals: <?php echo json_encode($t['principals']); ?>
    });
    AppKit.util.Layout.getCenter().add(userManager);
    AppKit.util.Layout.doLayout();
});
</script>
