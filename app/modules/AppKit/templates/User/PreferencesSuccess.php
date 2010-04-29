<?
$user = $t["user"];

?>

<script type='text/javascript'>
Ext.ns("AppKit.UserPrefs")
Ext.onReady(function() {
	AppKit.UserPrefs.languageStore = new Ext.data.JsonStore({
		url: '<? echo $ro->gen("appkit.data.languages") ?>',
		storeId: 'availableLocales',
		root: 'locales',
		idProperty: 'id',
		fields: ['id','description','isCurrent'],
	
	});
	
	AppKit.UserPrefs.container = new Ext.Container({
		layout:'fit',
		border:false,
		items: new Ext.form.FormPanel({
			padding:5,
			border:false,
			items: [{
				xtype:'fieldset',
				width:500,
				title: _('Language settings'),
				padding:5,
				layout:'form',
				defaults: {
					labelWidth: 250	
				},
				items: [{
					fieldLabel: _('Language'),
					xtype: 'combo',
					store: AppKit.UserPrefs.languageStore,
					mode: 'remote',
					value: '<? echo $tm->getCurrentLocaleIdentifier(); ?>',
					valueField: 'id',
					displayField: 'description',
					id:'cmb_language',
					triggerAction: 'all'
				}],
				buttons: [{
					text: _('Change language'),
					handler: function(b,e) {
						Ext.Ajax.request({
							url: '<? echo $ro->gen("my.preferences") ?>',
							params: {
								upref_key: 'de.icinga.appkit.locale',
								upref_val: Ext.getCmp('cmb_language').getValue(),
								isLong: false
							},
							success: function() {
								window.location.href = '<? echo $ro->gen("my.preferences") ?>';
							}
						});
					}
				}],
			},{
				title: _('Advanced'),
				type:'fieldset',
				collapsible:true,	
				collapsed:true,
				autoHeight: true,		
				borders:true,
				width:500,
				tools: [{
					id: 'plus',
					handler: function(event,tool,panel,tc) {
						var id = Ext.id();
						AppKit.UserPrefs.addProperty();
					},
					scope: this
				}],
				items: new Ext.grid.PropertyGrid({
					clicksToEdit: 2,

					autoHeight: true,					
					selModel: new Ext.grid.RowSelectionModel({singleSelect: true}),
					striperows:true,
					width:500,
					source: <? echo json_encode($user->getPreferences()) ?>,
					id: 'pedit_preferences',
					listeners: {
						beforeedit: function(event)  {
							if(event.value == 'BLOB') {
								AppKit.Ext.infoField("This item is read only!",2);
								return false;
							}
						},
						rowcontextmenu: function(grid,rowIndex,e) {
							e.preventDefault();
							var record = grid.getStore().getAt(rowIndex);
							new Ext.menu.Menu({
								items: [{
									text: _('Remove this preference'),
									iconCls: 'silk-cancel',
									handler: function() {
										var mask = new Ext.LoadMask(Ext.getBody(), {msg: _("Saving")});
										mask.show();
										var params = record.data;
										params["upref_key"] = params["name"];
										params["remove"] = true;
										record.store.remove(record);
										Ext.Ajax.request({
											url: '<? echo $ro->gen("my.preferences") ?>',
											params: params,
											callback: function() {
												mask.hide();
											}
										});
									}
								}]
							}).showAt(e.getXY());
						}
					}
				}),
				buttons: [{
					text: _('Save changes'),
					handler: function(b,e) {
						var mask = new Ext.LoadMask(Ext.getBody(), {msg: _("Saving")});
						mask.show();
						try {
							var preferences = Ext.getCmp('pedit_preferences');
							var params = {};
							var i = 0;
							var store = preferences.getStore();
							store.each(function(record) {
								if(record.get("value") == 'BLOB')
									return null;
									
								params["params["+(i)+"][upref_key]"] = record.get("name");
								params["params["+(i)+"][upref_val]"] = record.get("value");
								params["params["+(i++)+"][isLong]"] = false
							})
							Ext.Ajax.request({
								url: '<? echo $ro->gen("my.preferences") ?>',
								params: params,
								callback: function() {
									mask.hide();
								}
							});
						} catch(e) {
							mask.hide();
							AppKit.log(e);
						}
					}
				}]
			
			}]
		})
	});
	AppKit.UserPrefs.addProperty = function() {
		new Ext.Window({
			layout:'fit',
			width:330,
			title: _('New Preference'),
			autoDestroy:true,
			hidden:false,
			id: 'win_'+id,
			height:150,
			items: new Ext.form.FormPanel({
				layout: 'form',				
				width:300,
				padding:5,
				id: id,
				items: [{
					fieldLabel:'Key',
					xtype: 'textfield',
					allowBlank:false,
					id: 'key_'+id
				}, {
					fieldLabel:'Value',
					xtype: 'textfield',
					id: 'value_'+id
				}],
				buttons: [{
					text: _('Add'),
					handler: function (btn,e) {
						if(!Ext.getCmp(id).getForm().isValid())
							return false;
						var record = Ext.data.Record.create(['name','value']);
						var store = Ext.getCmp('pedit_preferences').getStore();
						store.add(new record({
										name: Ext.getCmp("key_"+id).getValue(),
										value: Ext.getCmp("value_"+id).getValue()
								  }));
						Ext.getCmp('win_'+id).close();
					},
				}]
			})
		}).render(document.body);
	}
	var container = AppKit.UserPrefs.container;
	container.on('afterrender', function() {
		container.setHeight(Ext.lib.Dom.getViewHeight() - 68);
		
	}, container, { single: true });
		
	container.render("contentArea");
	container.doLayout();
	
	Ext.EventManager.onWindowResize(function(w,h) {
		this.setHeight(Ext.lib.Dom.getViewHeight() - 68);
			
		
		this.doLayout();
	}, container);
	
});

</script>