function jitAddEvent(obj, type, fn) {
	if (obj.addEventListener) {
		obj.addEventListener(type, fn, false);
	} else {
		obj.attachEvent("on" + type, fn);
	}
};

var JitLog = {
	elem: false,
	write: function(elementId, text) {
		if (!this.elem) 
			this.elem = document.getElementById(elementId);
			this.elem.innerHTML = text;
			this.elem.style.left = (500 - this.elem.offsetWidth / 2) + "px";
		}
};

function JitStatusMap (config) {

	this.cmp = false;

	this.config = {
		url: false,
		params: false,
		method: "GET",
		timeout: 50000,
		disableCaching: true,
		parentId: false
	};

	this.configWritable = ["url", "params", "method", "timeout", "disableCaching", "parentId"];

	this.elementIds = {
		jitContainer: "jitContainer",
		jitContainerCenter: "jitContainerCenter",
		jitMap: "jitMap",
		jitContainerRight: "jitContainerRight",
		jitDetails: "jitDetails",
		jitLog: "jitLog",
		jitCanvas: "jitCanvas"
	};

	this.elementIdsWrite = ["jitContainer", "jitContainerCenter", "jitMap", "jitContainerRight", "jitDetails", "jitLog", "jitCanvas"];

	this.jitJson = false;

	function jitInit (json, elementIds) {
		var infovis = document.getElementById(elementIds.jitMap);
		var w = infovis.offsetWidth, h = infovis.offsetHeight;
		var canvas = new Canvas(elementIds.jitCanvas, {
			"injectInto": elementIds.jitMap,
			"width": w,
			"height": h,
			"backgroundCanvas": {
				"styles": {
					"strokeStyle": "#e0e0e0"
				},
				"impl": {
					"init": function() {},
					"plot": function(canvas, ctx) {
						var times = 6, d = 100;
						var pi2 = Math.PI * 2;
						for (var i = 1; i <= times; i++) {
							ctx.beginPath();
							ctx.arc(0, 0, i * d, 0, pi2, true);
							ctx.stroke();
							ctx.closePath();
						}
					}
				}
			}
		});
		var rgraph = new RGraph(canvas, {
			Node: {
				color: "#ccddee"
			},
			Edge: {
				color: "#56a5ec"
			},
			onBeforeCompute: function(node){
				JitLog.write(elementIds.jitLog, "centering " + node.name + "...");
				document.getElementById(elementIds.jitDetails).innerHTML = node.data.relation;
			},
			onAfterCompute: function(){
				JitLog.write(elementIds.jitLog, "done");
			},
			onCreateLabel: function(domElement, node){
				domElement.innerHTML = node.name;
				domElement.onclick = function(){
					rgraph.onClick(node.id);
				};
			},
			onPlaceLabel: function(domElement, node){
				var style = domElement.style;
				style.display = "";
				style.cursor = "pointer";
				if (node._depth <= 1) {
					style.fontSize = "1em";
					style.color = "#000000";
				} else if(node._depth == 2){
					style.fontSize = "0.9em";
					style.color = "#505050";
				} else {
					style.display = "none";
				}
				var left = parseInt(style.left);
				var w = domElement.offsetWidth;
				style.left = (left - w / 2) + "px";
			}
		});
		rgraph.loadJSON(json);
		rgraph.refresh();
		document.getElementById(elementIds.jitDetails).innerHTML = rgraph.graph.getNode(rgraph.root).data.relation;
	}

	this.init = function (config) {
		this.setConfig(config);
		this.setElementIds();
		this.createContainer();
		this.getMapData();
	}

	this.setConfig = function (config) {
		var numParams = this.configWritable.length;
		for (var x = 0; x < numParams; x++) {
			var key = this.configWritable[x];
			var value = config[key];
			if (value != undefined) {
				this.config[key] = value;
			}
		}
		if (this.config.parentId != false) {
			this.cmp = Ext.getCmp(this.config.parentId);
		}
	}

	this.setElementIds = function () {
		var numElements = this.elementIdsWrite.length;
		for (var x = 0; x < numElements; x++) {
			this.elementIds[this.elementIdsWrite[x]] += AppKit.Ext.genRandomId('cronk');
		}
	}

	this.createContainer = function () {
		var container = new Ext.Container({
			id: this.elementIds.jitContainer,
			autoEl: 'div', 
			layout: 'column',
			defaults: {
				xtype: 'container',
				autoEl: 'div',
				layout: 'auto',
				style: {
					border: "none"
				}
			},
			items : [{
				id: this.elementIds.jitContainerCenter,
				cls: "jitContainerCenter",
				items: {
					id: this.elementIds.jitMap,
					cls: "jitMap"
				}
			},{
				id: this.elementIds.jitContainerRight,
				cls: "jitContainerRight",
				items: {
					id: this.elementIds.jitDetails,
					cls: "jitDetails"
				}
			},{
				id: this.elementIds.jitLog,
				cls: "jitLog"
			}]
		});
		this.cmp.add(container);
		this.cmp.doLayout();
	}

	this.showMask = function () {
		this.mask = new Ext.LoadMask(Ext.getBody());
		this.mask.show();			
	}

	this.getMapData = function () {
		this.showMask();
		var ajax = Ext.Ajax.request({
			url : this.config.url,
			params : this.config.params,
			method : this.config.method,
			success : this.getMapDataSuccess,
			failure : this.getMapDataFail,
			callback : this.getMapDataDefault,
			scope: this,
			timeout : this.config.timeout,
			disableCaching : this.config.disableCaching,
		});
	}
	
	this.getMapDataSuccess = function (response, o) {
		this.jitJson = Ext.util.JSON.decode(response.responseText);
		jitInit(this.jitJson, this.elementIds);
		this.mask.hide();
		this.mask.disable();
	}

	this.getMapDataFail = function (response, o) {
		this.jitJson = {};
	}

	this.getMapDataDefault = function (options, success, request) {
		
	}

	this.init(config);

}