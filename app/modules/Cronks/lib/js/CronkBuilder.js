
Ext.ns('Cronk.util.form.action');

Cronk.util.CronkBuilder = function(config) {
	Cronk.util.CronkBuilder.superclass.constructor.call(this, config);
}

Cronk.util.form.action.CronkBuilderCustom = Ext.extend(Ext.form.Action.Submit, {
	
	constructor : function(form, options, propertyGrid) {
		Cronk.util.form.action.CronkBuilderCustom.superclass.constructor.call(this, form, options);
		
		this.propertyGrid = propertyGrid;
	},
	
	getParams : function() {
		var buff = Cronk.util.form.action.CronkBuilderCustom.superclass.getParams.call(this);
		
		var np = {};
		
		var data = this.propertyGrid.getSource();
		
		Ext.iterate(data, function(k, v) {
			np['p[' + k + ']='] = v;
		});
		
		if (!Ext.isEmpty(buff)) {
			buff += '&';
		}
		
		buff += Ext.urlEncode(np);
		
		return buff;
	}
});

Ext.extend(Cronk.util.CronkBuilder, Ext.Window, {
	title: _('Save custom Cronk'),
	modal: true,
	height: 650,
	closeAction: 'hide',
	width: 800,
	
	initComponent : function() {
		
		this._buildBars();
		
		Cronk.util.CronkBuilder.superclass.initComponent.call(this);
		
		this.paramGrid = this._paramGrid();
		
		this.formPanel = this._buildForm();
		
		this.action = new Cronk.util.form.action.CronkBuilderCustom(this.formPanel.getForm(), {
			params: { xaction: 'write' },
			url: AppKit.c.path + '/cronks/provider/categories'
		}, this.paramGrid);
		
		// Lazy
		this.addListener('beforerender', function(c) {
			this.add(this.formPanel);
		}, this, { single: true });
		
	},
	
	_buildBars : function() {
		
		var CB = this;
		
		this.bbar = [{
			text: _('Save'),
			iconCls: 'icinga-icon-star-plus',
			handler: function(b, e) {
				this.formPanel.getForm().doAction(this.action);
			},
			scope: this
		}, {
			text: _('Close'),
			iconCls: 'icinga-icon-cross',
			handler: function(b, e) {
				CB.hide();
			}
		}];
	},
	
	_iconCombo: function() {
		var iconStore = new Ext.data.JsonStore({
			autoDestroy: true,
			url: AppKit.c.path + '/appkit/provider/icons',
			baseParams: { path: 'cronks' },
			fields: ['web_path', 'name', 'short'],
			root: 'rows'
		});
		
		iconStore.load();
		
	    var iconTpl = new Ext.XTemplate(
	        '<tpl for="."><div class="x-icinga-icon-search-item" style="background-image: url({web_path});">',
	            '<span>{name}</span>',
	        '</div></tpl>'
	    );
	    
	    return new Ext.form.ComboBox({
    		xtype: 'combo',
    		store: iconStore,
    		displayField: 'name',
    		typeAhead: false,
    		loadingText: _('Searching ...'),
    		hideTrigger: false,
    		tpl: iconTpl,
    		itemSelector: 'div.x-icinga-icon-search-item',
    		triggerAction: 'all',
    		valueField: 'short',
    		width: 200,
    		height: 40,
    		allowBlank: false,
    		name: 'image',
    		fieldLabel: _('Image')
    	});
	},
	
	_paramGrid : function() {
		return new Ext.grid.PropertyGrid({
			id: 'cronkbuilder-param-properties',
			height: 250,
			viewConfig : {
            	forceFit: true,
            	scrollOffset: 2
        	},
        	bbar: [{
        		iconCls: 'icinga-icon-add',
        		text: _('Add'),
        		handler: function(b, e) {
        			Ext.MessageBox.prompt(_('Add'), _('Add new parameter to properties'), function(btn, text) {
	    				if (!Ext.isEmpty(text)) {
							var rec = new Ext.grid.PropertyRecord({
							    name: text,
							    value: null
							}, text);
							this.paramGrid.store.addSorted(rec);
						}
        			}, this);
        		},
        		scope: this
        	}, {
				iconCls: 'icinga-icon-delete',
				text: _('Remove'),
				handler: function(b, e) {
					var sel = this.paramGrid.getSelectionModel().selection;
					try {
						this.paramGrid.removeProperty(sel.record.id);
					} catch (e) {
						AppKit.notifyMessage(_('Error'), _('No selection was made!'));
					}
				},
				scope: this
			}]
		});
	},
	
	_buildForm: function() {
		
		this.categories = new Ext.data.JsonStore({
			autoDestroy: true,
			url: AppKit.c.path + '/cronks/provider/categories',
			baseParams: { all : 1 },
			writer: new Ext.data.JsonWriter({
			    encode: true,
			    writeAllFields: false
			})
		});
		
		this.categories.load();
		
		this.groups = new Ext.data.JsonStore({
			autoDestroy: true,
			url: AppKit.c.path + '/appkit/provider/groups',
			baseParams: { addMeta : 1 },
			writer: new Ext.data.JsonWriter({
			    encode: true,
			    writeAllFields: false
			})
		});
		
		this.groups.load();
		
		return new Ext.form.FormPanel({
			layout: 'border',
			height: 650,
			padding: '5px 0 5px 0',
			
			defaults: {
				border: false
			},
			
			items: [{
				padding: '5px',
		        layout: 'form',
		        region: 'center',
		        height: 400,
		        items: [{
		        	xtype: 'fieldset',
		        	title: _('Meta'),
		        	
	 				defaults: {
			        	width: 220,
			        	msgTarget: 'side'
			        },
			        
			        items: [{
			        	xtype: 'textfield',
			        	name: 'name',
			        	fieldLabel: _('Name'),
			        	allowBlank: false
			        }, {
			        	xtype: 'textfield',
			        	name: 'description',
			        	fieldLabel: _('Description'),
			        	allowBlank: false
			        }, {
			        	xtype: 'textfield',
			        	name: 'cid',
			        	fieldLabel: _('Cronk Id'),
			        	readOnly: true,
			        	allowBlank: false,
			        }, {
			        	xtype: 'checkbox',
			        	name: 'hide',
			        	fieldLabel: _('Hidden')
			        }]
		        }, {
		        	xtype: 'fieldset',
		        	title: _('Categories'),
		        	height: 180,
		        	items: [{
		        		xtype: 'multiselect',
		        		name: 'categories',
		        		fieldLabel: _('All categories available'),
		        		allowBlank: false,
		        		width: 200,
		        		store: this.categories,
		        		valueField: 'title',
		        		displayField: 'title',
		        		msgTarget: 'side',
		        		tbar: [{
		        			text: _('Add'),
		        			iconCls: 'icinga-icon-add',
		        			handler: function(b, e) {
		        				var c = this.categories;
		        				
		        				Ext.MessageBox.prompt(_('Add'), _('Add new category'), function(btn, text) {
				    				if (!Ext.isEmpty(text)) {
										var r = new c.recordType({
											title: text,
											visible: true,
											position: 0,
											active: false
										});
										
										c.add(r);
									}
			        			}, this);
		        			},
		        			scope: this
		        		}]
		        	}]
		        }, {
		        	xtype: 'fieldset',
		        	title: _('Share your Cronk'),
		        	items: [{
		        		xtype: 'checkbox',
		        		name: 'share',
		        		fieldLabel: 'Make your cronk available for others',
		        		msgTarget: 'side',
		        		handler: function(c, checked) {
		        			var field = this.formPanel.getForm().findField('role');
		        			
		        			if (checked == true) {
		        				field.enable();
		        			}
		        			else {
		        				field.disable();
		        			}
		        		},
		        		scope: this
		        	}, {
		        		xtype: 'multiselect',
		        		name: 'role',
		        		width: 200,
		        		fieldLabel: _('Principals'),
		        		store: this.groups,
		        		valueField: 'role_id',
		        		displayField: 'role_name',
		        		disabled: true,
		        		msgTarget: 'side'
		        	}]
		        }]
		    }, {
		    	region: 'east',
		    	width: 390,
		    	padding: '5px',
		    	layout: 'form',
		    	
		    	items: [{
		    		xtype: 'fieldset',
		    		title: _('Image'),
		    		defaults: { msgTarget: 'side' },
		    		items: this._iconCombo()
		    	}, {
		    		xtype: 'fieldset',
		    		title: _('Parameters'),
					defaults: { 
						border: false,
						msgTarget: 'side'
					},
		    		items: [{
		    			items: this.paramGrid
		    		}]
		    	}, {
		    		xtype: 'fieldset',
		    		title: _('Agavi setting'),
		    		defaults: {
			        	width: 220,
			        	msgTarget: 'side'
			        },
		    		items: [{
		    			xtype:'textfield',
		    			fieldLabel: _('Module'),
		    			name: 'module',
		    			value: 'Cronks',
		    			allowBlank: false
		    		}, {
		    			xtype:'textfield',
		    			fieldLabel: _('Action'),
		    			name: 'action',
		    			allowBlank: false
		    		}]
		    		
		    	}]
		    }]
		});
	},
	
	setCurrentCronkId : function(id) {
		cronk = Cronk.Registry.get(id);
		
		if (cronk) {
			this.cronkId = id;
			this.cronk = cronk;
			this.cronkCmp = Ext.getCmp(id);
			
			var params = Ext.apply({}, cronk.params);
			
			delete(params['action']);
			delete(params['module']);
			
			this.paramGrid.setSource(params || {});
			
			var form = this.formPanel.getForm();
			
			// AppKit.log(this.cronkCmp, this.cronk);
			
			form.findField('name').setValue(this.cronkCmp.title);
			form.findField('cid').setValue(Ext.id(null, 'CUSTOM-' + this.cronk.crname));
			form.findField('module').setValue(this.cronk.params.module);
			form.findField('action').setValue(this.cronk.params.action);
		}
	}
});

// ...