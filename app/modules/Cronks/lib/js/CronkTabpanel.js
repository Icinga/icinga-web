Ext.ns('Cronk.util');

Cronk.util.Tabpanel = function(config) {

    this.stateEvents = ['add', 'remove', 'tabchange', 'titlechange'];
    
    Cronk.util.Tabpanel.superclass.constructor.call(this, config);  
};

Ext.extend(Cronk.util.Tabpanel, Ext.ux.panel.DDTabPanel, {
    
    URLTabData : false,
    minTabWidth: 125,
    tabWidth:135,
    enableTabScroll : true,
    resizeTabs      : true,
    minTabWidth     : 75,
    tabOrder : [],
    customCronkCredential : false,
    
    initComponent : function() {
        
        this.plugins = [
            new Cronk.util.CronkTabHelper(),
            
            new Ext.ux.TabScrollerMenu({
                maxText  : 15,
                pageSize : 5
            })
        ];
        
        Cronk.util.Tabpanel.superclass.initComponent.call(this);
        
        // This is missed globally
        this.on('beforeadd', function(tabPanel, component, index) {
            if (!Ext.isDefined(component.tabTip) && Ext.isDefined(component.title)) {
                component.tabTip = component.title;
            }
        }, this);
        
        this.on('beforeadd', function(tabPanel, component, index) {
            component.on('removed', this.handleTabRemove, this, { single : true });
        }, this);
        
        this.on('tabchange', this.fillTabOrder, this);
        
    },

    fillTabOrder : function(tabs, changed) {
        this.tabOrder.push(changed.getId());
        
        // Sort of GV
        var lastItem = null;
        Ext.each(this.tabOrder, function(item, number) {
            
            // Item does not exist anymore
            if (this.items.get(item) === false) {
                this.tabOrder.splice(number, 1);
                return false;
            }
            
            // Doubled entry
            if (lastItem === item) {
                this.tabOrder.splice(number-1, 1);
            }
            
            lastItem = item;
        }, this)
    },
    
    handleTabRemove : function(removec, ownerCt) {
        var index = 0;
        while (index >= 0) {
            index = this.tabOrder.indexOf(removec.getId());
            if (index >= 0) {
                this.tabOrder.splice(index,1);
            }
        }
        
        var sid = this.tabOrder.pop();
        if (sid == this.getActiveTab().getId()) {
            sid = this.tabOrder.pop();
        } else {
            return;
        }
        this.items.each(function(item, index, len) {
            if (item.getId() === sid) {
                this.setActiveTab(item);
                return false;
            }
        }, this);
    },
    
    setURLTab : function(params) {
        this.URLTabData = params;
    },
    
    getTabIndex: function(tab) {
        var i = -1;
        this.items.each(function(item, index, a) {
            i++;
            if (item == tab) {
                return false;
            }
        });
        return i;
    },
    
    getActiveTabIndex: function() {
        return this.getTabIndex(this.getActiveTab());
    },
    
    getState: function() {
        
        var cout = {};
    
        this.items.each(function(item, index, l) {
            if (Cronk.Registry.get(item.getId())) {
                
                // Copy reference
                cout[item.getId()] = Ext.apply({}, Cronk.Registry.get(item.getId()));
                
                // Local space is not for serializing
                if (Ext.isDefined(cout[item.getId()].local)) {
                    delete(cout[item.getId()].local);
                }
                
                if (Ext.isDefined(item.iconCls)) {
                    cout[item.getId()].iconCls = item.iconCls;
                }
            }
        });
        // AppKit.log("STATE", cout);
        var t = this.getActiveTab();
        return {
            cronks: cout,
            items: this.items.getCount(),
            active: ( (t) ? t.getId() : null ),
            tabOrder : this.tabOrder
        }
    },
    
    applyState: function(state) {
        (function() {
            if (state.cronks) {
                // Adding all cronks
                Ext.iterate(state.cronks, function(index, item, o) {
                    this.add(item);
                }, this);
                
                if(this.URLTabData) {
                    
                    var tabPlugin = this.plugins;   
                    if(Ext.isArray(this.plugins)) {
                        tabPlugin = null;
                        for(var i=0;i<this.plugins.length;i++)
                            if(this.plugins[i].createURLCronk) {
                                tabPlugin = this.plugins[i];
                                break;
                            }
                    }
                    if(tabPlugin) {
                        var index = this.add(tabPlugin.createURLCronk(this.URLTabData));
                        this.setActiveTab(index);   
                    }
                }               
                else {
                    this.setActiveTab(state.active || 0);
                }
                
                if (Ext.isArray(state.tabOrder)) {
                    this.tabOrder = state.tabOrder;
                    
                    // AppKit.log("Got state: ", state.tabOrder);
                }
                
                this.getActiveTab().doLayout();
            }
                
                        
        }).defer(5, this);
                
        return true;
    },
    
    listeners: {
        tabchange: function(tab) {
            var aTab = tab.getActiveTab();  
            document.title = String.format('{0} - {1}', AppKit.util.Config.get('core.app_name'), aTab.title);
        }
    }
});

Ext.reg('cronk-control-tabs', Cronk.util.Tabpanel);
