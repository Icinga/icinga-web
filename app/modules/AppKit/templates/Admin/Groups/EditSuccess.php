<script type='text/javascript'>
Ext.ns("AppKit.groupEditor");

<?php
	$users = $t["users"];
	echo $t['principal_editor'];
?>

AppKit.groupEditor.STD_CONTAINER= "contentArea";
Ext.onReady(function(){
	var container = "<?php echo $t['container'] ?>";
	if(!container)
		container = AppKit.groupEditor.STD_CONTAINER;
	
	var initEditorWidget = function() {
		AppKit.groupEditor.formFields = [
			{
				xtype: 'hidden',
				name: 'role_id',
				id: 'role_id'
			},{
				xtype:'fieldset',
				title: _('General information'),
				defaults: {
					allowBlank: false
				},
				items: [{
					xtype:'textfield',
					fieldLabel: _('Group name'),
					name: 'role_name',
					id: 'role_name',
					anchor: '95%',
					minLength: 3,
					maxLength: 18
				},{
					xtype:'textfield',
					fieldLabel: _('Description'),	
					name: 'role_description',
					id: 'role_description',
					anchor: '95%'
					
				}, {
					xtype: 'checkbox',
					name: 'role_disabled',
					id: 'role_disabled',
					fieldLabel: _('Disabled')
				}]
			},{
				xtype:'spacer',
				height:25
			},{
				xtype: 'fieldset',
				title: _('Meta information'),
				items: [{
					xtype:'displayfield',
					fieldLabel: _('Created'),
					name: 'role_created',
					id: 'role_created',
					preventMark: true,
					allowBlank: true,
					anchor: '95%'				
				},{
					xtype:'displayfield',
					fieldLabel: _('Modified'),
					name: 'role_modified',
					id: 'role_modified',
					preventMark: true,
					allowBlank: true,
					anchor: '95%'
				}]
			},{
				xtype:'fieldset',
				title: _('Permissions'),
				items: [{
					xtype:'panel',
					layout:'auto',
					autoHeight:true,
					collapsible: true,
					collapsed: false,
					title: _('Users'),
					anchor: '95%',
					padding:4,
					labelWidth:400,
					qtip: _("Click to edit user"),
					id: 'groupUsers',
					items: []
				}, {
					xtype:'panel',
					layout: 'fit',
					title: _('Principals'),		
					anchor: '95%',
					collapsible:true,
					collapsed:true,
					id:'principalsPanel',
					
					items: AppKit.principalEditor.instance
				}]
			}
		]
		
	
	
		AppKit.groupEditor.editorWidget = Ext.extend(Ext.form.FormPanel,{
			constructor: function(cfg) {
				if(!cfg)
					cfg = {}
				cfg.items =  AppKit.groupEditor.formFields
				Ext.apply(this.cfg);
				AppKit.groupEditor.editorWidget.superclass.constructor.call(this,cfg);
				this.addButton({text: _('Save')},this.saveHandler,this);
				
			},
			
			saveHandler: function(b,e) {
				if(this.getForm().isValid()) {
				 	values = this.getForm().getValues();
				 	this.addPrincipalsToForm(values);
				 	var roleId = values["role_id"];
				 	values["id"] = values["role_id"]
				 	values["password"] = values["role_password"];
				 	values["password_validate"] = values["role_password_confirmed"];
				 	values["role_disabled"] = (values["role_disabled"] == "on" ? 1 : 0)
					values["role_users"] = ""
					Ext.each(Ext.getCmp('role_users').getValue(),function(chkbox) {
					   values["role_users"]+=chkbox.id+";";
					});
				 	Ext.Ajax.request({
						url: '<?php echo $ro->gen("modules.appkit.admin.groups.alter")?>'+roleId,
						params: values,
						success: function() {
							if(Ext.getCmp('<?php echo $t["container"] ?>'))
								Ext.getCmp('<?php echo $t["container"] ?>').hide();
						},
						scope:this
				 	});
				 	
				 	return true;
				}
			 	Ext.Msg.alert(_("Error"),_("One or more fields are invalid"));			
			},
			
			// Default style setting
			layout:'form',
			padding:5,
			autoScroll:true,
			defaults: {
				padding:3,
				xtype:'textfield',
				anchor: '95%'
			},
			
			
			fillRoleValues: function(roleVals) {
				var form = this.getForm();
				var blank = {};
				var elemVals = form.getFieldValues();
				for(var i in elemVals) {
					blank[i] = '';
				}
				form.setValues(blank);
				form.setValues(roleVals);
			},
			
			addPrincipalsToForm: function(values) {
				if(Ext.isFunction( AppKit.principalEditor.instance.getPrincipals)) {
					principalData =  AppKit.principalEditor.instance.getPrincipals();

				 	this.objToForm(principalData.principal_values,values,"principal_value");
					this.objToForm(principalData.principal_target,values,"principal_target");
					
				}		
			},
			
			objToForm: function(obj,values,prefix) {
				this.getKeyValPairs(obj,values,prefix || "");
			},

			getKeyValPairs: function(obj,arr,prefix) {
				if(!prefix)
					prefix = "";
				
				for(var i in obj) {
					if(Ext.isFunction(obj[i]))
						continue;
					var newPrefix = prefix;
					if(!(Ext.isArray(obj) && obj.length == 1))
						newPrefix = newPrefix+"["+i+"]";
					else 
						newPrefix = newPrefix+"[]";
					var val = obj[i];
					if(Ext.isArray(val) || Ext.isObject(val)) {
						this.getKeyValPairs(val,arr,newPrefix);
					} else
						arr[newPrefix] = val;
				}

			},
			
			showGroupUsers: function(data) {

				var cmp = Ext.getCmp('groupUsers');
                var items = [];
				var allUsers = <?php echo json_encode($users);?>;
				var usernames = {};
				Ext.each(data["users"],function(u) {usernames[u.user_name] = true});
				Ext.each(allUsers,function(user) {
					items.push({
                        boxLabel: user.user_name,
						name: user.user_name,
						id: user.user_id,
                        checked: Ext.isDefined(usernames[user.user_name])
                      })
				});
                var group = new Ext.form.CheckboxGroup({name:'role_users',id:'role_users',items:items,columns:2});
				cmp.add(group);
				cmp.doLayout();
			},
					
			insertPresets: function(id)	{
				Ext.getCmp('groupUsers').removeAll();
				AppKit.principalEditor.instance.clearPrincipals();
				if(id == 'new')  {
					this.fillRoleValues({
						'role_id' : 'new',
						'role_users' : []
					});
					this.showGroupUsers({users : []});
					return true;
				}
				Ext.Ajax.request({
					url: '<?php echo $ro->gen("modules.appkit.data.groups")?>/'+id,
					success: function(resp,options) {
						var data = Ext.decode(resp.responseText);
						data = data.roles;
						this.fillRoleValues(data);
						this.showGroupUsers(data);
						AppKit.principalEditor.instance.loadPrincipalsForRole(data.role_id);
					},
					scope:this
					
				})			
			}
		});
		
		AppKit.groupEditor.editorWidget.instance = new AppKit.groupEditor.editorWidget();
		var container = '<?php echo $t["container"] ?>';
		/**
		 * Refill the form with the role values
		 */
		 var editor = AppKit.groupEditor.editorWidget.instance;
		
	
		if(Ext.getCmp(container)) {
			Ext.getCmp(container).add(editor);
		} else {
			editor.on("afterrender",function(){
				this.doLayout()
			},editor);
			editor.render(container);
		}
	}
	/*
	 * Build the form if not done yet
	 */
	if(!AppKit.groupEditor.editorWidget)
		initEditorWidget();
	

})
</script>
