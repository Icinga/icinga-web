<script type='text/javascript'>
Ext.Msg.minWidth = 200;
Ext.onReady(function() {
	Ext.ns("lconf.Admin");
	<?php echo $t["js_editWindow"]; ?>		

	lconf.Admin.connectionTbar = function() {
		var tbar = new Ext.Toolbar({
			items: [{
				text:_('Remove connections'),
				iconCls: 'icinga-icon-cancel',
				handler: lconf.Admin.removeSelected
			}]
		});
		return tbar;		
	}
	
	/**
	 * The connection listing dataview
	 * 
	 * @return Ext.DataView 
	 *
	 **/
	lconf.Admin.connectionList = new function() {
		var recordSkeleton = Ext.data.Record.create([
			'connection_id','connection_name','connection_description','connection_binddn',
			'connection_bindpass','connection_host','connection_port','connection_basedn','connection_tls', 
			'connection_ldaps','connection_default'
		]);
		
		this.addConnection = function(values) {
			var record = new recordSkeleton(values);
			if(!values.connection_ldaps)
				values.connection_ldaps = false;
			this.dStore.add([record]);
			this.dStore.save();
		}
		
		this.testConnection = function(values) {
			if(this.ld_mask)
				this.ld_mask.hide();
			
			this.ld_mask = new Ext.LoadMask(Ext.getBody(), {msg:_("Please wait...")});
			this.ld_mask.show();
			Ext.Ajax.request({
				url:"<?php echo $ro->gen('modules.lconf.data.connect')?>",
				params: {
					testOnly:true,
					connection: Ext.encode(values)
				},
				success: function(response) {
					if(this.ld_mask)
						this.ld_mask.hide();			
					Ext.MessageBox.alert(_("Success"),_("Connecting succeeded!"));
				},
				failure: function(response) {
					if(this.ld_mask)
						this.ld_mask.hide();
					Ext.Msg.minWidth = 500;
					Ext.MessageBox.alert(_("Error"),_("Connecting failed!<br/><br/>")+response.responseText);
					Ext.Msg.minWidth = 200;
				},
				scope:this
			});
		}
		
		this.restProxy = new Ext.data.HttpProxy({
			method:'GET',
			restful:true,
			url: "<?php echo $ro->gen('modules.lconf.data.connectionlisting'); ?>"

		});
		
		this.dStore = new Ext.data.JsonStore({
			autoLoad:true,
			autoSave: false,
			root:'result',
			listeners: {
				// Check for errors
				exception : function(prox,type,action,options,response,arg) {
					if(this.ld_mask)
						this.ld_mask.hide();
					if(response.status == 200)
						return true;
					response = Ext.decode(response.responseText);
					if(response.error.length > 100)
						response.error = _("A critical error occured, please check your logs");
					Ext.Msg.alert("Error", response.error);
				},
				load: function() {
					
				},
				beforeLoad: function() {
					
				},
				save: function(store) {
					if(this.ld_mask)
						this.ld_mask.hide();
					store.load();
				}, 
				
				beforesave: function() {
					if(this.ld_mask)
						this.ld_mask.hide();
				
					this.ld_mask = new Ext.LoadMask(Ext.getBody(), {msg:_("Please wait...")});
					this.ld_mask.show();	
					
				},
				scope: this
			},
			autoDestroy:true,
			fields: [
				'connection_id','connection_name','connection_description','connection_binddn',
				'connection_bindpass','connection_host','connection_port','connection_basedn','connection_tls',
				'connection_ldaps','connection_default','is_owner'
			],
			writer:new Ext.data.JsonWriter({encode: true}),
			idProperty:'connection_id',
			root: 'connections',
			proxy: this.restProxy
		})
		
		var _t = this.dStore.reader.realize 
		this.dStore.reader.realize = function() {
			try {
				_t.realize(arguments);
			} catch(e) {}
		}
		
		this.tpl =new Ext.XTemplate(
			'<tpl for=".">',
				'<tpl if="is_owner == true">',
				'<div class="ldap-connection {icon}" ext:qtip="{connection_description}" id="conn_{connection_id}">',
					'<div class="thumb lconf-icon-{icon}"></div>',
					'<span class="X-editable"><b>{connection_name}</b></span><br/>',					
					'<span class="X-editable">',
					'<tpl if="connection_ldaps == true">ldaps://</tpl>',
					'{connection_host} {connection_port}</span><br/>',	
				'</div>',
				'</tpl>',
			'</tpl>'
		);
		
		this.dView = new Ext.DataView({
			store:this.dStore,
			tpl:this.tpl,
			multiSelect: true,
			overClass:'x-view-over',
			selectedClass:'x-view-select',
			itemSelector: 'div.ldap-connection',
			emptyText: _('No connections defined yet'),
			listeners: {
				click : function(dview,index,htmlNode,event) {
					var record = dview.getStore().getAt(index);
					lconf.Admin.container.layout.east.panel.setDisabled(false);	
					if(lconf.Admin.getPrincipalEditor()) {
						lconf.Admin.getPrincipalEditor().ownerCt.ownerCt.show();
						if(record.get("connection_id") > -1)
							lconf.Admin.getPrincipalEditor().cmp.populate(record.get("connection_id"));
					}
					Ext.getCmp('form_lconfUserPanel').items.items[0].getForm().setValues(record.data);
				},
				scope: this
			}
		});
		
		this.dStore.on("load",function() {
			// prepend "Add Connection"
			var el = this.dView.getTemplateTarget();
			var added = this.dView.tpl.insertBefore(el, {connection_id: -1, connection_name: _('Add new connection'), icon: 'plus', connection_ldaps: false, is_owner:true  },true);
			added.on("click",function() {
				//lconf.Admin.getPrincipalEditor().cmp.populate(-1);
				lconf.Admin.container.layout.east.panel.setDisabled(false);
				if(lconf.Admin.getPrincipalEditor())
					lconf.Admin.getPrincipalEditor().ownerCt.ownerCt.hide();
				
				Ext.getCmp('form_lconfUserPanel').items.items[0].getForm().reset();
				Ext.getCmp('form_lconfUserPanel').items.items[0].getForm().setValues({connection_id: -1,connection_name:_('New connection'),is_owner:true});
				return false;
			})
		}, this, {single:true});
	}
	
	lconf.Admin.removeSelected = function() {
		var records = lconf.Admin.connectionList.dView.getSelectedRecords();
		if(records.length == 0) {
			Ext.MessageBox.alert(_("Error"),_("No connection selected"));
			return false;
		}
		Ext.MessageBox.confirm(
			_("Delete selected connections"),
			_("Do you really want to delete this ")+records.length+_(" connection(s)?"),
			function(btn) {
				if(btn != "yes")
					return false;
				var store = this[0].store;

				Ext.each(this,function(record) {
					store.remove(record);
					store.save();
				});
			},
			records
		);
	}
	
	/**
	 * Displays the user interface panel for adding connections
	 * 
	 * @return void
	 */
	lconf.Admin.getUserPanel = function(defaults) {

		var _id = "lconfUserPanel";
		if(this.userPanel)
			return this.userPanel;

		this.userPanel = new Ext.Panel({
			width:500,
			layout:'form',
			padding:5,
			id: 'form_'+_id,
			monitorValid:true,
			title:'<b>'+_('Connection detail')+'</b>',
			iconCls: 'icinga-icon-application-edit',
			items: [{
				xtype: 'form',
				border:false,
				id: _id,
				bodyStyle:'background:none',
				items: [
				{
					xtype:'hidden',
					name: 'connection_id'
				},
				{	
					xtype:'fieldset',
					title: _('General details'),
					defaults: {
						xtype:'textfield'
					},
					items: [{
						fieldLabel: _('Connection Name'),
						name: 'connection_name',
						anchor:'95%',
						allowBlank:false
					}, {
						xtype:'textarea',
						fieldLabel: _('Connection Description'),
						name: 'connection_description',
						anchor: '95%',
						height: 100
					}]
				},{
					xtype:'fieldset',
					title:_('Authorization'),
					defaults: {
						xtype:'textfield'
					},
					items: [{
						fieldLabel:_('Bind DN'),
						name: 'connection_binddn',
						anchor:'70%'
					},{
						fieldLabel:_('Bind Pass'),	
						name: 'connection_bindpass',
						anchor:'70%',
						inputType:'password'
					}]
				},{
					xtype:'fieldset',
					title:_('Connection Details'),
					defaults: {
						xtype:'textfield'
					},
					items: [{
			
						xtype:'textfield',
						fieldLabel:_('Host'),
						allowBlank: false,
						name: 'connection_host',
						layout:'form',
						anchor:'70%'
					},{
						xtype:'numberfield',
						fieldLabel:_('Port'),
						allowBlank:false,
						name: 'connection_port',
						defaultValue: 389,				
						layout:'form',
						anchor:'70%'
					},{
						fieldLabel:_('Root DN'),
						name: 'connection_basedn',
						anchor:'70%'
					}, {
						xtype:'checkbox',
						name: 'connection_tls',
						fieldLabel: _('Use TLS')
						
					},{
						xtype:'checkbox',
						name: 'connection_ldaps',
						fieldLabel: _('Enable SSL (ldaps://)')
						
					}]
				}]
			}],
			buttons: [{
				text: _('Check connection'),
				iconCls: 'icinga-icon-world',
				parentId: _id,
				handler: function(btn,e) {
					var form = Ext.getCmp(btn.parentId);
					if(!form.getForm().isValid()) 
						return false;
					var values = form.getForm().getValues();
					values.is_owner = true;
					lconf.Admin.connectionList.testConnection(values);
					
				}
			}, {
				text: _('Save'),
				iconCls: 'icinga-icon-disk',	
				formBind:true,
				parentId: _id,
				handler: function(btn,e) {
					var form = Ext.getCmp(btn.parentId);
					if(!form.getForm().isValid()) 
						return false;
					var values = form.getForm().getValues();
					values.is_owner = true;
					lconf.Admin.connectionList.addConnection(values);
			
				}
			}]
		});
		return this.userPanel;	
	}
	
	

	var modItems =  [
		lconf.Admin.getUserPanel(), 
		lconf.Admin.getPrincipalEditor()
	]
	lconf.Admin.container = new Ext.Panel({
		layout:'border',
		id: 'view-container',
		items: [{
			title: _('Connections'),
			region: 'center',
			id: 'connection-frame',
			layout: 'fit',
			margins:'5 0 5 5',
			cls: false,
			autoScroll:true,
			tbar: lconf.Admin.connectionTbar(),
			items: lconf.Admin.connectionList.dView
		},{
			region: 'east',
			id: 'user-frame',
			disabled:true,
			//collapsible: true,
			split:true,
			margins:'5 5 5 0',
			activeItem:0,
			xtype: 'panel',
			layout: 'fit',
			items: {
				xtype: 'panel',
				layout: 'accordion',
				items: modItems.remove(null)			
			},
			width:"50%"
		}]
	});
	

	
	AppKit.util.Layout.getCenter().add(lconf.Admin.container);
	AppKit.util.Layout.doLayout();

})
</script>
