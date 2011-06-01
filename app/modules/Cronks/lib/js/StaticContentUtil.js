Ext.ns('Cronk.util');

Cronk.util.StaticContentUtil = {
	
	convertToLink : function(ele, image_class) {
		ele.addClass([image_class, 'icinga-image-link']);
	},
	
	drilldownLink : function(c) {
		
		var to = Ext.getCmp(c.cmpid);
		
		if (Ext.get(c.jsid) && to) {
			var link = Ext.get(c.jsid);
			Cronk.util.StaticContentUtil.convertToLink(link, 'icinga-icon-drilldown');
			
			var updater = to.getUpdater();
			link.on('click', function(e) {
				var u = updater.defaultUrl.split('?', 2);
				
				try {
					var oUrl = Ext.urlDecode(u[1]);
				} catch(e) {
					var oUrl = {}
				}
				
				if (Ext.isEmpty(oUrl['p[filter_appendix]'])) {
					oUrl['p[filter_appendix]'] = "";
					var ary = [];
				} else {
					var ary = oUrl['p[filter_appendix]'].split('|');
				}
				
				var x = new Ext.XTemplate(c['filter_value']);
				var filter_value = x.apply(c['filter_object']);
				
				ary.push(String.format('{0},{1}', c['filter_field'], filter_value.toUpperCase()));
				
				oUrl['p[filter_appendix]'] = ary.join('|');
				
				oUrl['p[chain]'] = c.chainid += 1;
				
				updater.defaultUrl = Ext.urlAppend(u[0], Ext.urlEncode(oUrl));
				
				updater.refresh();
			}) 
		}
	},
	
	drillupLink: function(c) {
		var to = Ext.getCmp(c.cmpid);
		
		if (Ext.get(c.jsid) && to) {
			var link = Ext.get(c.jsid);
			
			Cronk.util.StaticContentUtil.convertToLink(link, 'icinga-icon-drillup');
			
			var updater = to.getUpdater();
			link.on('click', function(e) {
				var u = updater.defaultUrl.split('?', 2);
				
				try {
					var oUrl = Ext.urlDecode(u[1]);
				} catch(e) {
					var oUrl = {}
				}
				
				if (Ext.isEmpty(oUrl['p[filter_appendix]'])) {
					oUrl['p[filter_appendix]'] = "";
					var ary = [];
				} else {
					var ary = oUrl['p[filter_appendix]'].split('|');
				}
				
				if (ary.length == 0) {
					throw "drillupLink: need filter_appendix set!";
				}
				
				ary.pop();
				
				oUrl['p[filter_appendix]'] = ary.join('|');
				
				oUrl['p[chain]'] = c.chainid -= 1;
				
				updater.defaultUrl = Ext.urlAppend(u[0], Ext.urlEncode(oUrl));
				
				updater.refresh();
				
			});
		}
	}
}
	
