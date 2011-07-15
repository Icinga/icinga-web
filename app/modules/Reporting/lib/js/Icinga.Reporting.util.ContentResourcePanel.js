Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.ContentResourcePanel = Ext.extend(Ext.Panel, {
	
	title : _('Content resource'),
	
	constructor : function(config) {
		
		config = Ext.apply(config || {}, {
			tbar : [{
				text : _('Save to disk'),
				iconCls : 'icinga-icon-disk',
				handler : this.processDownload,
				scope : this
			}, {
				text : _('Preview'),
				iconCls : 'icinga-icon-eye',
				handler : this.processPreview,
				scope : this
			}]
		});
		
		Icinga.Reporting.util.ContentResourcePanel.superclass.constructor.call(this, config);
	},
	
	initComponent : function() {
		Icinga.Reporting.util.ContentResourcePanel.superclass.initComponent.call(this);
		this.setToolbarEnabled(false);
	},
	
	setToolbarEnabled : function(bool) {
		if (Ext.isEmpty(bool)) {
			bool = true;
		}
		
		this.getTopToolbar().items.eachKey(function(key, item) {
			item.setDisabled(!bool);
		});
	},
	
	processNodeClick : function(node, e) {
		AppKit.log(node);
	},
	
	processDownload : function(b, e) {
		
	},
	
	processPreview : function(b, e) {
		
	}
});