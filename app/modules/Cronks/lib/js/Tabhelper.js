Ext.ns('Cronk.util');

Cronk.util.CronkTabHelper = function() {
    var pub = {};
    var tp = null;
    var ctxItem = null;
    var contextmenu = null;
    var keyMap = null;
};
    
Cronk.util.CronkTabHelper = Ext.extend(Object, { 
        
    last_tab: null,
    
    sliding_tab: null,
    
    customCronkCredential: false,
    
    init: function(c) {
        tp = c;
        
        // create  our drop target for the cronks
        tp.on('afterrender', this.createDropTarget, this, { single: true });
        
        // Deny removing all tabs
        tp.on('beforeremove', this.itemRemoveActiveHandler, this);
        
        // Enable title change bubble to safe the state 
        // of the component
        tp.on('add', this.itemModifier, this);
        
        // Saving the last tab for a history
        tp.on('beforetabchange', this.itemActivate, this);
        
        // Adding the contextmenu
        tp.on('contextmenu', this.contextMenu, this);
        
        // Check if we've no tabs and say hello if the frame is empty
        tp.on('afterrender', this.sayHello, this, { single: true, delay: 5 });
        
        this.customCronkCredential = tp.customCronkCredential || false;
        
        // Prevent refresh
        this.applyGlobalKeyMap();
    },
    
    createWelcomeCronk : function() {
        return Cronk.factory({
            title: _("Welcome"),
            crname: ( AppKit.getPrefVal('org.icinga.cronk.default') || 'portalHello' ),
            closable: true,
            iconCls: 'icinga-cronk-icon-start'
        });
    },
    
    sayHello : function() {
        if (tp.items.getCount() < 1) {
            tp.setActiveTab(tp.add(this.createWelcomeCronk()));
        }
    },
    
    applyGlobalKeyMap : function() {
        keyMap = new Ext.KeyMap(Ext.getDoc(), {
            key: Ext.EventObject.F5,
            fn: function(keycode, e) {
                
                if (Ext.isIE) {
                    e.browserEvent.keyCode = Ext.EventObject.ESC;
                }
                
                var tab = tp.getActiveTab();
                if (tab) {
                    e.stopEvent();
                    tab.getUpdater().refresh();
                }
            },
            scope: this
        });
    },
    
    createDropTarget : function() {
        var t = tp;
        new Ext.dd.DropTarget(t.header, {
                        ddGroup: 'cronk',
                        
                        notifyDrop: function(dd, e, data){
                            // add them to the tabs
            
                            var a = tp.add({
                                iconCls: Cronk.getIconClass(data.dragData['image_id']),
                                title: data.dragData['name'],
                                crname: data.dragData.cronkid,
                                closable: true,
                        
                                params: data.dragData.parameter,
                                xtype: 'cronk',
                                params: Ext.apply({}, data.dragData['ae:parameter'], { 
                                    module: data.dragData.module, 
                                    action: data.dragData.action 
                                })
                            });
                            
                            // Set active
                            tp.setActiveTab(a);
                        }
        });
    },
    
    itemRemoveActiveHandler : function (tabPanel, ri) {
        
        if (tabPanel.items.getCount() <= 1) {
            AppKit.notifyMessage(_('Sorry'), _('Could not remove the last tab!'));
            return false;
        }
        else {
            
            if (this.last_last_tab && ri.id !== last) {
                tabPanel.setActiveTab( last );
            }
            else {
                tabPanel.setActiveTab( (tabPanel.items.getCount() - 1) );
            }
        }
        
        return true;
    },
    
    itemModifier : function (co, item, index) {
        item.enableBubble('titlechange');
        return true;
    },
    
    itemActivate : function (p, ntab, ctab) {
        if (ctab && "id" in ctab) {
            this.last_tab = ctab.id;
        }
    },
    isFullscreen: false,
    setFullscreen: function(val) {
        var func = "hide";
        if(val == true) {
            func = "hide";
            if(Ext.getCmp(tp.id+'-expand')) {
                Ext.getCmp(tp.id+'-expand').hide();
                Ext.getCmp(tp.id+'-reset').show();
            }
            this.isFullscreen = true;
        } else {
            func = "show";
            if(Ext.getCmp(tp.id+'-expand')) {
                Ext.getCmp(tp.id+'-expand').show();
                Ext.getCmp(tp.id+'-reset').hide();
            }
            this.isFullscreen = false;
        }
        Ext.getCmp('north-frame')[func]();  
        Ext.getCmp('west-frame')[func]();
        Ext.getCmp('viewport-north')[func]();
        Ext.getCmp('view-container').doLayout();
        AppKit.util.Layout.doLayout();
    
    },  
    
    contextMenu : function (myp, tab, e) {
        if (!this.contextmenu) {
            this.contextmenu = new Ext.menu.Menu({
                items: [{
                    text: _("Close"),
                    id: tp.id + '-close',
                    iconCls: 'icinga-icon-cross',
                    handler: function() { tp.remove(ctxItem); }
                }, {
                    text: _("Close others"),
                    id: tp.id + '-close-others',
                    iconCls : 'icinga-icon-applications-stack',
                    handler: function() {
                        tp.items.each(function(item){
                            if(item.closable && item != ctxItem){
                                // IE stops there because Ext.fly
                                try {
                                    tp.remove(item, true);
                                }
                                catch(e) {}
                            }
                        });
                    }
                },'-', {
                    text: _("Expand"),
                    id: tp.id +'-expand',
                    iconCls: 'icinga-icon-arrow-out',
                    
                    handler: function() {
                        this.setFullscreen(true);
                        this.contextmenu.hide();
                        
                    },
                    scope: this
                },{
                    text: _("Reset view"),
                    id: tp.id +'-reset',
                    iconCls: 'icinga-icon-arrow-in', 
                    hidden: true,   
                    handler: function() {
                        this.setFullscreen(false);  
                    },
                    scope: this

                },'-',{
                    text: _("Rename"),
                    id: tp.id + '-rename',
                    iconCls: 'icinga-icon-table-edit',
                    handler: this.renameTab,
                    scope: this
                },{
                    text: _("Reload cronk"),
                    tooltip: _("Reload the cronk (not the content)"), 
                    iconCls: 'icinga-icon-arrow-refresh',
                    handler: function() {
                        ctxItem.getUpdater().refresh();
                    }
                }, {
                    xtype: 'menuseparator'
                }, {
                    text: _("Save Cronk"),
                    tooltip: _("Save this view as new cronk"),
                    iconCls: 'icinga-icon-star-plus',
                    id: tp.id + '-save-custom',
                    handler: function() {
                        var cb = Cronk.util.CronkBuilder.getInstance();
                        cb.show(this.getEl());
                        cb.setCurrentCronkId(ctxItem.getId());
                    }
                }]
            });
        }
        
        ctxItem = tab;
        this.contextmenu.items.get(myp.id + '-save-custom').setDisabled(!this.customCronkCredential);
        this.contextmenu.items.get(myp.id + '-close').setDisabled(!tab.closable);
        this.contextmenu.items.get(myp.id + '-close-others').setDisabled(!tab.closable);
        this.contextmenu.items.get(myp.id + '-rename').setDisabled(!tab.closable);
        this.contextmenu.showAt(e.getPoint());
    },
    
    renameTab : function() {
        var msg = Ext.Msg.prompt(_("Enter title"), _("Change title for this tab"), function(btn, text) {
            
            if (btn == 'ok' && text) {
                ctxItem.setTitle(text);
            }
                    
        }, this, false, ctxItem.title);
        
        // Move the msgbox to our context menu
        msg.getDialog().alignTo(this.contextmenu.el, 'tr-tr');
    },
    
    createURLCronk: function(data) {        
        
        var urlCronk =  Cronk.factory({
            title: data.title,
            crname: data.crname,
            closable: true,
            iconCls: data.iconCls || 'icinga-cronk-icon-go-out',
            
            params: {
                'template': data.template
            }
        });
        
        urlCronk.on("add",function(p, c, i) {
            if(!c.store)
                return null; 
            
            Ext.apply(c.store.baseParams,data.cr_base);
            
            c.store.originParams= {};
            Ext.apply(c.store.originParams,data.cr_base);
            
            c.store.groupDir = data.groupDir;
            c.store.groupField = data.groupField;
            
            c.store.sort(data.groupField,data.groupDir);
            
            if(c.parentCmp.sort_array)  {
                c.parentCmp.sort_array[0]['direction'] = data.groupDir;
                c.parentCmp.sort_array[0]['field'] = data.groupField;
            }
            
            c.store.load();
            
        });
        
        return urlCronk;
    }
    
});
