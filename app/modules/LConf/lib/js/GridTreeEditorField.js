Ext.onReady(function() {
	Ext.ns("AppKit");
	AppKit.GridTreeEditorField = Ext.extend(Ext.Component, {
		
		constructor: function(cfg) {
			Ext.apply(this,cfg);
			Ext.Component.prototype.constructor.call(this,cfg);
			this.bindEditorEvents();

		},
		reactivateGrid: function() {
			if(this.grid)
				this.grid.resumeEvents();
		},
		bindEditorEvents: function() {
			this.on("hide",this.reactivateGrid,this);
			this.on("destroy",this.reactivateGrid,this);
		},
		types: {},
		editing: false,
		ignoreNoChange: true,
		value: "",
		startValue: "",
	
		field: {
			focus: function () {}
		},
	
		setValue: function (v) {	
			this.value = v; 
		},
		
		getValue: function () {
			if(this.value == "")
				this.value = this.startValue;
			return this.value;
		},
	
		reset: function () {},
	
		focus: function () {},
	
		realign: function () {},
		
		searchStartValue: function () {
		},
		
		cancelEdit: function (remainVisible) {
			if(Ext.EventObject.browserEvent.type == "mousewheel")
				return false; //ignore cancel on scroll
		
			
			//this.fireEvent("canceledit",this,this.getValue,this.startValue);
			this.hideEdit(remainVisible);	
			this.targetNode.update(this.getValue());	
			this.fireEvent("complete",this,this.getValue(),this.startValue);	
			return true	;
		},
		
		completeEdit: function (remainVisible) {
			if(!this.editing) {
				return;
			}
			if(this.startValue == this.getValue() && this.ignoreNoChange) {
				this.hideEdit(remainVisible);	
				return;
			}
			if(this.fireEvent("beforeComplete",this,this.getValue(),this.startValue) !== false) {
				var value = this.getValue();
				this.hideEdit(remainVisible);

				this.fireEvent("complete",this,value,this.startValue);
			}
		},
		
		hideEdit: function (remainVisible) {
			if(remainVisible !== true) {
				this.editing = false;
				if(this.tree)		
					this.tree.destroy()
				if(this.tree.editorTxt)
					this.tree.editorTxt.destroy();
				this.grid.resumeEvents();
			}
		},
		
		getTree: function () {
			var tree = new Ext.tree.TreePanel({
				autoDestroy: true,
				animate: true,
				rootVisible: false,
				enableDD: false,
				containerScroll:true,
				autoScroll:true,
				layout:'fit',
				border: true,
				singleExpand: true,
				cls:'propertySelectorList',
			//	style: 'height:200px',
			
				root: {
					nodeType: 'async',
					autoScroll:true,
					
					draggable: false,
					loader: new Ext.tree.TreeLoader({
						url: this.url,
						baseParams: {
							"asTree": true,
							"field" : 'properties',
							"connectionId" : this.grid.connId || this.grid.store.baseParams.connectionId
						}
					})
				},
				listeners: {
					click: function(node,e) {
						if(!node.isLeaf())
							return true;
						this.tree.editorTxt.setValue(node.text);
						this.setValue(node.text);
						return false;
					},
					dblclick: function (node, e) {
						if(!node.isLeaf())
							return true;

						this.setValue(node.text);
						this.completeEdit();
						return false;	
					},
					scope:this
				}
			});
			tree.filterByProperty = function(p) {
				var root = this.getRootNode();
				var r = new RegExp('.*'+p+'.*');
				r.ignoreCase = true;
				root.cascade(function(node) {
					if(!node.isLeaf())
						return true;
					if(r.test(node.text)) {
						if(node.hidden) {
							node.getUI().show();	
						}
					} else {
						if(!node.hidden) {
							node.getUI().hide();	
						}	
					}
				},this);
			}
			tree.getRootNode().addListener("expand",function (root) {
				var toExpand = null;
				root.eachChild(function (child) {
					if(!child.attributes.objclasses)
						return true;
					for (var i=0; i< child.attributes.objclasses.length; i++) {
						var e = child.attributes.objclasses[i]; 
						if((e == "DEFAULT" && !toExpand) || this.types[e]) {
							toExpand = child;
							continue;
						}
					}
					return true;
				},this);
				if(toExpand) {
					toExpand.expand();
					toExpand.ensureVisible();
				}
			},this);
		
			tree.getRootNode().expand();
			return tree;
		},
		
		determineType: function () {
			var store = this.record.store;
			Ext.iterate(store.data.items,function (el) {
				if(el.id.split("_")[0].toLowerCase() == "objectclass") {
					this.types[el.data.value] = true;
				}
			},this)
		},
		cancelEditEv: function (ev,target) {
			if(!this.editing)
				return true;
			var el = Ext.get(target);
			if(el.hasClass('x-tree-root-ct') || el.parent('.x-tree-root-ct')) {
				ev.stopEvent();
				return false;
			} else {	
				this.cancelEdit();
				return true;
			}
		},

		startEdit: function (el) {
			this.editing = true;
			this.startValue = el.innerHTML != '&nbsp;' ? el.innerHTML : '';			
			this.determineType();
			this.tree =  this.getTree();
			this.tree.setPosition(Ext.EventObject.getPageX(),Ext.EventObject.getPageY());
			this.grid.suspendEvents();
			this.grid.el.addListener("click",this.cancelEditEv,this);
			
			this.tree.editorTxt = new Ext.form.TextField({
				cls: 'x-tree-root-ct',
				value: this.startValue || '',
				enableKeyEvents: true
			});
			
			this.tree.editorTxt.addListener("focus",function(me) {
				me.setValue("");
			},this,{single:true});

			this.tree.editorTxt.addListener("keyup",function(e) {		
				var intermediateValue = this.tree.editorTxt.getRawValue();
				this.tree.filterByProperty(intermediateValue);
			},this,{buffer:true});
			
			this.tree.editorTxt.addListener("change",function(e) {
				var intermediateValue = this.tree.editorTxt.getRawValue();
				this.tree.filterByProperty(intermediateValue);
	
				this.setValue(e.getValue());
			
			},this);

			var propertyTextNode = Ext.get(Ext.get(el.parentNode).child('.x-grid3-col-property'));
			propertyTextNode.update("");
			this.targetNode = propertyTextNode;
			this.tree.editorTxt.render(propertyTextNode);
			this.tree.render(el.parentNode);
			
			this.fireEvent("startedit",el.parentNode,this.startValue);
		},
		
		stopEdit: function () {
			this.hideEdit();
		},
		onDestroy: function () {
			this.hideEdit();
		}
	});
})
