Ext.ns('Ext.ux');
Ext.ux.AutoComboBox = Ext.extend(Ext.form.ComboBox, {
	
	minChars : 0,
	
	height : 30,
	
	pageSize : 20,
	
	
	constructor : function(cfg) {
		cfg = cfg || {};
		cfg.storeCfg = cfg.storeCfg || {};
			
		Ext.applyIf(cfg, {
            triggerAction : 'all',
            
            listEmptyText : _('No results...'),
            editable : true
            //tpl : '<tpl for="."><div ext:qtip="{{0}}" class="x-combo-list-item">{{0}}</div></tpl>'.format(cfg.name), 
		});
	
        
	
	    
		Ext.form.ComboBox.prototype.constructor.call(this, cfg);
		
		this.store.on({
			beforeload : function(store, options) {
				var value = options.params[this.valueField] || store.baseParams[this.valueField];
				
				if(value) {
					if(value.charAt(0) != '%') {
						value = '%' + value;
					}
					if(value.charAt(value.length-1) != '%') {
						value += '%';
					}
					store.setBaseParam(this.valueField, value);
				} else {
					store.setBaseParam(this.valueField, '%');
				}
			},
			scope : this
		});
	},
	
	onRender : function() {
		Ext.ux.AutoComboBox.superclass.onRender.apply(this, arguments);
		
		this.el.on({
			click : function() {
				this.selectText();
				this.getStore().reload();
			},
			scope : this
		});
	}
	
});

Ext.reg('autocombo', Ext.ux.AutoComboBox);

