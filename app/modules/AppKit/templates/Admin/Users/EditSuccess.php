<?php 
$user = $t['user'];
$roles = $t['roles'];
$authTypes = $t['authTypes'];
?>
<script type='text/javascript'>
Ext.ns("AppKit.userEditor");
if(!Ext.isFunction(window._))
	_ = function(t) {return t}
<?php
	echo $t['principal_editor'];
?>

AppKit.userEditor.STD_CONTAINER= "contentArea";
Ext.onReady(function(){
	var container = "<?php echo $t['container'] ?>";
	if(!container)
		container = AppKit.userEditor.STD_CONTAINER;
	
	var initEditorWidget = function() {
		/**
		 * Forms definition
		 *
		 **/
		AppKit.userEditor.formFields = [
			{
				xtype: 'hidden',
				name: 'user_id',
				id: 'user_id'
			},{
				xtype:'fieldset',
				title: _('General information'),
				defaults: {
					allowBlank: false
				},
				items: [{
					xtype:'textfield',
					fieldLabel: _('User name'),
					name: 'user_name',
					id: 'user_name',
					anchor: '95%',
					minLength: 3,
					maxLength: 127
				},{
					xtype:'container',
					layout: 'column',
					anchor: '100%',
					items: [{
						xtype:'container',
						layout:'form',
						
						items: {
							fieldLabel: _('Name'),
							name: 'user_firstname',			
							id: 'user_firstname',
							xtype:'textfield',
							anchor: '95%',
							allowBlank: false,
							minLength: 3,
							maxLength: 40
						},
						columnWidth:0.5
					},{
						xtype:'container',
						layout: 'form',
						labelWidth:65,
						items: {
							xtype:'textfield',
							name: 'user_lastname',	
							id: 'user_lastname',
							fieldLabel: _('Surname'),
							anchor: '90%',
							allowBlank: false,
							minLength: 3,
							maxLength: 40
						},
						columnWidth:0.5
					}]
				},{
					xtype:'textfield',
					fieldLabel: _('Email'),	
					name: 'user_email',
					id: 'user_email',
					anchor: '75%',
					regex: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i
				}, {
					xtype: 'checkbox',
					name: 'user_disabled',
					id: 'user_disabled',
					fieldLabel: _('Disabled')
				}, {
					xtype: 'combo',
					fieldLabel: _('Auth via'),
					typeAhead: true,
					name: 'user_authsrc',
					id: 'user_authsrc',
					triggerAction: 'all',
					mode:'local',
					store: new Ext.data.ArrayStore({
						id:0,
						fields: ['user_authkey'],
						data:<?php echo $authTypes ?>
					}),
					valueField: 'user_authkey',
					displayField: 'user_authkey'
				}]
			},{
				xtype:'spacer',
				height:25
			},{
				xtype: 'fieldset',
				title: _('Change Password'),
				items: [{
					xtype:'textfield',
					fieldLabel: _('Password'),
					id: 'user_password',
					name: 'user_password',
					validator: function(value) {
						if(Ext.getCmp('user_id').getValue() == 'new' && !value) 
							return _("Please provide a password for this user");
						return true;
					},
					inputType:'password',
					minLength: 6,
					maxLength: 20,
					width: '200'
				},{
					xtype:'textfield',
					fieldLabel: _('Confirm password'),
					name: 'user_password_confirmed',
					inputType:'password',
					validator: function(value)  {
						var cmp_value = Ext.getCmp('user_password').getValue();
						if(value != cmp_value && cmp_value != "") 
							return _("The confirmed password doesn't match");
						return true;		
					},
					width: '200'
				}, {
					xtype: 'textfield',
					fieldLabel: _('Authkey for Api (optional)'),
					id: 'user_authkey',
					name: 'user_authkey',
					minLength: 8,
					maxLength: 40,
					width: '200',
					regex: /[A-Za-z0-9]*/
				}]
			},{
				xtype: 'fieldset',
				title: _('Meta information'),
				items: [{
					xtype:'displayfield',
					fieldLabel: _('Created'),
					name: 'user_created',
					id: 'user_created',
					preventMark: true,
					allowBlank: true,
					anchor: '95%'				
				},{
					xtype:'displayfield',
					fieldLabel: _('Modified'),
					name: 'user_modified',
					id: 'user_modified',
					preventMark: true,
					allowBlank: true,
					anchor: '95%'
				}]
			},{
				xtype:'fieldset',
				title: _('Permissions'),
				items: [{
					xtype:'panel',
					layout:'form',
					autoHeight:true,
					collapsible: true,
					collapsed: false,
					title: _('Groups'),
					anchor: '95%',
					labelWidth:400,
					items: [
			<?php $ctr=0;foreach($roles as $role) :?>
					{
						xtype:'checkbox',
						name: 'userroles[<?php echo $role->get("role_id")?>]',
						id: 'userroles_<?php echo $role->get("role_id") ?>',
						inputValue: '<?php echo $role->get("role_id") ?>',
						fieldLabel: '<?php echo $role->get("role_name")." (".$role->get("role_description").") "; ?>'
					} <?php if(++$ctr < count($roles)) echo ',';?>
			<?php endforeach; ?>
					]
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

		/**
		 * Forms end
		 */
	
	
		AppKit.userEditor.editorWidget = Ext.extend(Ext.form.FormPanel,{
			constructor: function(cfg) {
				if(!cfg)
					cfg = {}
				cfg.items =  AppKit.userEditor.formFields;
	
				cfg.width = 600;
				Ext.apply(this,cfg);
				
				Ext.form.FormPanel.prototype.constructor.call(this,cfg);
				this.addButton({text: _('Save')},this.saveHandler,this);			
			},
			
			saveHandler: function(b,e) {
				if(this.getForm().isValid()) {
				 	values = this.getForm().getValues();
				 	this.addPrincipalsToForm(values);
				 	var userId = values["user_id"];
				 	values["id"] = values["user_id"]
				 	values["password"] = values["user_password"];
				 	values["password_validate"] = values["user_password_confirmed"];
				 	values["user_disabled"] = (values["user_disabled"] == "on" ? 1 : 0)
				 	Ext.Ajax.request({
						url: '<?php echo $ro->gen("modules.appkit.admin.users.alter")?>'+userId,
						params: values,
						success: function() {
							if(Ext.getCmp('<?php echo $t["container"] ?>'))
								Ext.getCmp('<?php echo $t["container"] ?>').hide();
							else {
								AppKit.changeLocation('<?php echo $ro->gen("modules.appkit.admin.users") ?>');
							}
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
			
			
			fillUserValues: function(userVals) {
				var form = this.getForm();
				var blank = {};
				var elemVals = form.getFieldValues();
				for(var i in elemVals) {
					blank[i] = '';
				}
				form.setValues(blank);
				form.setValues(userVals);
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
			
			insertPresets: function(id)	{
				AppKit.principalEditor.instance.clearPrincipals();
				if(id == 'new')  {
					this.fillUserValues({
						'user_id' : 'new',
						'user_roles' : []
					});
					return true;
				}
				Ext.Ajax.request({
					url: '<?php echo $ro->gen("modules.appkit.data.users")?>/'+id,
					success: function(resp,options) {
						var data = Ext.decode(resp.responseText);
						this.fillUserValues(data.users);
						AppKit.principalEditor.instance.loadPrincipalsForUser(data.users.user_id);
					},
					scope:this
					
				})			
			}
		});

		AppKit.userEditor.editorWidget.instance = new AppKit.userEditor.editorWidget({maxWidth:600});
		var container = '<?php echo $t["container"] ?>';

		/**
		 * Refill the form with the user values
		 */
		 var editor = AppKit.userEditor.editorWidget.instance;

		if(Ext.getCmp(container)) {
			Ext.getCmp(container).add(editor);
		} else {
			AppKit.util.Layout.getCenter().add(AppKit.userEditor.editorWidget.instance)
			AppKit.util.Layout.doLayout();
		}
	}
	/*
	 * Build the form if not done yet
	 */
	if(!AppKit.userEditor.editorWidget)
		initEditorWidget();

	<?php if(!$t["container"]) { ?>
			AppKit.userEditor.editorWidget.instance.insertPresets(<?php echo $user->get("user_id") ?>);				
 	<?php }?>

})
</script>
