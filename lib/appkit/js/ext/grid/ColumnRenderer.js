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
		},
		
		statusMap : function(type) {
			
			var o = {
				baseCls: 'icinga-status'
			}
			
			if (type == "host") {
				o["mapCls"] = {
					0: 'icinga-status-up',
					1: 'icinga-status-down',
					2: 'icinga-status-unreachable'
				};
				
				o["mapLabel"] = {
					0: 'UP',
					1: 'DOWN',
					2: 'UNREACHABLE'
				};
			}
			else if (type == "service") {
				o["mapCls"] = {
					0: 'icinga-status-ok',
					1: 'icinga-status-warning',
					2: 'icinga-status-critical',
					3: 'icinga-status-unknown'
				};
				
				o["mapLabel"] = {
					0: 'OK',
					1: 'WARNING',
					2: 'CRITICAL',
					3: 'UNKNOWN'
				};
			}
			
			return o;
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
				return String.format('<img src="appkit/image/{0}"{1} />', my.image, (flat_attr && " " + flat_attr + " "));
			}
		}
	},
	
	statusMapWrapper : function(type, value) {
		var map = AppKit.Ext.grid.ColumnRendererUtil.statusMap(type);
		return String.format('<div class="{0} {1}"><span>{2}</span></div>', map.baseCls, map.mapCls[value], map.mapLabel[value]);
	},
	
	serviceStatus : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			return AppKit.Ext.grid.ColumnRenderer.statusMapWrapper('service', value);
		}
	},
	
	hostStatus : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			return AppKit.Ext.grid.ColumnRenderer.statusMapWrapper('host', value);
		}
	},
	
	switchStatus : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			var my = cfg;
			var type="host";
			if ('serviceField' in my && record.data[ my.serviceField ]) {
				type="service";
			}
			return AppKit.Ext.grid.ColumnRenderer.statusMapWrapper(type, value);
		}
	}
	
};
