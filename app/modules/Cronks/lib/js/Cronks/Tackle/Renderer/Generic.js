Ext.ns('Icinga.Cronks.Tackle.Renderer');

Icinga.Cronks.Tackle.Renderer.Generic = {
	showIconCls : function(value, metaData, record, rowIndex, colIndex, store, cfg) {
		
		if (Ext.isEmpty(cfg.iconCls) === true) {
			throw("cfg.iconCls not set!");
		}
		
		metaData.css += ' ' + cfg.iconCls;
		if (cfg.qtip) {
			metaData.attr += 'ext:qtip="' + cfg.qtip + '"';
		}
	}
}