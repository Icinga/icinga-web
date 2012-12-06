Ext.ns("Icinga.Cronks.util").FilterEditorWindow = function(grid,filters) {
    "use strict";
    var tree = new Icinga.Cronks.util.FilterEditor({
        autoDestroy: false,
        grid: grid,
        filterCfg:filters
    });

    this.state = new Icinga.Cronks.util.FilterState({
        autoDestroy: false,
        grid: grid,
        tree: tree
    });

    this.updateFromJsonString = function(json) {
        if(Ext.isString(json))
            this.filter = Ext.decode(json);
        else
            this.filter = json;
        if(this.state)
            this.state.update(this.filter);
        grid.getStore().load();
    }

    this.show = function() {
        tree = new Icinga.Cronks.util.FilterEditor({
            autoDestroy: false,
            grid: grid,
            filterCfg:filters
        });

        this.state = new Icinga.Cronks.util.FilterState({
            autoDestroy: false,
            grid: grid,
            tree: tree
        });
        var filterPanel = this.getFilterPanel(tree);
        var cronkPanel = Ext.getCmp('west-frame');
        registerEvents(filterPanel,cronkPanel);
        
        cronkPanel.add(filterPanel);
        cronkPanel.getLayout().setActiveItem(1);
    };
    
    
    this.getFilterPanel = function(tree) {
        if(this.filter)
            this.state.update(this.filter);
        return new Ext.Panel({
            tbar: [{
                xtype: 'button',
                text: _('Save'),
                iconCls: 'icinga-icon-accept',
                handler: function() {
                    this.state.save();


                },
                scope: this
            },'->',{
                xtype: 'button',
                text: _('Back to cronks'),
                iconCls: 'icinga-icon-arrow-left',
                handler: function() {
                    Ext.getCmp('west-frame').resetCronkView()
                }
            }],
            layout:'vbox',
            items:	[{
                layout:'fit',
                width: 300,
                flex:2,
                items: tree
            },{
                layout:'fit',
                width: 300,
                flex: 2,
                title:_('Available Elements'),
                items: tree.getAvailableElementsList(false)
            }]
        });
    }


    
    var registerEvents = function(filterPanel,cronkPanel) {
        // resizing must be done manually here
        var resizePanelHandler = function(resizedCmp) {
            var width = resizedCmp.getWidth();
            filterPanel.setWidth(width);
            filterPanel.items.each(function(cmp_child) {
                cmp_child.width = width;
                cmp_child.doLayout();
            })
            filterPanel.doLayout();
        };
        cronkPanel.on("resize",resizePanelHandler)
        
        // register cleanup functions
        grid.on({
            "hide" : cronkPanel.resetCronkView,
            "close" : cronkPanel.resetCronkView,
            "destroy" : cronkPanel.resetCronkView
        });
        grid.findParentByType('tabpanel').on("tabchange",cronkPanel.resetCronkView);
        cronkPanel.on("reset", function() {
            cronkPanel.removeListener("resize",resizePanelHandler);
        });    
    }
    
    
};