
Cronk.util.CronkBuilder = function(config) {
	Cronk.util.CronkBuilder.superclass.constructor.call(this, config);
}

Ext.extend(Cronk.util.CronkBuilder, Ext.Window, {
	title: _('Save custom Cronk'),
	modal: true,
	height: 400,
	closeAction: 'hide',
	width: 740,
	
	initComponent : function() {
		
		this._buildBars();
		
		Cronk.util.CronkBuilder.superclass.initComponent.call(this);
		
		this.paramGrid = this._paramGrid();
		
		this.formPanel = this._buildForm();
		
		// Lazy
		this.addListener('beforerender', function(c) {
			this.add(this.formPanel);
		}, this, { single: true });
	},
	
	_buildBars : function() {
		
		var CB = this;
		
		this.bbar = [{
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
    		name: 'image',
    		fieldLabel: _('Image')
    	});
	},
	
	_paramGrid : function() {
		return new Ext.grid.PropertyGrid({
			autoHeight: true,
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
							});
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
			baseParams: { all : 1 },
			writer: new Ext.data.JsonWriter({
			    encode: true,
			    writeAllFields: false
			})
		});
		
		return new Ext.form.FormPanel({
			layout: 'border',
			height: 400,
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
			        },
			        
			        items: [{
			        	xtype: 'textfield',
			        	name: 'name',
			        	fieldLabel: _('Name'),
			        	value: 'LLL'
			        }, {
			        	xtype: 'textfield',
			        	name: 'description',
			        	fieldLabel: _('Description')
			        }, {
			        	xtype: 'textfield',
			        	name: 'cid',
			        	fieldLabel: _('Cronk Id'),
			        	readOnly: true
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
		        		fieldLabel: _('Available'),
		        		width: 200,
		        		store: this.categories,
		        		valueField: 'title',
		        		displayField: 'title',
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
		        }]
		    }, {
		    	region: 'east',
		    	width: 350,
		    	padding: '5px',
		    	layout: 'form',
		    	
		    	items: [{
		    		xtype: 'fieldset',
		    		title: _('Image'),
		    		items: this._iconCombo()
		    	}, {
		    		xtype: 'fieldset',
		    		title: _('Parameters'),
					defaults: { border: false },
		    		items: [{
		    			items: this.paramGrid
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
			
			this.paramGrid.setSource(cronk.params || {});
			
			var form = this.formPanel.getForm();
			
			form.findField('name').setValue(this.cronkCmp.title);
			form.findField('cid').setValue(Ext.id(null, 'CUSTOM-' + this.cronk.crname));
		}
	}
});

// ...