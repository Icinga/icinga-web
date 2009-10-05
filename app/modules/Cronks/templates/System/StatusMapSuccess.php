<?php
	/**
	* @author Christian Doebler <christian.doebler@netways.de>
	*/
?>
<div id="jitContainer">
	<div id="jitContainerLeft">
		<div class="text">
			<h4>Tree Animation</h4>
			Icinga test
		</div>
		<div id="jitIdList"></div>
	</div>
	<div id="jitContainerCenter">
		<div id="jitMap"></div>    
	</div>
	<div id="jitContainerRight">
		<div id="jitDetails"></div>
	</div>
	<div id="jitLog"></div>
</div>
<script type="text/javascript">
//	var JitLog = {
//	    elem: false,
//	    write: function(text){
//	        if (!this.elem) 
//	            this.elem = document.getElementById("jitLog");
//	        this.elem.innerHTML = text;
//	        this.elem.style.left = (500 - this.elem.offsetWidth / 2) + "px";
//	    }
//	};
//
//	function jitAddEvent(obj, type, fn) {
//	    if (obj.addEventListener) obj.addEventListener(type, fn, false);
//	    else obj.attachEvent("on" + type, fn);
//	};
//
//
//	function jitInit(){
//	    //init data
//	    var json = {};
//	    //end
//	    
//	    var infovis = document.getElementById("jitMap");
//	    var w = infovis.offsetWidth, h = infovis.offsetHeight;
//	    
//	    //init canvas
//	    //Create a new canvas instance.
//	    var canvas = new Canvas("mycanvas", {
//	        //Where to append the canvas widget
//	        "injectInto": "infovis",
//	        "width": w,
//	        "height": h,
//	        
//	        //Optional: create a background canvas and plot
//	        //concentric circles in it.
//	        "backgroundCanvas": {
//	            "styles": {
//	                "strokeStyle": "#555"
//	            },
//	            
//	            "impl": {
//	                "init": function(){},
//	                "plot": function(canvas, ctx){
//	                    var times = 6, d = 100;
//	                    var pi2 = Math.PI * 2;
//	                    for (var i = 1; i <= times; i++) {
//	                        ctx.beginPath();
//	                        ctx.arc(0, 0, i * d, 0, pi2, true);
//	                        ctx.stroke();
//	                        ctx.closePath();
//	                    }
//	                }
//	            }
//	        }
//	    });
//	    //end
//	    //init RGraph
//	    var rgraph = new RGraph(canvas, {
//	        //Set Node and Edge colors.
//	        Node: {
//	            color: "#ccddee"
//	        },
//	        
//	        Edge: {
//	            color: "#772277"
//	        },
//
//	        onBeforeCompute: function(node){
//	            JitLog.write("centering " + node.name + "...");
//	            //Add the relation list in the right column.
//	            //This list is taken from the data property of each JSON node.
//	            document.getElementById("jitDetails").innerHTML = node.data.relation;
//	        },
//	        
//	        onAfterCompute: function(){
//	            JitLog.write("done");
//	        },
//	        //Add the name of the node in the correponding label
//	        //and a click handler to move the graph.
//	        //This method is called once, on label creation.
//	        onCreateLabel: function(domElement, node){
//	            domElement.innerHTML = node.name;
//	            domElement.onclick = function(){
//	                rgraph.onClick(node.id);
//	            };
//	        },
//	        //Change some label dom properties.
//	        //This method is called each time a label is plotted.
//	        onPlaceLabel: function(domElement, node){
//	            var style = domElement.style;
//	            style.display = "";
//	            style.cursor = "pointer";
//
//	            if (node._depth <= 1) {
//	                style.fontSize = "0.8em";
//	                style.color = "#ccc";
//	            
//	            } else if(node._depth == 2){
//	                style.fontSize = "0.7em";
//	                style.color = "#494949";
//	            
//	            } else {
//	                style.display = "none";
//	            }
//
//	            var left = parseInt(style.left);
//	            var w = domElement.offsetWidth;
//	            style.left = (left - w / 2) + "px";
//	        }
//	    });
//	    
//	    //load JSON data
//	    rgraph.loadJSON(json);
//	    //compute positions and make the first plot
//	    rgraph.refresh();
//	    //end
//	    //append information about the root relations in the right column
//	    document.getElementById("jitDetails").innerHTML = rgraph.graph.getNode(rgraph.root).data.relation;
//	}

	//jitInit("/web/cronks/statusMap/json");

	function JitStatusMap (config) {

		this.config = {
			url: false,
			//url: "/web/cronks/statusMap/json",
			params: false,
			method: "GET",
			timeout: 50000,
			disableCaching: true
		};

		this.jitJson = {};

		this.init = function (config) {
			this.setConfig(config);
			this.getMapData();
		}

		this.setConfig = function (config) {
			Ext.iterate(
				this.config,
				function (key, value) {
alert(key + "|" + value);
					if (config[key] != undefined) {
alert(value);
						this.config[key] = value;
					}
				}
			);
alert(this.config.url);
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
alert(response.responseText);
			this.jitJson = Ext.util.JSON.decode(response.responseText);
		}

		this.getMapDataFail = function (response, o) {
			
		}

		this.getMapDataDefault = function (options, success, request) {
			
		}

		this.init(config);

	}

	var statusMap = new JitStatusMap({
		url: "/web/cronks/statusMap/json"
	});

alert("nix");
</script>