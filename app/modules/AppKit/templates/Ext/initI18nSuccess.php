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

    $files = $t['files'];
    $default = $t['default'];
    if (!is_array($files) && !count($files)) return;
?>
AppKit.onReady(function() {
    var l = {};
    <?php foreach ($files as $domain=>$json): ?>

    if (typeof(l['<?php echo $domain; ?>']) == "undefined") {
        l['<?php echo $domain; ?>'] = {};
    }

    Ext.apply(l['<?php echo $domain; ?>'], <?php echo $json[1]; ?>); 

    <?php endforeach; ?>
    
    AppKit.util.Gettext = new Gettext({
        domain: "<?php echo $default; ?>",
        locale_data: l
    });
    
    // Make this more global available
    window._ = AppKit.util.Gettext.gettext.createDelegate(AppKit.util.Gettext);
    window._gt = AppKit.util.Gettext;
}, window);
