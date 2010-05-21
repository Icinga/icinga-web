Ext.ns('Cronk.grid');

/**
 * Util singleton class for use with other renderers
 */
Cronk.grid.ColumnRendererUtil = function() {
	var pub = {
		metaDataObject : function(o) {
			var meta = {};
			var attributes = Cronk.util.StructUtil.extractParts(o, ['attr', 'cellAttr']);
			meta.attr = Cronk.util.StructUtil.attributeString(attributes.attr || {});
			meta.cellAttr = Cronk.util.StructUtil.attributeString(attributes.cellAttr || {});
			Ext.applyIf(meta, o);
			
			return meta;
		}
	}
	
	return pub;
}();

/**
 * Default column renderes
 */
Cronk.grid.ColumnRenderer = {
	
	bogusGroupRenderer : function(cfg) {
		return function(value, garbage, record, rowIndex, colIndex, store) {
			return "GROUP: " + v;
		}
	},
	
	nullDisplay : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			
			if (value == undefined) {
				metaData.css = 'x-icinga-grid-data-null';
				return '(null)';
			}
			
			return value;
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
			
			Ext.apply(metaData, Cronk.grid.ColumnRendererUtil.metaDataObject(my));
			
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
			Ext.apply(metaData, Cronk.grid.ColumnRendererUtil.metaDataObject(my));
			
			var flat_attr = metaData.attr;
			delete metaData.attr;
			
			if (!('image' in my) || !my["image"]) {
				return '[no image defined (attr=image)]';
			}
			else {
				return String.format('<img src="{0}/{1}"{1} />', AppKit.c.path, my.image, (flat_attr && " " + flat_attr + " "));
			}
		}
	},
	
	serviceStatus : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			return Icinga.StatusData.wrapElement('service', value);
		}
	},
	
	hostStatus : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			return Icinga.StatusData.wrapElement('host', value);
		}
	},
	
	switchStatus : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			var my = cfg;
			var type="host";
			if ('serviceField' in my && record.data[ my.serviceField ]) {
				type="service";
			}
			return Icinga.StatusData.wrapElement(type, value);
		}
	}
	
};
