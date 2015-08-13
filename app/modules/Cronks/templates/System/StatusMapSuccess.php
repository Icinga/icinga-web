<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-2015 Icinga Developer Team.
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
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {
    this.getParent().removeAll();

    var map = new Icinga.Cronks.StatusMapPanel({
        stateuid: this.stateuid,
        url: "<?php echo $ro->gen('modules.cronks.statusMap.json'); ?>",
        refreshTime : "<?php echo $us->getPrefVal('org.icinga.status.refreshTime', 60); ?>"
    });
    if(Ext.isDefined(this.state)) {
        map.applyState(this.state);
    }
    // Link some object to the cronk registry object

    map.on("afterrender",function() {
        if(this.registry.local)
            this.registry.local.statusmap = map;

    },this);


    this.add(map);

    this.doLayout(true);

});
</script>
