/**
 * simple data provider - display object
 * Date: 2009-09-17
 * Author: Christian Doebler <christian.doebler@netways.de>
 */

Ext.ns('Icinga.util');

Icinga.util.SimpleDataProvider = (function () {

		var toolTip = null;
	
		var pub = {};
	
		var config = {
			url:			false,
			srcId:			false,
			width:			false,
			filter:			false,
			anchor:			false,
			closable:		false,
			target:			false,
			delay:			false,
			autoDisplay:	true,
			title:			false
		};

		pub.reset = function () {
			config.url = AppKit.c.path + "/web/simpleDataProvider/json?src_id=";
			config.srcId = "";
			config.width = 200;
			config.filter = {};
			config.delay = 15000;
			config.autoDisplay = true;
			
			config.target = false;
			config.closable = true;
			config.anchor = 'left';
			config.title = '';
			
			return config;
		};

		pub.getFilter = function () {
			var filter = "";
			if (config.filter !== false) {
				var filterDef = config.filter;
				var filterCount = filterDef.length;

				
				for (var x = 0; x < filterCount; x++) {
					filter += "&filter[" + filterDef[x].key + "]=" + filterDef[x].value;
				}
			}
			return filter;
		};

		pub.getUrl = function () {
			var url = config.url + config.srcId + pub.getFilter();
			return url;
		};

		pub.checkData = function () {
			var dataOk = false;
			if (config.url !== false && config.srcId !== false && config.target !== false) {
				dataOk = true;
			}
			return dataOk;
		};

		pub.setConfig = function (c) {
			for (var key in c) {
				if (c[key] != undefined) {
					config[key] = c[key];
				}
			}
		};

		pub.display = function () {
			
			if (pub.checkData()) {
				
				if (!Ext.isEmpty(toolTip)) {
					toolTip.destroy();
				}
				
				toolTip = new Ext.ToolTip({
					width: config.width,
					dismissDelay: config.delay,
					closable: config.closable,
					anchor: config.anchor,
					target: config.target,
					dismissDelay: 5000,
					hideDelay: 1000,
					draggable: true,
					autoHide: true,
					title: (!Ext.isEmpty(config.title)) ? _(config.title) : '' 
				});
				
				toolTip.render(Ext.getBody());
				
				toolTip.getEl().on('mouseover', function(e) {
					// set the tooltip as the new target and clear
					// the autoHide timer.
					toolTip.initTarget(toolTip.getEl());
					toolTip.showAt([]); 
				});
				
				toolTip.getUpdater().update({
					url: pub.getUrl(),
					callback: pub.outputTable,
					scope: Icinga.util.SimpleDataProvider 
				});
				
				toolTip.on('hide', function(tt) {
					tt.destroy();
				});
				
//				toolTip.getEl().on('click', function() {
//					toolTip.hide();
//				})
				
				toolTip.show();
				
			}
		};

		pub.outputTable = function (el, success, response, options) {
			var responseObj = Ext.util.JSON.decode(response.responseText);
			
			var tpl = null;
			
			if (!Ext.isEmpty(responseObj.result.template)) {
				tpl = new Ext.XTemplate(responseObj.result.template);
			}
			else {
				tpl = new Ext.XTemplate(
					'<tpl for="data">',
					'<div class="icinga-detailed-info-container">',
					'<table cellpadding="0" cellspacing="0" border="0" class="icinga-detailed-info">',
					'<tpl for=".">',
						'<tr>',
							'<td class="key">{key}</td>',
							'<td class="val">{val}</td>',
						'</tr>',
					'</tpl>',
					'</table>',
					'</div>',
					'</tpl>'
				);
			}
			
			tpl.overwrite(el, responseObj.result);
		};
		
		pub.createToolTip = function(c) {
			
			pub.reset();
			pub.setConfig(c);
			
			if (config.autoDisplay) {
				pub.display();
			}
		}

		return pub;
})();

Icinga.util.showComments = function(oid, instanceid, target) {
	Icinga.util.SimpleDataProvider.createToolTip({
		srcId: 'comments',
		filter: [
			{key: 'object_id', value: oid},
			{key: 'instance_id', value: instanceid}
		],
		target: target
	});
}
