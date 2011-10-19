<?php
	$user =& $t["user"];
?>
<script type="text/javascript">
Ext.ns("AppKit.UserPrefs");
Ext.onReady(function() {
	
	AppKit.UserPrefs.languageStore = new Ext.data.JsonStore({
		url: '<?php echo $ro->gen("modules.appkit.data.languages") ?>',
		storeId: 'availableLocales',
		root: 'locales',
		idProperty: 'id',
		fields: ['id','description','isCurrent'],
		successProperty: 'success'
		
	});
	
	AppKit.UserPrefs.container = new Ext.Container({

		layout: 'border',
		border:false,

		style: 'height:500px',
		autoScroll:true,
		defaults: {
			border: false
		},

		items: [{
			layout:'fit',
			region: 'center',

			items: new Ext.form.FormPanel({
				padding:5,
				autoScroll:true,
				border:false,
				width: 250,
				items: [{
					xtype:'fieldset',
					title: _('Language settings'),
					padding:5,
					width:Ext.getBody().getWidth()*0.50,
					layout:'form',
					defaults: {
						labelWidth: 100
					},
					items: [{
						fieldLabel: _('Language'),
						xtype: 'combo',
						store: AppKit.UserPrefs.languageStore,
						mode: 'remote',
						value: '<?php echo $tm->getCurrentLocaleIdentifier(); ?>',
						valueField: 'id',
						displayField: 'description',
						id:'cmb_language',
						editable: false,
						triggerAction: 'all'
					}],
					buttons: [{
						text: _('Change language'),
						handler: function(b,e) {
							Ext.Ajax.request({
								url: '<?php echo $ro->gen("my.preferences") ?>',
								params: {
									upref_key: 'org.icinga.appkit.locale',
									upref_val: Ext.getCmp('cmb_language').getValue(),
									isLong: false
								},
								success: function() {
									AppKit.notifyMessage(_('Language changed'), _('Your default language hast changed!'));
								}
							});
						}
					}]
				}
<?php if(!$t["isDemoSystem"])  { ?>
				,{
					title:_('Change Password'),
					xtype: 'fieldset',
					padding:5,
					width:Ext.getBody().getWidth()*0.50,
					layout:'form',
					items: [{
						xtype:'textfield',
						inputType:'password',
						fieldLabel: _('New password'),
						id: 'passwd_new',
						minLength: 6,
						allowBlank:false
					},{
						xtype:'textfield',
						inputType:'password',
						fieldLabel: _('Confirm password'),
						id: 'passwd_confirm',
						validator: function(val) {
							var passwd = Ext.getCmp('passwd_new');
							if(passwd.isValid()) {
								if(passwd.getValue() != val)
									return _("The passwords don't match!");
							}
							return true;
						}
					}],
					buttons: [{
						text: _('Save password'),
						handler: function(b,e) {
							var passwd = Ext.getCmp('passwd_new');
							var confirm = Ext.getCmp('passwd_confirm');
							if(passwd.isValid() && confirm.isValid()) {
								var mask = new Ext.LoadMask(Ext.getBody(), {msg: _("Saving")});
								mask.show();
								Ext.Ajax.request({
									url: '<?php echo $ro->gen("my.preferences") ?>',
									params: {newPass: passwd.getValue()},
									callback: function() {
										mask.hide();
									},
									success: function() {
										mask.hide();
										Ext.Msg.alert(_("Password changed"),_("The password was successfully changed"));
									}
								});
							}
						}
					}]
				}
<?php } ?>
				,{
					title: _('Advanced'),
					type:'fieldset',
					collapsible:true,
					collapsed:true,
					autoHeight: true,
					borders:true,
					width:Ext.getBody().getWidth()*0.50,
					tools: [{
						id: 'plus',
						handler: function(event,tool,panel,tc) {
							// var id = Ext.id();
							var win = AppKit.UserPrefs.addProperty();
							win.show();
						},
						scope: this
					}],
					items: new Ext.grid.PropertyGrid({
						clicksToEdit: 2,

						autoHeight: true,
						selModel: new Ext.grid.RowSelectionModel({singleSelect: true}),
						striperows:true,
						height: 220,
						source: <?php echo json_encode($user->getPreferences()) ?>,
						id: 'pedit_preferences',
						listeners: {
							beforeedit: function(event)  {
								if(event.value == 'BLOB') {
									AppKit.notifyMessage(_("Can't modify"),_("This item is read only!"));
									return false;
								}
							},
							rowcontextmenu: function(grid,rowIndex,e) {
								e.preventDefault();
								var record = grid.getStore().getAt(rowIndex);
								new Ext.menu.Menu({
									items: [{
										text: _('Remove this preference'),
										iconCls: 'icinga-icon-cancel',
										handler: function() {
											var mask = new Ext.LoadMask(Ext.getBody(), {msg: _("Saving")});
											mask.show();
											var params = record.data;
											params["upref_key"] = params["name"];
											params["remove"] = true;
											record.store.remove(record);
											Ext.Ajax.request({
												url: '<?php echo $ro->gen("my.preferences") ?>',
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
						text: _('Save these preferences'),
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
									url: '<?php echo $ro->gen("my.preferences") ?>',
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
		}, {
			region: 'east',
			padding: 5,
			width: 220,

			items: [{
				title: _('Reset application state'),
				xtype: 'fieldset',
				labelWidth: 150,
				layoutConfig: {
					padding: 5
				},
				items: [{
					xtype: 'label',
					text: _('To start with a new application profile, click the following button.')
				},{
					xtype: 'button',
					text: 'Reset',
					style: 'margin: 10px 0 10px 20px',
					iconCls: 'icinga-icon-user-delete',
					handler: function() {
						var mask = new Ext.LoadMask(Ext.getBody(), {msg: _("Saving")});
						mask.show();
						try {
							Ext.Ajax.request({
								url: '<?php echo $ro->gen("my.preferences") ?>',
								params: {
									upref_key: 'org.icinga.ext.appstate',
									remove: true
								},
								callback: function() {
									mask.hide();
								},
								success: function() {
									AppKit.notifyMessage(_('App reset'), _('Your application profile has been deleted!'));
								}

							});
						} catch(e) {
							mask.hide();
							AppKit.log(e);
						}
					}
				}]
			}]
		}]

	});
	
	AppKit.UserPrefs.addProperty = function() {
		var wid = 'userpref_customproperty_target';
		
		if (!Ext.getCmp(wid)) {
			new Ext.Window({
				layout:'fit',
				width:330,

				title: _('New Preference'),
				closeMethod: 'hide',
				hidden:false,
				id: wid,
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
						}
					}]
				})
			});
		}
		
		return Ext.getCmp(wid);
	}
	
	if (Ext.getCmp('user_prefs_target')) {
		Ext.getCmp('user_prefs_target').setWidth(Ext.getBody().getWidth()*0.70);
		Ext.getCmp('user_prefs_target').add(AppKit.UserPrefs.container);
		AppKit.UserPrefs.container.doLayout();
		Ext.getCmp('user_prefs_target').doLayout();
	}
	
	
});
</script>