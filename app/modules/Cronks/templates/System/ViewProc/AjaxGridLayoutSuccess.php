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
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {

    var CE = this;
    
    var CreateGridProcessor = function (meta) { 
        
        // Add base url, so static js files can build routes, too
        meta.baseURL = AppKit.c.path;
        
        var MetaGrid = new Cronk.grid.MetaGridCreator(meta);
        
        MetaGrid.setStateUid(CE.stateuid);
        
        if (Ext.isDefined(CE.state)) {
            MetaGrid.setInitialState(CE.state);
        }
        
        MetaGrid.setStoreUrl("<?php echo $ro->gen('modules.cronks.viewProc.json', array('template' => $rd->getParameter('template'))); ?>");
        MetaGrid.setParameters(<?php echo json_encode($rd->getParameters()); ?>);
        MetaGrid.setParameters({storeDisableAutoload: true});
        
        var grid = MetaGrid.createGrid();
        CE.setStatefulObject(grid);
        
        // Add the window to a toolbar button
        grid.on('render', function(g) {
            
            if (meta.template.option.mode == 'minimal') {
                this.topToolbar.hide();
            }
            else {
            
                var bFilters = false;
                
                Ext.iterate(MetaGrid.getFilterCfg(), function() {
                    if (bFilters == false) bFilters = true;
                });
            
                if (bFilters == true) {
                    function initGrid() {
                        // wait till ready
                        if(!Cronk.util.GridFilterWindow) {
                            initGrid.defer(200,this);
                            return false;
                        }
                        var fw = new Cronk.util.GridFilterWindow();

                        fw.setGrid(grid);
                        fw.setFilterCfg( MetaGrid.getFilterCfg() );

                        // Distribute destroy events
                        grid.on('destroy', function() {
                            fw.destroyHandler();
                        });

                        grid.on('refresh', function() {
                            fw.destroyHandler();
                        });

                        this.topToolbar.add([
                            '-', {
                                text: _("Filter"),
                                iconCls: 'icinga-icon-pencil',
                            //  cls: this.filter_params.length ? 'activeFilter' : null,
                                id: grid.id+"_filterBtn",
                                menu: {
                                    items: [{
                                        text: _("Modify"),
                                        iconCls: 'icinga-icon-application-form',                                        
                                        handler: fw.startHandler,
                                        scope: this
                                    },{
                                        text: _("Remove"),
                                        iconCls: 'icinga-icon-cancel',
                                        handler: function(b, e) {
                                            fw.removeFilters();
                                        },
                                        scope: this
                                    }]
                                }
                            }
                        ]);
                    }
                    initGrid.call(this);
                }
                
                // If the templates uses commands
                var Options = MetaGrid.getOptions();
                var bCommands = (Options['commands'] && Options['commands']['enabled'] == true) ? true : false;
                
                var bCommandRo = '<?php echo $us->getNsmUser()->hasTarget("IcingaCommandRo"); ?>';
            
                
                if (bCommands == true && !bCommandRo == 1) {
                    
                    var tbEntry = this.topToolbar.add({
                        text: '<?php echo $tm->_("Commands"); ?>',
                        iconCls: 'icinga-icon-server-lightning',
                        menu: {
                            items: []
                        }
                    });
                    // An instance to work with
                    var cHandler = new IcingaCommandHandler(meta);
                    
                    // The entry point to start
                    cHandler.setToolbarEntry(tbEntry);
                    
                    // We need some selection from a grid panel
                    cHandler.setGrid(grid);
                    
                    // Where we can get some info
                    cHandler.setInfoUrl('<?php echo urldecode($ro->gen("modules.cronks.commandProc.metaInfo", array("command" => "{0}"))); ?>');
                    cHandler.setSendUrl('<?php echo urldecode($ro->gen("modules.cronks.commandProc.send", array("command" => "{0}"))); ?>');
                    
                    // We need something to click on
                    cHandler.enhanceToolbar();
                }

                var combo = this.getConnectionComboBox()
                
                this.topToolbar.add(["->",combo]);
            }
        });
        
        Ext.onReady(function() {
            // Check if the store is loaded by whatever ...
            // If no load with defautl params!
            grid.on('render', function(g) {
                if (this.storeIsLoaded() == false) {
                    this.initStore();
                }
            }, MetaGrid);
            
            // Add to parent component
            CE.add(grid);
            CE.doLayout();
            
        });
    }
    
    // First loading the meta info to configure the grid
    var oContainer = function() {
        
        var s = AppKit.util.getStore('viewproc_templates');
        
        var template = "<?php echo $rd->getParameter('template'); ?>";
        var initGrid = function() {
            var meta = s.get(template);
            CreateGridProcessor(meta);
        }
        
        if (s.containsKey(template)) {
            initGrid();
        }
        else {
        
            Ext.Ajax.request({
                   url: "<?php echo $ro->gen('modules.cronks.viewProc.json.metaInfo', array('template' => $rd->getParameter('template'))); ?>",
                   
                   success: function(response, opts) {

                        s.add(template, Ext.decode(response.responseText));
                        initGrid();
                   },
                   
                   failure: function(response, opts) {
                        AppKit.notifyMessage(
                            "Ext.Ajax.request: request failed!",
                            String.format("{0} ({1})", response.statusText, response.status)
                        );
                   },
                   scope : oContainer
            });
        }
    }
    
    oContainer.call(this);
    
});
</script>
