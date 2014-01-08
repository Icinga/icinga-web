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

/*
 * We want to use the global search with our Cronks object search window
 * Just registering the handler ...
 */
(function() {
    var s = Icinga.Cronks.search.SearchHandler;
    s.setProxyUrl("<?php echo $ro->gen('modules.cronks.objectsearch.json')?>");
    s.setMinimumChars(<?php echo (int)AgaviConfig::get('modules.cronks.search.numberMinimumLetters', 2); ?>);
    s.register();
})();

Cronk.util.initEnvironment('viewport-center', function() {
    
    var portal = new Icinga.Cronks.System.CronkPortal({
       customCronkCredential: <?php echo json_encode((boolean)$us->hasCredential('icinga.cronk.custom')); ?>
    });
    
    AppKit.util.Layout.addTo(portal);
    
    if(<?php echo $rd->getParameter("isURLView") ? 1 : 0 ?>) {
        Ext.getCmp('cronk-tabs').setURLTab(<?php echo $rd->getParameter('URLData');?>);
    }
    
    AppKit.util.Layout.doLayout();
        
}, { run: true, extready: true });

</script>
