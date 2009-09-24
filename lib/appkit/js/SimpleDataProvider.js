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
			target:			false,
			delay:			false,
			autoDisplay:	true
		};

		this.reset = function () {
			this.config.url = "http://icinga-web/web/simpleDataProvider/json?src_id=";
			this.config.srcId = "";
			this.config.width = 200;
			this.config.filter = {};
			this.config.target = "";
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
			url = this.config.url + this.config.srcId + this.getFilter();
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
				new Ext.ToolTip({
					target: this.config.target,
					width: this.config.width,
					autoLoad: {url: this.getUrl()},
					dismissDelay: this.config.delay
				});
			}
		};

		this.reset();
		this.setConfig(config);
		if (this.config.autoDisplay) {
			this.display();
		}

}