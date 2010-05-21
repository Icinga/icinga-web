/**
 * simple data provider - display object
 * Date: 2009-09-17
 * Author: Christian Doebler <christian.doebler@netways.de>
 */
function SimpleDataProvider (config) {

		this.config = {
			url:			false,
			srcId:			false,
			width:			false,
			filter:			false,
			targetXY:		false,
			delay:			false,
			autoDisplay:	true
		};

		this.reset = function () {
			this.config.url = AppKit.c.path + "/web/simpleDataProvider/json?src_id=";
			this.config.srcId = "";
			this.config.width = 200;
			this.config.filter = {};
			this.config.targetXY = [0, 0];
			this.config.delay = 15000;
			this.config.autoDisplay = true;
			return this;
		};

		this.getFilter = function () {
			var filter = "";
			if (this.config.filter !== false) {
				var filterDef = this.config.filter;
				var filterCount = filterDef.length;
				for (var x = 0; x < filterCount; x++) {
					filter += "&filter[" + filterDef[x].key + "]=" + filterDef[x].value;
				}
			}
			return filter;
		};

		this.getUrl = function () {
			var url = this.config.url + this.config.srcId + this.getFilter();
			return url;
		};

		this.checkData = function () {
			var dataOk = false;
			if (this.config.url !== false && this.config.srcId !== false && this.config.target !== false) {
				dataOk = true;
			}
			return dataOk;
		};

		this.setConfig = function (config) {
			for (key in config) {
				if (this.config[key] != undefined) {
					this.config[key] = config[key];
				}
			}
		};

		this.display = function () {
			if (this.checkData()) {

				var toolTip = new Ext.ToolTip({
					width: this.config.width,
					dismissDelay: this.config.delay
				});

				toolTip.render(Ext.getBody());
				toolTip.targetXY = this.config.targetXY;

				toolTip.getUpdater().update({
					url: this.getUrl(),
					callback: this.outputTable
				});

				toolTip.show();

			}
		};

		this.outputTable = function (el, success, response, options) {
			var responseObj = Ext.util.JSON.decode(response.responseText);
			var tpl = new Ext.XTemplate(
				'<table cellpadding="0" cellspacing="0" border="0">',
				'<tpl for="data">',
					'<tr>',
						'<td>{key}</td>',
						'<td>{value}</td>',
					'</tr>',
				'</tpl>',
				'</table>'
			);
			tpl.overwrite(el, responseObj.result);
		};

		this.reset();
		this.setConfig(config);
		if (this.config.autoDisplay) {
			this.display();
		};

}