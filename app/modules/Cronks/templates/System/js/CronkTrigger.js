function CronkTrigger (config) {

	var thisConfig = {};

	function initConfig() {
		thisConfig.objectId = false;
		thisConfig.objectName = false;
		thisConfig.objectType = false;
		thisConfig.returnVal = false;		
	};

	function setConfig (config) {
		if (config.objectId != undefined && config.objectType != undefined) {
			thisConfig.objectId = config.objectId;
			thisConfig.objectName = config.objectName;
			thisConfig.objectType = config.objectType;
			completeConfig();
		}
	};

	function completeConfig () {
		switch (thisConfig.objectType) {
			case "host":
				thisConfig.idPrefix = "servicesForHost";
				thisConfig.titlePrefix = "Services for ";
				thisConfig.targetTemplate = "icinga-service-template";
				thisConfig.targetField = "host_object_id";
				break;
			default:
				initConfig();
				break;
		}
	};

	function createCronk () {
		if (thisConfig.objectId != false && thisConfig.objectType != false) {
			var cronk = {
				parentid: thisConfig.idPrefix + "subGridComponent",
				title: thisConfig.titlePrefix + thisConfig.objectName,
				crname: "gridProc",
				closable: true,
				params: {template: thisConfig.targetTemplate}
			};

			var filter = {};
			filter["f[" + thisConfig.targetField + "-value]"] = thisConfig.objectId;
			filter["f[" + thisConfig.targetField + "-operator]"] = 50;

			AppKit.Ext.util.InterGridUtil.gridFilterLink(cronk, filter);
		}
	};

	initConfig();
	setConfig(config);
	createCronk();
	return thisConfig.returnVal;

};