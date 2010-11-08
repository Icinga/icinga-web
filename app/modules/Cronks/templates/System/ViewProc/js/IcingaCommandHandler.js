
// ---
// KEEP THIS LINE
// ---

var IcingaCommandHandler = function(meta) {
	
	this.toolbaritem = undefined;
	
	this.meta = undefined;
	
	this.command_options = {};
	
	this.url_info = undefined;
	this.url_send = undefined;
	
	this.grid = undefined;	
	this.constructor.call(this, meta);
};

IcingaCommandHandler.prototype = {
	
	constructor : function(meta) {
		this.meta = meta;
		
		this.command_options = this.meta.template.option.commands;
	},
	
	setToolbarEntry : function (tb) {
		this.toolbaritem = tb;
	},
	
	setInfoUrl : function(url) {
		this.url_info = url;
	},
	
	setSendUrl : function(url) {
		this.url_send = url;
	},
	
	setGrid : function(grid) {
		this.grid = grid;
	},
	
	enhanceToolbar : function() {
		
		Ext.iterate(this.command_options.items, function(k,v) {
			var b = this.toolbaritem.menu.add({
				text: v.title,
				iconCls: v.icon_class || 'icinga-icon-bricks'
			});
			
			if (v.seperator && v.seperator === true) {
				this.toolbaritem.menu.add('-');
			}
			
			b.on('click', function(b, e) { this.showCommandWindow(k, v.title) }, this);
			
		}, this);
		
		
		
	},
	
	validSelection : function() {
		return this.grid.getSelectionModel().hasSelection();
	},
	
	getField : function(o) {
		
		oDef = {
			fieldLabel: o.fieldLabel,
			name: o.fieldName,
			value: o.fieldValue,
			width: 200,
			allowBlank: (o.fieldRequired == true) ? false : true
		}
		
		switch (o.fieldType) {
			
			case 'notification_options':

				delete oDef['name'];
			
				Ext.apply(oDef, {
					store: new Ext.data.ArrayStore({
						idIndex: 0,
						fields: ['fId', 'fStatus', 'fLabel'],
						data: [
							['1', '0', _('(default) no option')],
							['2', '1', _('Broadcast')],
							['3', '2', _('Forced')],
							['4', '4', _('Increment current notification')]
						]
					}),
					
					'name': '__return_value_combo',
					
					mode: 'local',
					typeAhead: true,
					triggerAction: 'all',
					forceSelection: true,
					
					
					fieldLabel: _('Option'),
					
					valueField: 'fStatus',
					displayField: 'fLabel',
					
					hiddenName: o.fieldName
				});
					
				return new Ext.form.ComboBox(oDef);

			break;
			
			case 'return_code':
			
				delete oDef['name'];
			
				Ext.apply(oDef, {
					store: new Ext.data.ArrayStore({
						idIndex: 0,
						fields: ['fId', 'fStatus', 'fLabel'],
						data: [
							['1', '0', _('OK')],
							['2', '1', _('Warning')],
							['3', '2', _('Critical')],
							['4', '3', _('Unknown')],
							['5', '255', _('Return code out of bounds')]
						]
					}),
					
					'name': '__return_value_combo',
					width:300,
					mode: 'local',
					typeAhead: true,
					triggerAction: 'all',
					forceSelection: true,
					
					
					fieldLabel: _("Status"),
					
					valueField: 'fStatus',
					displayField: 'fLabel',
					
					hiddenName: o.fieldName
				});
					
				return new Ext.form.ComboBox(oDef);
			
			break;
			case  'hidden':
				return new Ext.form.Hidden(oDef);
			break;
			case 'date':
				oDef.format = 'Y-m-d H:i:s';
				oDef.value = new Date();
				return new Ext.form.DateField(oDef);
			break;
			
			case 'ro':
				oDef.readOnly = true;
				return new Ext.form.Field(oDef);
			break;
			
			case 'checkbox':
				Ext.apply(oDef, {
					name: o.FieldName + '-group',
					columns: 2,
					items: [
						{boxLabel: _('Yes'), inputValue: 1, name: o.fieldName},
						{boxLabel: _('No'), inputValue: 0, name: o.fieldName, checked: true}
					]
				});
				
				return new Ext.form.RadioGroup(oDef);
				
			break;
			
			case 'textarea':
				oDef.height = 120;
				return new Ext.form.TextArea(oDef);
			break;
			
			case 'text':
				return new Ext.form.TextField(oDef);
			break;
			
			default:
				oDef.value =  '(' + String.format(_('Unknown field type: {0}'), o.fieldType) + ')';
				return new Ext.form.DisplayField(oDef);
			
			break;
		}
	},
	
	getSelection : function() {
		
		var r = [];
		
		Ext.each(this.grid.getSelectionModel().getSelections(), function(item, index, arry) {
			var td = {};
			
			for (var skey in this.command_options.source) {
				
				if (item.data[ this.command_options.source[skey] ]) {
					td[ skey ] = item.data[ this.command_options.source[skey] ]
				}
				
			}
			
			r.push( td );
			
		}, this);
		
		return r;
	},
	
	showCommandWindow : function(command, title) {
		
		if (this.validSelection() !== true) {
			AppKit.notifyMessage(_('Command'), _('Selection is missing'));
			return;
		}
		
		
		
		
		Ext.Ajax.request({
			url: String.format(this.url_info, command),
			scope: this,
			
			success: function(response, opts) {
				
				var o = Ext.decode(response.responseText);
				
				var oWin = new Ext.Window({
					title: String.format(_('{0} ({1} items)'), title, this.grid.getSelectionModel().getCount()),
					autoDestroy: true,
					autoHeight: true,
					closable: true,
					modal: true,
					defaultType: 'field',
					
					bbar: [{
						text: _('OK'),
						iconCls: 'icinga-icon-accept',
						handler: function(b, e) {
							oForm.getForm().doAction(oFormAction);
						}
					}, {
						text: _('Abort'),
						iconCls: 'icinga-icon-cross',
						handler: function(b, e) { oWin.close(); }
					}]
				});
				
				// This fixes the webkit (safari, chrome) width issue ...
				oWin.on('afterrender', function() {
					this.syncSize();
					this.syncShadow();
				}, oWin, { delay: 40, single: true });
				
				var oForm = new Ext.form.FormPanel({
					border: false,
					bodyStyle: 'padding: 5px 5px 5px 5px',
					
					defaults: {
						border: false,
						msgTarget: 'side'
					}
				});
				
				oForm.getForm().on('beforeaction', function(f, a) {
					if(!f.isValid())
						return false;
					var selection = Ext.util.JSON.encode( this.getSelection() );
					
					// Auth for the command, key is the timekey given
					// by the json request (Timekey is valid for 5 minutes)
					// The complete key is valid for the command and the selection
					var h_data = command + '-' + selection;
					h_data += '-' + Ext.util.JSON.encode( f.getValues(false) );
					// simplify string
					h_data = h_data.replace(/[ẃéŕźúíóṕǘáśǵḱĺýćǘńḿèàẁùìòàỳǜǹȩŗźíóṕáşḑģḩķĺýçńḿÈẀÙÌÒÀỲǛǸẂÉŔŹÚÍÓṔÚÜÁŚǴḰĹÝĆǗǸḾȨŖŢŞḐĢḨĶĻÝÇŅ]/g,"");

					var h_key = o.tk;
					var h_auth = hex_hmac_rmd160(h_key, h_data);
					
					a.options.params['auth'] = h_auth;
					a.options.params['selection'] = selection;
					
					oWin.disable();

					return true;
					
				}, this);
				
				var oFormAction = new Ext.form.Action.JSONSubmit(oForm.getForm(), {
					clientValidation: true,
					
					url: String.format(this.url_send, command),
					
					// The name of the json store
					json_namespace: 'data',
					
					params: {},
					
					failure: function(f, a) {
						if (a.failureType != Ext.form.Action.CLIENT_INVALID) {
							var e = Ext.util.JSON.decode(a.response.responseText);
							var error = e.errors['default'];

							oWin.close();

							AppKit.notifyMessage(_('Error sending command'), _('Could not send the command, please examine the logs!'));

							Ext.Msg.show({
							   title:_('Error sending command'),
							   msg: error,
							   buttons: Ext.MessageBox.OK,
							   icon: Ext.MessageBox.ERROR
							});
						}
					},
					
					success: function(f, a) {
						oWin.close();
						AppKit.notifyMessage(_('Command sent'), String.format(_('{0} command was sent successfully!'), command));
					}
				});
				
				var bAdd = false;
				
				Ext.each(o.fields, function(item, index, arry) {
					
					if (this.command_options.source[item]) return;
					
					var f = this.getField({
						fieldLabel: item,
						fieldName: item,
						fieldType: o.types[item].type,
						fieldValue: this.command_options.predefined[item] || '',
						fieldRequired: o.types[item].required || false
					});
					
					if (f) {
						bAdd = true; 
						oForm.add(f);
					}
					
				}, this);
				
				if (bAdd === false) {
					oForm.add({
						xtype: 'label',
						text: _('This command will be send to all selected items')
					});
				}
				
				oWin.add(oForm);
				
				oWin.render(Ext.getBody());
				oWin.show();
				
				
				oWin.doLayout();
				oWin.setWidth( oWin.getWidth() + 50 );
				oWin.setHeight( oWin.getHeight() + 5 );
				oWin.doLayout();
			}
		});
	}
	
}

