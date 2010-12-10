
Cronk.util.CronkBuilder = function(config) {
	Cronk.util.CronkBuilder.superclass.constructor.call(this, config);
}

Ext.extend(Cronk.util.CronkBuilder, Ext.Window, {
	title: _('Save custom Cronk'),
	modal: true,
	width: 650,
	height: 400,
	closeAction: 'hide',
	
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
        	}
		});
	},
	
	_buildForm: function() {
		return new Ext.form.FormPanel({
			layout: 'border',
			height: 200,
			
			defaults: {
				border: false
			},
			
			items: [{
				padding: '5px',
		        layout: 'form',
		        region: 'center',
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
		        	fieldLabel: _('Cronk Id')
		        }]
		    }, {
		    	region: 'east',
		    	width: 350,
		    	padding: '5px',
		    	layout: 'form',
		    	
		    	items: [
		    		this._iconCombo(),
		    		this.paramGrid
		    	]
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
			form.findField('cid').setValue(this.cronk.cmpid + '-' + this.cronk.crname);
		}
	}
});

// ...