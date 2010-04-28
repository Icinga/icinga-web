<script type='text/javascript'>
Ext.ns("AppKit.users")
Ext.onReady(function() {
	AppKit.users.userList = new Ext.data.JsonStore({
		autoDestroy: true,
		storeId: 'userListStore',
		idProperty: 'user_id',
		autoLoad:true,
		url: '<? echo $ro->gen("appkit.admin.data.users")?>',
		fields: [
			{name: 'user_id', type:'int'},
			'user_name',
			'user_lastname',
			'user_firstname',
			'user_email',
			{name: 'user_disabled',type:'boolean',convert: function(v) {
				return '<div style="width:16px;height:16px;margin-left:25px" class="'+(v==1? 'silk-cancel' : 'silk-accept')+'"></div>';
			}},
			{name: 'user_created'},
			{name: 'user_modified'}
		]
	})
	
	var wnd_userEditPanel = new (Ext.extend(Ext.Window,{
		width:600,
		height: 600,
		layout:'fit',
		title: _('Edit user'),
		closeAction: 'hide',
		stateful:false,
		y:0,
		modal:true,
		id: 'wnd_userEditPanel',
		listeners: {
			close: AppKit.users.userList.reload.createDelegate(AppKit.users.userList),
			hide: AppKit.users.userList.reload.createDelegate(AppKit.users.userList)
		},
		editUser: function(id,_new) {
			if(!_new) {
				this.setTitle(_('Edit user'));
			}
			if(!this.uiFormLoaded) {
				if(!Ext.isDefined(id))  {
					Ext.Msg.alert(__("Error"),_("Unknown id"));
					return false;
				}
				if(!AppKit.userEditor) {
					this.getUpdater().update({
						url : String.format('<? echo $ro->gen("appkit.admin.users.edit")?>{0}/{1}',id,'wnd_userEditPanel'),
						scripts: true,
						callback: function(el,success,response,options) {
							AppKit.userEditor.editorWidget.instance.insertPresets(id);
							this.show(document.body);		
						},
						scope: this
					})
				} else {
					AppKit.userEditor.editorWidget.instance.insertPresets(id);
					this.show(document.body);		
				}	
				return true;
			}
		},
		createUser: function() {
			this.setTitle(_('Create a new user'));
			this.editUser('new',true);
		}
	}))();
	
	wnd_userEditPanel.render("contentArea");
	var grid =   new Ext.grid.GridPanel({
		autoHeight: true,
		tools: [{
			id: 'plus',
			qtip: _('Add new user'),
			handler: function() {wnd_userEditPanel.createUser();}
		},{
			id: 'minus',
			qtip: _('Remove selected users'),
			handler:  function(ev,toolEl,panel,tc) {
				Ext.Msg.confirm(_("Delete user"),_("Do you really want to delete these users?"),function(btn) {
					if(btn != "yes")
						return false;
					var selModel = this.getSelectionModel();
					var selected = selModel.getSelections();
					var ids = {};
					Ext.each(selected,function(record) {
						var currentId = record.get("user_id");
						var currentName = "user_id["+currentId+"]";
						ids[currentName] = currentId;
					},this);
					
					Ext.Ajax.request({
						url: '<?echo $ro->gen("appkit.admin.users.remove") ?>',
						success: function() {
							this.getStore().reload();
						},
						scope:this,
						params: ids
						
					});
				},panel)
			},
			scope:this
		}],		 
		
		store : AppKit.users.userList,
		
		listeners: {
			rowdblclick: function(grid,index,_e) {
				var id = grid.getStore().getAt(index).get("user_id");
				wnd_userEditPanel.editUser(id);											
			},
			rowcontextmenu: function(grid,index,_e) {
				_e.preventDefault();
				var record =  grid.getStore().getAt(index);
				var id =record.get("user_id");
				grid.ctxmenu(id,record,_e.getXY());
			},
			scope: this
		},
		
		ctxmenu: function(id,record,pos) {
			new Ext.menu.Menu({
				autoDestroy:true,
				items: [{
					text:'Edit this user',
					handler: wnd_userEditPanel.editUser.createCallback(id),
					iconCls: 'silk-pencil'
				}]			
			}).showAt(pos);
		},
		
		colModel: new Ext.grid.ColumnModel({
			defaults: {
				width:120,
				sortable:true
			},
			columns: [
				{id:'user_id', header: 'ID', width:75,  dataIndex: 'user_id'},
				{header: _('username'), dataIndex: 'user_name'},
				{header: _('lastname'), dataIndex: 'user_lastname'},
				{header: _('firstname'), dataIndex: 'user_firstname'},
				{header: _('email'),dataIndex: 'user_email'},
				{header: _('inactive'), dataIndex: 'user_disabled',width:75}
			]
		}),
		autoScroll:true,
		title: _('Available users'),

		width:800,
		sm: new Ext.grid.RowSelectionModel(),
		iconCls: 'silk-user'
	});
	
	/**
	 * Main Layout holding the user-grid
	 */
	var container = new Ext.Container({
		layout: 'fit',
		width:800,
		items: new Ext.Panel({
			layout: 'border',
			border:false,
			defaults: {
				margins: {top: 10, left: 10, right: 10, bottom: 0},
			},
			autoScroll:true,
			items: [{
				region:'center',
				xtype:'panel',
				layout:'fit',
				id:'userListPanel',
				items: grid,
				autoScroll:true
			}]	
		})
	});
	container.on('afterrender', function() {
		container.setHeight(Ext.lib.Dom.getViewHeight() - 68);
		
	}, container, { single: true });
		
	container.render("contentArea");
	container.doLayout();
	
	Ext.EventManager.onWindowResize(function(w,h) {
		this.setHeight(Ext.lib.Dom.getViewHeight() - 68);
			
		
		this.doLayout();
	}, container);
	
})
	
</script>