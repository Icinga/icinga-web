Ext.ns('AppKit.Ext.util');

AppKit.Ext.util.StructUtil = function(){
	
	var pub = {

		extractParts : function(o, list) {
			var intersect = {};
			
			Ext.each(list, function(item, index, arry) {
				if (item in o) {
					intersect[item] = o[item];
				}
				else if (!(item in intersect)) {
					intersect[item] = {};
				}
			});
			
			return intersect;			
		},
		
		attributeString : function(o) {
			var p = [];
			Ext.iterate(o, function(k,v) {
				p.push(String.format('{0}="{1}"', k, v));
			});
			return p.join(' ');
		}
		
	};
	
	return pub;
	
}();