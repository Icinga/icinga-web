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
		},
		
		applyXTemplate : function(grid, index, string) {
			var data = grid.getStore().getAt(index).data;
			var tpl = new Ext.XTemplate(string);
			return tpl.apply(data);
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
			if(!value) 
				return "";
			// skip truncate if html is located at the ouput
			if(value.match(/<.*?>(.*?)<\/.*?>/g))
				return value;

			var out = Ext.util.Format.ellipsis(value, (cfg.length || 50));
			if (out.indexOf('...', (out.length-3)) != -1) {
				// @todo Check if html encoding brings some trouble
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
				var imgName = new Ext.XTemplate(my.image).apply(record.data);
				return String.format('<img src="{0}/{1}"{2} />', AppKit.c.path, imgName, (flat_attr && " " + flat_attr + " "));
			}
		}
	},

	regExpReplace: function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			var exp = new RegExp(cfg.expression);
			return value.replace(exp,cfg.replacement);

		}
	},
	
	serviceStatus : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
		
			if(Ext.isDefined(record.json.service_is_pending)) {
				if(record.json.service_is_pending == 1)
					value=99;
			}
			if(!Ext.isDefined(value))
				return "";
			return Icinga.StatusData.wrapElement('service', value);
		}
	},
	
	hostStatus : function(cfg) {
		return function(value, metaData, record, rowIndex, colIndex, store) {
			if(Ext.isDefined(record.json.host_is_pending)) {
				if(record.json.host_is_pending == 1)
					value=99;
			}
			if(!Ext.isDefined(value))
				return "";
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
