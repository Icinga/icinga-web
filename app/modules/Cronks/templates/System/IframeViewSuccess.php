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

 $url = $t['url']; 

?>
<?php if ($url) { ?>
<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
    var newid = this.cmpid; 
    var domid = newid + '-dom';
    var stateuid = this.stateuid;
    
    // Create a new panel with a modified body element
    var config = {
        id: newid,
        listeners: {
            
            beforerender: function(ct) {
                this.bodyCfg = {
                    tag: 'iframe',
                    src: '<?php echo $url ?>',
                    id: domid
                };
                
                Ext.EventManager.on(window, 'unload', function() {
                    this.saveState();
                }, this);
                
                return true;
            }
            
        }
    };
    
    if (stateuid) {
        Ext.apply(config, {
            stateId: stateuid,
            stateEvents: ['unload'],
            stateful: true,
            
            getState: function() {
                var url = null;
                
                var e = this.body.dom;
                if (e.contentDocument) {
                    url = e.contentWindow.location.href;
                }
                
                return {
                    url: url
                };
            },
            
            applyState: function(state) {
                return true;
            }
        });
    }
    
    
    // Insert he element (no add, because reload results in multiple items)
    this.insert(0, new Ext.Panel(config));
    
    // Notify about changes
    this.doLayout();
    
});
</script>
<?php } ?>
