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
// Start with a simple scriptblock

// This is the init method called when the cronk environment is ready
Cronk.util.initEnvironment("<?php echo $rd->getParameter('parentid'); ?>", function() {
    
    var CE = this;
    
    var p = this.add({
    
        xtype: 'panel',
        
        items: [{
            layout: 'absolute',
            width: 200,
            height: 50,
            style: 'margin: 20px auto',
            border: false,
            items: [{
                xtype: 'button',
                text: _('Press me'),
                width: '100%',
                height: 50,
                iconCls: 'icinga-icon-bell',
                handler: function(btn, e) {
                    Ext.MessageBox.alert(_('Say what?'), CE.getParameter('say_what'));
                }
            }]
        
        
        }]
    });
    
    this.doLayout();
});
</script>
