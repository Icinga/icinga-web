Ext.ns("lconf.Admin");

(function() {
var __instance;

var getCompatibilityFieldMapping = function(legacyField, newField) {
    return {
        name: legacyField,
        convert: function(v,record) {
            if(typeof record[legacyField] !== "undefined") {
                return record[legacyField];
            } else {
                return record[newField];
            }
        }
    }
}

lconf.Admin.getPrincipalEditor = function() {
	if(<?php echo ($us->hasCredential('lconf.admin') ? 'false' : 'true') ?>)
		return null;
	if(__instance)
		return __instance;



	/**
	 * Excludes records selected in store.sourceStore from this store
	 * 
	 * @param {Ext.data.Store} store
	 */
	this.excludeSelectedRecords = function(store) {
		var primary = store.idProperty;
		// sourcestore is an id
		if(Ext.isString(store.sourceStore))
			store.sourceStore = Ext.StoreMgr.lookup(store.sourceStore);
        var toRemove = [];
		store.each(function(record) {
			var index = store.sourceStore.find(primary,record.get(primary));
			if(index != -1)
				if(!store.isStaticSource)
					store.sourceStore.removeAt(index);
				else
					toRemove.push(record)
		});
		Ext.iterate(toRemove,function(i) {
			store.remove(i);
		});
	}

	this.populate = function(connection_id) {
		Ext.iterate(this.storeCollection,function(s) {
			s.setBaseParam('connection_id',connection_id);
			s.load();
		});
	}

	/**
	 * There are always to pairs of stores: Available users/groups and selected users/groups
	 */
	this.groupStore = new Ext.data.JsonStore({
		autoDestroy: true,
		isStaticSource: true,
		storeId: 'groupListStore',
		sourceStore: 'groupListSelectedStore',
		idProperty: 'role_id',
		remoteSort: true,
		root:'roles',
		url: '<?php echo $ro->gen("modules.appkit.data.groups")?>',
		fields: [
			getCompatibilityFieldMapping('role_id','id'),
			getCompatibilityFieldMapping('role_name','name')
		],
		listeners: {
			// function to filter out already selected values from the available view
			load:this.excludeSelectedRecords,
			scope:this
		}
	})
	
	this.selectedGroupsStore = new Ext.data.JsonStore({
		autoDestroy:false,
		autoSave: false,
		storeId: 'groupListSelectedStore',
		idProperty: 'role_id',
		sourceStore: this.groupStore,
		root:'groups',
		url: '<?php echo $ro->gen("modules.lconf.data.principals") ?>',
		baseParams: {
			target: 'groups'
		},
		fields: [
			getCompatibilityFieldMapping('role_id','id'),
			getCompatibilityFieldMapping('role_name','name')
		],
		writer: new Ext.data.JsonWriter({
			encode:true
		}),
		proxy: new Ext.data.HttpProxy({
			url:'<?php echo $ro->gen("modules.lconf.data.principals") ?>'
		}),
		listeners: {
			// function to filter out already selected values from the available view
			load:this.excludeSelectedRecords,
			save: function(s) {s.load()},
			scope:this
		}
	})
	
	
	this.userStore = new Ext.data.JsonStore({
		autoDestroy: true,
		storeId: 'userListStore',
		sourceStore: 'userListSelectedStore',
		totalProperty: 'totalCount',
		isStaticSource: true,
		root: 'users',
		idProperty: 'user_id',
		url: '<?php echo $ro->gen("modules.appkit.data.users")?>?hideDisabled=false',
		remoteSort: true,
		
		fields: [
			getCompatibilityFieldMapping('user_id','id'),
            getCompatibilityFieldMapping('user_name','name')
		],
		listeners: {
			// function to filter out already selected values from the available view
			load:this.excludeSelectedRecords,
			scope:this
		}
	})
	
	
	this.selectedUsersStore = new Ext.data.JsonStore({
		autoDestroy:false,
		autoSave: false,
		storeId: 'userListSelectedStore',
		sourceStore: this.userStore,
		idProperty: 'user_id',
		root:'users',
		url: '<?php echo $ro->gen("modules.lconf.data.principals") ?>',
		baseParams: {
			target: 'users'	
		},
		fields: [
			getCompatibilityFieldMapping('user_id','id'),
			getCompatibilityFieldMapping('user_name','name')
		],
		writer: new Ext.data.JsonWriter({
			encode:true
		}),
		proxy: new Ext.data.HttpProxy({
			url:'<?php echo $ro->gen("modules.lconf.data.principals") ?>'
		}),
		listeners: {
			// function to filter out already selected values from the available view
			load: this.excludeSelectedRecords,
			save: function(s) {s.load()}
		}
	})
	
	
	this.getPrincipalTabbar = function() {
		var usersTab = lconf.Admin.itemGranter({
			targetStore: this.selectedUsersStore,
			store: this.userStore,	
			iconCls: 'icinga-icon-user',
			title: _('Users'),
			id: "userPanel",
			columns:[
				{header:_('Id'),name:'user_id'},
				{header:_('User'),name:'user_name'}
			],
			targetColumns:[
				{header:_('Id'),name:'user_id',dataIndex:'user_id'},
				{header:_('User'),name:'user_name',dataIndex:'user_name'}
			]
			
		})
		var groupTab = lconf.Admin.itemGranter({
			targetStore: this.selectedGroupsStore,
			store: this.groupStore,	
			title: _('Groups'),
			iconCls: 'icinga-icon-users',
			id: "groupPanel",
			columns: [
				{header:_('Id'),name:'role_id'},
				{header:_('Group'),name:'role_name'}
			],
			targetColumns:[
				{header:_('Id'),name:'role_id',dataIndex:'role_id'},
				{header:_('Group'),name:'role_name',dataIndex:'role_name'}
			]
		})

		return new Ext.TabPanel({
			activeTab: 0,
			items: [
				usersTab,
				groupTab
			]
		})
		
	}
	var id = Ext.id;
	
	return new Ext.Panel({
		layout:'fit',
		autoScroll:true,	
		id: "wnd_"+id,
		items: this.getPrincipalTabbar(),
		title: '<b>'+('Access')+'</b>',
		iconCls: 'icinga-icon-user',
		buttons: [{
			text:_('Save changes'),
			iconCls: 'icinga-icon-disk',
			handler: function(btn) {
				this.selectedUsersStore.save();
				this.selectedGroupsStore.save();
				
				
			},
			scope: this
		}]
	})
	
}

lconf.GridDropZone = function(grid, config) {
	this.grid = grid;
	lconf.GridDropZone.superclass.constructor.call(this, grid.view.scroller.dom, config);
};

Ext.extend(lconf.GridDropZone, Ext.dd.DropZone, {
	onContainerOver:function(dd, e, data) {
		return (!this.grid.disabled && (dd.grid !== this.grid)) 
					? this.dropAllowed : this.dropNotAllowed;
	},
	
	onContainerDrop:function(dd, e, data) {
		if(!this.grid.disabled && dd.grid !== this.grid) {
			// Move the records between the stores on drop
		
			Ext.each(data.selections,function(r) {
				var rec = r.copy();
				Ext.data.Record.id(rec);
				this.grid.store.add(rec);
				dd.grid.store.remove(r);
			},this)
			
			return true;
		} 
		return false;
	},
	containerScroll:true
});

lconf.Admin.itemGranter = function(config) {
	this._interface = null;
	Ext.apply(this,config);
	
	this.notifySelected = function(where) {
		if(where == "available")
			target = this.gridSelected;
		else 
			target = this.gridAvailable;

		if(target.getSelectionModel().hasSelection()) {
			target.getSelectionModel().clearSelections();
		}
	}
	
	this.gridAvailable =  new Ext.grid.GridPanel({
		title: _("Available"),	
		
		store: this.store,
		columnWidth: .5,
		colModel: new Ext.grid.ColumnModel({
			defaults: {
				width:100,
				sortable: true
			},
			columns: this.columns
		}),
		
		bbar: new Ext.PagingToolbar({
			pageSize: 25,
			store: this.store,
			displayInfo: true,
			displayMsg: _('Showing ')+' {0} - {1} '+_('of')+' {2}',
			emptyMsg: _('Nothing to display')
		}),	
		layout: 'fit',
		enableDragDrop: true,		
		sm: new Ext.grid.RowSelectionModel({
			singleSelect:false
		}),
		listeners: {
			render: function(grid) {
				this.dz = new lconf.GridDropZone(grid,{ddGroup:grid.ddGroup || 'GridDD'});
				grid.getStore().load();
			}
			
		}
	});	
	
	
	this.gridSelected = new Ext.grid.GridPanel({
		title: _("Selected"),
		store: this.targetStore,
		colModel: new Ext.grid.ColumnModel({
			defaults: {
				width:100,
				sortable: true
			},
			columns: this.targetColumns
		}),
		columnWidth: .5,
		layout: 'fit',
		enableDragDrop: true,
		sm: new Ext.grid.RowSelectionModel({
			singleSelect:false	
		}),
		listeners: {
			render: function(grid) {
				this.dz = new lconf.GridDropZone(grid,{ddGroup:grid.ddGroup || 'GridDD'});
			}	
		}
	});	

	this.addSelectedItems = function(from,to) {
		// check selection
		if(!from.getSelectionModel().hasSelection())
			Ext.MessageBox.alert(_("Error"),_("You haven't selected anything."));
		Ext.each(from.getSelectionModel().getSelections(),function(r) {
			var rec = r.copy();
			Ext.data.Record.id(rec);
			to.getStore().add(rec);
			from.getStore().remove(r);
			});
	}

	this.buildInterface = function() {
		var available = this.gridAvailable;
		var selected =  this.gridSelected;
		this._interface = new Ext.Panel({
			layout:'column',	
			title:this.title,
			defaults: {
				cellCls: 'middleAlign'
			},
			items: [	
				available,
				{
					width:50,
					style: 'margin-top:50%',
					cls: 'middleAlign',
					items:[{
						xtype:'button',
						text: '<<',
						width:50,
						handler: function() {this.addSelectedItems(selected,available)},
						scope: this
					},{
						xtype:'button',
						text: '>>',
						width:50,
						handler: function() {this.addSelectedItems(available,selected)},
						scope: this
					}]	
					
				},
				selected
			],
			listeners: {
				resize: function(el) {
					el.suspendEvents();
					Ext.iterate(el.items.items,function(item) {
						item.setHeight(el.getHeight());
					});
					el.resumeEvents();
				}
			}
		})		

	}

	this.storeCollection = [
		this.selectedGroupsStore,
		this.selectedUsersStore
	]
	
	this.selectedUsersStore.on("load",function() {this.sourceStore.load();},this.selectedUsersStore);	
	this.selectedGroupsStore.on("load",function() {this.sourceStore.load();},this.selectedGroupsStore);
	

	this.buildInterface();
	this._interface.cmp = this;
	__instance = this._interface;
	return this._interface;
}
})();
