
var Grapher = {
		
		showGraph : function(sUrl, sId, oAdditionalParams) {
			sUrl = AppKit.appendParams(sUrl, oAdditionalParams);
			return AppKit.ajaxHtmlRequest(sUrl, sId, false);
		}
		
};

function GrapherFlashObject(sId) {
	
	
	this.sTargetId 	= sId;
	
	this.file		= "/flash/flexchart/NETWAYSGrapher.swf";
	this.express	= "/js/swfobject/expressInstall.swf";

	this.vars 		= {};
	this.params 	= {};
	
	this.width 		= null;
	this.height 	= null;
	this.version 	= null;
	
	this.setWidth = function(width) {
		this.width = width;
		return true;
	};
	
	this.setHeight = function(height) {
		this.height = height;
		return true;
	};
	
	this.setVersion = function(version) {
		this.version = version;
		return true;
	};
	
	this.setFile = function(sFile) {
		this.file = sFile;
	};
	
	this.addVar = function(key, val) {
		this.vars[key] = escape(val);
		return true;
	};
	
	this.addParam = function(key, val) {
		this.params[key] = val;
		return true;
	};
	
	this.embeddFlash = function() {
		swfobject.embedSWF(
			this.file, 
			this.sTargetId,
			this.width,
			this.height,
			this.version,
			this.express, 
			this.vars,
			this.params
		);
	};
	
	this.refresh = function() {
		this.embeddFlash();
	};
	
	this.redraw = function() {
		$('#' + this.sTargetId).replaceWith('<div id="' + this.sTargetId + '"></div>');
		this.embeddFlash();
	};
	
};

ChartObjects = new AppKitObjectContainer();
