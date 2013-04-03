<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
    var panel = new Icinga.Cronks.System.CronkListingPanel({
        combinedProviderUrl: '<?php echo $ro->gen("modules.cronks.provider.cronks.combined"); ?>',
        id: 'cronk-listing-panel',
        stateId: 'cronk-listing-panel',
        customCronkCredential: <?php echo json_encode((boolean)$us->hasCredential('icinga.cronk.custom')); ?>,
        cronkUrlBase: '<?php echo $ro->gen('modules.cronks.open'); ?>'
    });

    <?php if ($us->hasCredential('icinga.cronk.category.admin')) { ?>
        panel.setCategoryAdmin(true);
    <?php } ?>

    <?php if ($us->hasCredential('icinga.cronk.admin')) { ?>
        panel.setCronkAdmin(true);
    <?php } ?>
    
    this.add(panel);
    
    this.doLayout.defer(500);
});
</script>
