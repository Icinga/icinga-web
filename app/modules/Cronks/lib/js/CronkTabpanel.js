Ext.ns('Cronk.util');

Cronk.util.Tabpanel = function(config) {

	this.stateEvents = ['add', 'remove', 'tabchange', 'titlechange'];
	
	if (!Ext.isArray(config.plugins)) {
		config.plugins = [
			new Cronk.util.CronkTabHelper(),
			
			new Ext.ux.TabScrollerMenu({
				maxText  : 15,
				pageSize : 5
			})
		];
	}
	
	Cronk.util.Tabpanel.superclass.constructor.call(this, config);	
}

Ext.extend(Cronk.util.Tabpanel, Ext.TabPanel, {
	URLTabData : false,
	
	minTabWidth: 125,
    tabWidth:135,
    
    enableTabScroll : true,
	resizeTabs      : true,
	minTabWidth     : 75,
	
	initComponent : function() {
		
		Cronk.util.Tabpanel.superclass.initComponent.call(this);
		
		// This is missed globally
		this.on('beforeadd', function(tabPanel, component, index) {
			if (!Ext.isDefined(component.tabTip) && Ext.isDefined(component.title)) {
				component.tabTip = component.title;
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
				cout[item.getId()] = Cronk.Registry.get(item.getId());
				
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
			active: ( (t) ? t.getId() : null )
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
				
				this.getActiveTab().doLayout();
			}
				
						
		}).defer(5, this);
				
		return true;
	},
	
	listeners: {
		tabchange: function(tab) {
			var aTab = tab.getActiveTab();	
			document.title = "Icinga - "+aTab.title;
		}
	}
});

Ext.reg('cronk-control-tabs', Cronk.util.Tabpanel);
