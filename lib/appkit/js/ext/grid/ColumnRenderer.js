Ext.ns('AppKit.Ext.grid');

/**
 * Util singleton class for use with other renderers
 */
AppKit.Ext.grid.ColumnRendererUtil = function() {
	var pub = {
		
		metaDataObject : function(o) {
			var meta = {};
			var attributes = AppKit.Ext.util.StructUtil.extractParts(o, ['attr', 'cellAttr']);
			meta.attr = AppKit.Ext.util.StructUtil.attributeString(attributes.attr || {});
			meta.cellAttr = AppKit.Ext.util.StructUtil.attributeString(attributes.cellAttr || {});
			Ext.applyIf(meta, o);
			
			return meta;
		}
		
	}
	
	return pub;
}();

/**
 * Default column renderes
 */
AppKit.Ext.grid.ColumnRenderer = {
	
	bogusGroupRenderer : function(cfg) {
		return function(value, garbage, record, rowIndex, colIndex, store) {
			return "GROUP: " + v;
		}
	},
	
	truncateText : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			var out = Ext.util.Format.ellipsis(value, (cfg.length || 50));
			if (out.indexOf('...', (out.length-3)) != -1) {
				metaData.attr = 'ext:qtip="' + value + '"';
			}
			
			return out;
		}
	},
	
	columnElement : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			var my = cfg;	// local reference
			
			Ext.apply(metaData, AppKit.Ext.grid.ColumnRendererUtil.metaDataObject(my));
			
			if (("value" in my)) {
				return my.value;
			}
			else if (!("noValue" in my) && my.noValue != true) {
				return value;
			}
			else {
				return "";
			}
		}
	},
	
	columnImage : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			var my = cfg;	// local reference
			Ext.apply(metaData, AppKit.Ext.grid.ColumnRendererUtil.metaDataObject(my));
			
			var flat_attr = metaData.attr;
			delete metaData.attr;
			
			if (!('image' in my) || !my["image"]) {
				return '[no image defined (attr=image)]';
			}
			else {
				return String.format('<img src="/appkit/image/{0}"{1} />', my.image, (flat_attr && " " + flat_attr + " "));
			}
		}
	},
	
	
};
