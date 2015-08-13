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

Cronk.util.initEnvironment("<?php echo $rd->getParameter('parentid'); ?>", function() {

    var cronk = new Icinga.Cronks.System.MonitorPerformance.Cronk({
        hostThreshold: <?php echo $rd->getParameter('hostLatencyWarningThreshold',10000);?>,
        serviceThreshold: <?php echo $rd->getParameter('serviceLatencyWarningThreshold',10000);?>,
        refreshInterval: <?php echo $us->getPrefVal('org.icinga.status.refreshTime', 60); ?>,
        dataProvider: '<?php echo $ro->gen('modules.cronks.monitorPerformance.json') ?>',
        storeId: 'overall-status-store'
    });

    this.getParent().removeAll();

    this.add(cronk);

    this.doLayout();
});

</script>
