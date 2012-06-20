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
?>
<script type="text/javascript">
    Ext.onReady(function () {
        var lTitle = _("We're Icinga");
        AppKit.util.Dom.makeImage('icinga-image-home', 'icinga.icinga-logo', { alt: lTitle , style: 'width: 200px' });
        
        AppKit.util.Dom.makeImage('icinga-image-bugreport', 'icinga.bugreport', { alt: lTitle });
        AppKit.util.Dom.makeImage('icinga-image-support', 'icinga.support', { alt: lTitle });
        AppKit.util.Dom.makeImage('icinga-image-wiki', 'icinga.wiki', { alt: lTitle });
        AppKit.util.Dom.makeImage('icinga-image-translate', 'icinga.translate', { alt: lTitle });
    });
</script>
<div style="width: 200px; margin: 0 auto;">
    <a id="icinga-image-home" href="http://www.icinga.org/"></a>
</div>

<h2 style="text-align: center;">
    Version <?php echo AgaviConfig::get('org.icinga.version.release'); ?>
</h2>

<h3 style="text-align: center;">
    <?php echo $tm->_d(AgaviConfig::get('org.icinga.version.releasedate')); ?>
</h3>

<p style="text-align: center;" class="legal">
    <?php echo AgaviConfig::get('org.icinga.version.copyright'); ?><br /><br />
    Portions copyright by Nagios/Icinga community members - see the THANKS file for more information.
</p>

<p style="text-align: center;" class="legal">
    Icinga is licensed under the GNU General Public License and is provided
    AS IS   with NO WARRANTY OF ANY KIND, INCLUDING THE WARRANTY OF DESIGN,
    MERCHANTABILITY, AND FITNESS FOR A PARTICULAR PURPOSE.<br /><br />
    All other trademarks are the property of their respective owners.
</p>

<p style="width: 400px; margin: 0 auto;">
    <a id="icinga-image-bugreport" href="http://www.icinga.org/faq/how-to-report-a-bug/"></a>
    <a id="icinga-image-support" href="http://www.icinga.org/support/"></a>
    <br />
    <a id="icinga-image-wiki" href="http://wiki.icinga.org/"></a>
    <a id="icinga-image-translate" href="http://translate.icinga.org/"></a>
</p>
