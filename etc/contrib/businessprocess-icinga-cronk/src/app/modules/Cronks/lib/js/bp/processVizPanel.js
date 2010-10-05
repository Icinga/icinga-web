
Ext.onReady(function() {
	Ext.ns("Cronk.bp"); 		
	
	Cronk.bp.processVisualizer = new function() {
		var isVisualized = false;
		var visualizeJSON = function() {}
		var vizInstance = null;
		var setupInstance = function(elemId) {
			vizInstance = new $jit.ST({
				injectInto: elemId,
				duration: 400,
				transition: $jit.Trans.Quart.easeInOut,
				levelDistance: 50,
				Navigation: {
					enable:true,
					panning:true
				},

				Node: {
					height: 50,
					width: 180,
					type: 'rectangle',
					color: '#f00',
					overridable: true
				},

				Edge: {
					type: 'bezier',
					overridable: true
				},

				onCreateLabel: function(label,node) {
					label.id = node.id;
					label.innerHTML = node.name;
					label.onclick = function() {
					//	if(normal.checked) {
							vizInstance.onClick(node.id);
				//		} else {
				//			st.setRoot(node.id,'animate');
				//		}
					};
					var style = label.style;
					style.width = 160+'px';
					style.height = 50+'px';
					style.cursor = 'pointer';
					style.fontSize = '1.2em';
					style.textAlign = 'center';
					style.paddingTop = '3px';
				},

				onBeforePlotNode: function(node) {

					switch(node.data.status) {
						case 'OK': 
							node.data.$color = '#0f0';
							break;
						case 'WARNING':
							node.data.$color = '#ff0';
							break;
						case 'CRITICAL':
							node.data.$color = '#f00';
							break;
						case 'UNKNOWN':
						default:
							node.data.$color = '#ff8000';
							break;
						
					}	
				}
			});
		}	
		
		
		var id = 'con_'+Ext.id();
		
		var container = new Ext.Container({
			title: 'Visualization',	
			layout:'fit',
			items: [{
				xtype:'container',
				autoScroll:false,
				id: id,
				style: 'background-color:#565656'
							
			}],			
			hidden:false,
			autoScroll:false,
			listeners: {
				show: function() {
					(function() {
							
						if(!vizInstance) {	
							setupInstance(id);
							if(current_json) {
								render();
							}
						}
					}).defer(200);
				}
			}
	
		});

		var buildMapFromStartNode = function(node) {
			var json = {
				id: "_viz"+Ext.id(),
				name: node["display_name"],
				data: {
					status:node.origState
				},// node.attributes,
				children: []
			};
			if(!node.children)
				return json	
			if(node.children.length > 0) {
				for(var i=0;i<node.children.length;i++) {
					json.children.push(buildMapFromStartNode(node.children[i]));	
				}
			} 
			return json;
		}
		
		var render = function() {
				if(vizInstance ) {
					vizInstance.loadJSON(current_json);
					vizInstance.compute();
					vizInstance.onClick(vizInstance.root);
				}
		}

		var current_json = {}
		return {
			getContainer: function() {
				container.updateContent = function(node) {

					var startNode = node;
					if(node.parentNode.attributes.display_name)
						startNode = node.parentNode;

					current_json = buildMapFromStartNode(startNode.attributes);
					container.show();
					render();

				}
				return container;
			}
		}
	}
});
