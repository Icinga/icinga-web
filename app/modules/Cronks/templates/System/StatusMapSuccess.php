<?php
	/**
	* @author Christian Doebler <christian.doebler@netways.de>
	*/
?>
<div id="jitContainer">
	<div id="jitContainerCenter">
		<div id="jitMap"></div>    
	</div>
	<div id="jitContainerRight">
		<div id="jitDetails"></div>
	</div>
	<div id="jitLog"></div>
</div>
<script type="text/javascript">
	function jitAddEvent(obj, type, fn) {
		if (obj.addEventListener) {
			obj.addEventListener(type, fn, false);
		} else {
			obj.attachEvent("on" + type, fn);
		}
	};

	var JitLog = {
		elem: false,
		write: function(text) {
			if (!this.elem) 
				this.elem = document.getElementById("jitLog");
				this.elem.innerHTML = text;
				this.elem.style.left = (500 - this.elem.offsetWidth / 2) + "px";
			}
	};

	function JitStatusMap (config) {

		this.config = {
			url: false,
			params: false,
			method: "GET",
			timeout: 50000,
			disableCaching: true
		};

		this.configWritable = ["url", "params", "method", "timeout", "disableCaching"];

		this.jitJson = false;

		function jitInit (json) {
			var infovis = document.getElementById("jitMap");
			var w = infovis.offsetWidth, h = infovis.offsetHeight;
			var canvas = new Canvas("mycanvas", {
				"injectInto": "jitMap",
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
					color: "#56A5EC"
				},
				onBeforeCompute: function(node){
					JitLog.write("centering " + node.name + "...");
					document.getElementById("jitDetails").innerHTML = node.data.relation;
				},
				onAfterCompute: function(){
					JitLog.write("done");
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
			document.getElementById("jitDetails").innerHTML = rgraph.graph.getNode(rgraph.root).data.relation;
		}

		this.init = function (config) {
			this.setConfig(config);
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
		}

		this.getMapData = function () {
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
			jitInit(this.jitJson);
		}

		this.getMapDataFail = function (response, o) {
			this.jitJson = {};
		}

		this.getMapDataDefault = function (options, success, request) {
			
		}

		this.init(config);

	}

	var statusMap = new JitStatusMap({
		url: "/web/cronks/statusMap/json"
	});

</script>