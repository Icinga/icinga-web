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

    $version_string = AgaviConfig::get('org.icinga.version.release');;
    $version_date   = AgaviConfig::get('org.icinga.version.releasedate');
    $copyright      = AgaviConfig::get('org.icinga.version.copyright');
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

<div class="icinga-cronk-welcome-frame">
    <div style="padding: 5px 200px 5px 20px;">
        <div style="width: 200px; padding: 5px; margin: 0 0 10px 0">
            <a id="icinga-image-home" href="http://www.icinga.org/"></a>
        </div>
        
        <h1>Welcome to <?php echo AgaviConfig::get('core.app_name'); ?> (<?php echo $version_string; ?>)</h1>
        
        <p>Feel free to poke around and don't forget to visit the project homepage  to post bug advisories or feature requests.</p>
        
        <p>What are Cronks? Simply put, they are widgets for the Icinga web front end - with a cooler name.</p>
        
        <p>Have fun!</p>
        
        <p><?php echo $tm->_d($version_date, 'date-medium'); ?> - <a href="http://www.icinga.org/"><?php echo $copyright; ?></a></p>
    </div>
</div>

<p style="width: 800px; margin: 0 auto;">
    <a id="icinga-image-bugreport" href="http://www.icinga.org/faq/how-to-report-a-bug/"></a>
    <a id="icinga-image-support" href="http://www.icinga.org/support/"></a>
    <a id="icinga-image-wiki" href="http://wiki.icinga.org/"></a>
    <a id="icinga-image-translate" href="http://translate.icinga.org/"></a>
</p>
