<script type='text/javascript'>
Ext.ns("AppKit.groups")
Ext.onReady(function() {
	AppKit.groups.structureView = new (Ext.extend(Ext.tree.TreePanel,{
		constructor: function(cfg) {
			if(!cfg) 
				cfg = {}
			cfg.root = new Ext.tree.TreeNode({
				hidden:false,
				editable:false,
				text:'Root',
				expanded:true
			});

			Ext.tree.TreePanel.superclass.constructor.call(this,cfg)
			this.getSelectionModel().on("selectionchange", function(model,node){
				if(!node)
					return true;
				record = [node.record];
				AppKit.groups.grid.getSelectionModel().selectRecords(record);
			})
		},
		enableDD: true,
		autoHeight: true,
		inserted : {},
		title: _('Group inheritance'),
		insertRoles: function() {
			this.inserted = {};
			this.getRootNode().removeAll();
			var noInsert = false;
			while(!noInsert) {
				noInsert = true;
				AppKit.groups.groupList.each(function(record) {
					var name = record.get("role_name");
					var id =  record.get("role_id");
					var parent = record.get("role_parent")
					if(!this.inserted[id] && (!parent || (parent && this.inserted[parent]))) {
						var node = new Ext.tree.TreeNode({text: name, iconCls: 'icinga-icon-group'});
						this.inserted[id] = node;
						node.record = record;
						node.recordId = id;
						noInsert = false;
						if(!parent)
							this.getRootNode().appendChild(node);
						else 
							this.inserted[parent].appendChild(node);
					}				
				},this)
			}
			this.doLayout();
			return true;
		},
		listeners: {
			movenode: function(tree,node,oldParent,newParent,index) {
				if(!node.record)
					return false;
				var parentId = newParent.record ? newParent.record.get("role_id") : -1;
				node.record.set("role_parent",parentId);
				var groupId = node.record.get("role_id");
				var params = {};
				Ext.apply(params,node.record.data);
				params["ignorePrincipals"] = true;
				Ext.Ajax.request({
					url: '<?php echo $ro->gen("modules.appkit.admin.groups.alter")?>'+groupId,
					params: params,
					success: function() {
						AppKit.groups.groupList.reload();
					},
					scope:this
			 	});
			 	
			},
			scope:this
		}
	}))();
	
	AppKit.groups.groupList = new Ext.data.JsonStore({
		autoDestroy: true,
		storeId: 'groupListStore',
		idProperty: 'role_id',
		baseParams: {
			hideDisabled: false
		},
		totalProperty: 'totalCount',
		root: 'roles',
		autoLoad:true,
		remoteSort: true,
		url: '<?php echo $ro->gen("modules.appkit.data.groups")?>',
		fields: [
			{name: 'role_id', type:'int'},
			'role_name',
			'role_description',
			{name: 'role_disabled', type:'boolean'},
			{name: 'role_disabled_icon',type:'boolean' , mapping:'role_disabled', convert: function(v) {
				return '<div style="width:16px;height:16px;margin-left:25px" class="'+(v==1? 'icinga-icon-cancel' : 'icinga-icon-accept')+'"></div>';
			}},
			{name: 'role_created'},
			{name: 'role_modified'},
			{name: 'role_parent'}
		],
		listeners: {
			load: AppKit.groups.structureView.insertRoles.createDelegate(AppKit.groups.structureView),
			scope: this
		}
	})
	
	var wnd_groupEditPanel = new (Ext.extend(Ext.Window,{
		width:600,
		height: 600,
		layout:'fit',
		title: _('Edit group'),
		closeAction: 'hide',
		stateful:false,
		y:0,
		modal:true,
		id: 'wnd_groupEditPanel',
		listeners: {
			close: AppKit.groups.groupList.reload.createDelegate(AppKit.groups.groupList),
			hide: AppKit.groups.groupList.reload.createDelegate(AppKit.groups.groupList)
		},
		editGroup: function(id,_new) {
			if(!_new) {
				this.setTitle(_('Edit group'));
			}
			if(!this.uiFormLoaded) {
				if(!Ext.isDefined(id))  {
					Ext.Msg.alert(_("Error"),_("Unknown id"));
					return false;
				}
				if(!AppKit.groupEditor) {
					this.getUpdater().update({
						url : String.format('<?php echo $ro->gen("modules.appkit.admin.groups.edit")?>{0}/{1}',id,'wnd_groupEditPanel'),
						scripts: true,
						callback: function(el,success,response,options) {
							AppKit.groupEditor.editorWidget.instance.insertPresets(id);
							this.show(AppKit.util.fastMode ?  null : document.body);		
						},
						scope: this
					})
				} else {
					AppKit.groupEditor.editorWidget.instance.insertPresets(id);
					this.show(AppKit.util.fastMode ?  null : document.body);		
				}	
				return true;
			}
		},
		createGroup: function() {
			this.setTitle(_('Create a new group'));
			this.editGroup('new',true);
		}
	}))();
	
	wnd_groupEditPanel.render(document.body);
	
	AppKit.groups.grid =  new Ext.grid.GridPanel({
		autoHeight: true,
		tools: [{
			id: 'plus',
			qtip: _('Add new group'),
			handler: function() {wnd_groupEditPanel.createGroup();}
		},{
			id: 'minus',
			qtip: _('Remove selected groups'),
			handler: function(ev,toolEl,panel,tc) {
				panel.deleteSelected.call(panel);
			},
			scope:this
		}],		 
		
		deleteSelected: function() {
			Ext.Msg.confirm(_("Delete groups"),_("Do you really want to delete these groups?"),function(btn) {
				if(btn != "yes")
					return false;
				var selModel = this.getSelectionModel();
				var selected = selModel.getSelections();
				var ids = {};
				Ext.each(selected,function(record) {
					var currentId = record.get("role_id");
					var currentName = "group_id["+currentId+"]";
					ids[currentName] = currentId;
				},this);
				
				Ext.Ajax.request({
					url: '<?php echo $ro->gen("modules.appkit.admin.groups.remove") ?>',
					success: function() {
						this.getStore().reload();
					},
					scope:this,
					params: ids
					
				});
			},this);
		},
		
		tbar: new Ext.PagingToolbar({
			pageSize: 25,
			store: AppKit.groups.groupList,
			displayInfo: true,
			displayMsg: _('Displaying groups {0} - {1} of {2}'),
			emptyMsg: _('No groups to display'),
			items: [{
				xtype: 'tbseparator',
				width: 15
			},{
				xtype: 'displayfield',
				value: _('Hide disabled ')
				
			},{
				xtype:'checkbox',
				id:'hide_disabled',
				name: 'disabled',
				handler: function(btn, checked){
					AppKit.groups.grid.getStore().setBaseParam('hideDisabled',checked);
				}
			},{
				xtype: 'tbseparator',
				width: 15
			},{
				xtype: 'button',
				iconCls: 'icinga-icon-cancel',
				text: _('Remove selected'),
				handler: function(ev,btn) {
					AppKit.groups.grid.deleteSelected();
				},
				scope: this
			},{
				xtype: 'tbseparator',
				width: 15
			},{
				xtype: 'button',
				iconCls: 'icinga-icon-add',
				text: _('Add new group'),
				handler: function() {wnd_groupEditPanel.createGroup();}
				
			}]
			
		}), 
		
		store : AppKit.groups.groupList,
		listeners: {
			rowdblclick: function(grid,index,_e) {
				var id = AppKit.groups.groupList.getAt(index).get("role_id");
				wnd_groupEditPanel.editGroup(id);							
			},
			rowcontextmenu: function(grid,index,_e) {
				_e.preventDefault();
				var record =  grid.getStore().getAt(index);
				var id =record.get("role_id");
				grid.ctxmenu(id,record,_e.getXY());
			},
			scope: this
		},
		
		ctxmenu: function(id,record,pos) {
			new Ext.menu.Menu({
				autoDestroy:true,
				items: [{
					text:'Edit this group',
					handler: wnd_groupEditPanel.editGroup.createDelegate(wnd_groupEditPanel,[id]),
					iconCls: 'icinga-icon-pencil'
				}]			
			}).showAt(pos);
		},
		
		colModel: new Ext.grid.ColumnModel({
			defaults: {
				width:120,
				sortable:true
			},
			columns: [
				{id:'group_id', header: 'ID', width:75,  dataIndex: 'role_id'},
				{header: _('groupname'), dataIndex: 'role_name'},
				{header: _('description'), dataIndex: 'role_description'},
				{header: _('isActive'), dataIndex: 'role_disabled_icon',width:75}
			]
		}),
		autoScroll:true,
		title: _('Available groups'),

		width:800,
		sm: new Ext.grid.RowSelectionModel({
			listeners: {
				rowselect: function(model,row,record) {
					var id = record.get("role_id");
					AppKit.groups.structureView.inserted[id].ensureVisible();
					AppKit.groups.structureView.inserted[id].select();
				}
			}
		}),
		iconCls: 'icinga-icon-group'
	});
	
	
	
	/**
	 * Main Layout holding the group-grid
	 */
	var container = new Ext.Container({
		layout: 'fit',
		items: new Ext.Panel({
			layout: 'border',
			border:false,
			defaults: {
				margins: {top: 10, left: 10, right: 10, bottom: 0}
			},
			autoScroll:true,
			items: [{
				region:'center',
				xtype:'panel',
				layout:'fit',
				id:'groupListPanel',
				items: AppKit.groups.grid,
				autoScroll:true
			}, {
				region: 'east',
				width: 300,
				layout: 'fit',
				items: AppKit.groups.structureView
			}]	
		})
	});
	AppKit.util.Layout.getCenter().add(container);
	AppKit.util.Layout.doLayout();
})
	
</script>
