Ext.ns("AppKit.principalEditor");
AppKit.principalEditor.STD_CONTAINER= "contentArea";


AppKit.principalEditor.principalStore = new Ext.data.JsonStore({
	autoDestroy:true,
	autoLoad:true,
	storeId: 'principalStore',
	idProperty: 'id',
	url: '<?php echo $ro->gen("modules.appkit.data.principals");?>',
	fields: ['id','name','description','type','fields']
});

AppKit.principalEditor.fieldConverter = function(v,record) {
	var counter = 1;
	result = [];
	if(Ext.isArray(v))
		return v;
	for(var i in v) {	
		result.push({'name': i, 'field_description': v[i]});
	}

	return result;
}

AppKit.principalEditor.principalSelector = Ext.extend(Ext.tree.TreePanel,{
	categoryNodes : {},
	singleNodes: {},
	
	
	constructor : function(cfg) {
		if(!cfg)
			cfg = {}
		this.setDefault(cfg);
		Ext.apply(this,cfg);
		
		AppKit.principalEditor.principalSelector.superclass.constructor.call(this,cfg);
		this.setupEditor();
	},
	
	
	editorStore : new Ext.data.JsonStore({
		autoDestroy: false,
		url: '<?php echo $ro->gen("icinga.api")?>'+'/json',
		autoLoad:false,
		root: 'result',
		forceSelection: false,
		storeId: 'editorStore'
	}),

	setupEditor: function() {
		this.editor = new Ext.tree.TreeEditor(this, new Ext.form.ComboBox({
				typeAhead:true,
				triggerAction:'all',
				store: this.editorStore,
				editDelay: -10,
				valueField: 'idField',
				forceSelection: false,
				displayField: 'idField'
			})
		);
		this.editor.getNode = function() {
			var selModel = this.tree.getSelectionModel();
			var selected = selModel.getSelectedNodes()[0];
			return selected;
		};
		
		this.editor.on("startedit",function(el,val) {
			var selected = this.editor.getNode();
			this.editor.__node = selected;
			if(!selected || !selected.field)
				this.editor.cancelEdit();
			var prefix = selected.field["name"];
			
			this.editor.setValue(val.replace(/\w*?: +/,""));
		},this);

		this.editor.on("beforecomplete",function(editor,val,startval) {

			var selected = this.editor.getNode();
			if(!editor.editNode)
				editor.editNode = selected;
			selected.field["value"] = val;
			editor.setValue(selected.field["name"]+": "+val);

		},this);
	},
	
	
	setDefault: function(cfg) {
		defaultObj = {
			useArrows:true,
			autoScroll:true,
			autoHeight:true,
			animate:true,
			enableDD: false,
			tbar: new Ext.Toolbar({
				items: [{
					iconCls:'icinga-icon-add',
					handler: function() {AppKit.principalEditor.principalList.show(this)},
					scope:this
				},{
					iconCls:'icinga-icon-delete',
					handler: function(_ev,toolEl,panel,tc) {
						this.removeSelectedNodes();
					},
					scope:this
				}]
			}),
			root: new Ext.tree.TreeNode({
				hidden:false,
				editable:false,
				text:'principals',
				expanded:true
			}),
			selModel: new Ext.tree.MultiSelectionModel()
		}
		Ext.apply(cfg,defaultObj);
	},

	prepareEditValue: function(node) {
		// @fixme: yep, looks stupid.
		var field = node.field.field_description.field;
		var target = node.field.field_description.target;
		this.editorStore.removeAll();
		this.editorStore.setBaseParam("columns[0]",field);
		this.editorStore.setBaseParam("target",target);
		this.editorStore.setBaseParam("withMeta",true);
		return true;
	},
	
	addPrincipal: function(record) {
		var cat = record.get("type");

		if(!this.categoryNodes[cat]) {
			var catNode = new Ext.tree.TreeNode({editable:false,text:cat,selectable:true})
			this.categoryNodes[cat] = this.getRootNode().appendChild(catNode);
		}

		this.categoryNodes[cat].appendChild(this.createPrincipalNode(record));
		this.categoryNodes[cat].type = "category";
		this.categoryNodes[cat].txt= cat;
		this.doLayout();
	},
	
	
	createPrincipalNode: function(record) {
		var node = new Ext.tree.TreeNode({
						editable:false,
						iconCls: 'icinga-icon-key',
						text:record.get("name")
					});
		node.targetId = record.get("id");
		node.type = "principal";
		var fields = AppKit.principalEditor.fieldConverter(record.get("fields"));
		if(fields.length == 0) {
			if(this.singleNodes[record.get("name")])
				return this.singleNodes[record.get("name")];
			this.singleNodes[record.get("name")] = node;
		}
		
		Ext.each(fields, function(field) {
			var subNode = new Ext.tree.TreeNode({
								editable:true,
								text:field["name"]+": "+(field["value"] || '')
						  });
			subNode.field = field;
			subNode.type = "value";
			subNode.isAPIPrincipal = record.get('type') == 'icinga';
			subNode.on("click", function(el) {
				this.prepareEditValue(el);
				
				this.editor.triggerEdit(subNode);
				
				return true;
			}, this);
			node.appendChild(subNode);

		},this);
		return node;
	},
	
	clearPrincipals: function() {
		if(!this.getRootNode().hasChildNodes())
			return true;
			
		this.categoryNodes = {}
		this.singleNodes = {}
		
		this.getRootNode().destroy();
		this.setRootNode(new Ext.tree.TreeNode({
			hidden:false,
			editable:false,
			text:'principals',
			expanded:true
		}))

	},

	
	loadPrincipalsForUser : function(userid) {

		Ext.Ajax.request({
			url: '<?php echo $ro->gen("modules.appkit.data.principals.user")?>'+userid,
			success: function(resp) {
				var data = Ext.decode(resp.responseText);
				this.setPrincipals(data);
			},
			scope: this
		});
	},
	
	loadPrincipalsForRole : function(role) {

		Ext.Ajax.request({
			url: '<?php echo $ro->gen("modules.appkit.data.principals.group")?>'+role,
			success: function(resp) {
				var data = Ext.decode(resp.responseText);
				this.setPrincipals(data);
			},
			scope: this
		});
	},
	
	setPrincipals: function(selected) {
		
		Ext.iterate(selected, function(target_name) {
			
			var fields = selected[target_name];
			
			Ext.iterate(fields, function(target_id, o) {
			
				var desc_record = this.getPrincipalDescriptor(target_name);
				var fields_list = AppKit.principalEditor.fieldConverter(desc_record.get("fields"));
				
				var i=0;
				
				Ext.iterate(o, function(fieldName, fieldValue) {
					
					fields_list[i].name = fieldName;
					fields_list[i++].value = fieldValue;
					
				}, this)
				
				desc_record.set('fields', fields_list);
				
				this.addPrincipal(desc_record);
				
				if(desc_record.store) desc_record.store.remove(desc_record);
				
			}, this);
			
		}, this);
	},
	
	getPrincipalDescriptor: function(pr_name) {
		var store = AppKit.principalEditor.principalStore;
		var found = null;
		
		store.each(function(record) {
			if(record.get("name") == pr_name) {
				found = record.copy();
				return false;
			}
		});
		
		return found;
	},
	
	/**
	 * Reads all principals from the tree and returns the data structure
	 * principal = {
	 * 		principal_target[target_id]{set: [1,1,..] , name :[name1, name2, ...]} 
	 * 		principal_value[target_id][fieldname][val1,val2,val3..]
	 * }
	 */
	getPrincipals: function() {
		var root = this.getRootNode();
		var principals = {
			principal_target: {},
			principal_values: {}
		}
		var counter = 0;
		root.cascade(function(node) {
			if(node.type != 'principal')
				return true;
			var vals = [];
			if(!principals.principal_target[node.targetId]) {
				principals.principal_target[node.targetId] = {set:[] ,name:[]};
				principals.principal_values[node.targetId] = {};
			}
			var target = principals.principal_target[node.targetId];
			var valueTarget = principals.principal_values[node.targetId];
			if(!valueTarget)
				valueTarget = {}
				
			target.set.push(1);
			target.name.push(node.text);
			if(node.hasChildNodes()) {
				node.eachChild(function(_child){
					if(_child.type == 'value') {
						if(!valueTarget[_child.field["name"]])
							valueTarget[_child.field["name"]] = [];
						valueTarget[_child.field["name"]].push(_child.field["value"]);	
					}
				});
			}
		
		},this)
		return principals;
	},
	
	
	removeSelectedNodes: function() {
		var selModel = this.getSelectionModel();
		var nodes = selModel.getSelectedNodes();
		var selectionNr = 0;
		Ext.each(nodes,function(node) {
			if(node.type == "value")
				return null;
			if(node.type == "principal") {
				selectionNr++;
				var parent = node.parentNode;
				node.remove();
				if(!parent.hasChildNodes()) {
					this.categoryNodes[parent.txt] = null;
					parent.remove();
				}
			} else if(node.type == "category") {
				Ext.Msg.confirm(_("Removing a category"),_("Do you really want to delete all ")+node.text+_(" principals?"),
					function(btn) {
						if(btn == "yes") {
							node.remove();
							selectionNr++;
						}
					}
				);
			}
		},this)
		if(!selectionNr)
			Ext.Msg.alert(_("Nothing selected"),_("No node was removed"));
	}
});


AppKit.principalEditor.principalList = new Ext.Window({
	width:700,
	autoHeight:true,
	autoScroll:true,
	layout:'form',
	modal: true,
	closeAction:'hide',
	title: _('Select a principal (Press Ctrl for multiple selects)'),
	bodyStyle:'background-color:#ffffff',
	items: new Ext.list.ListView({
		store: AppKit.principalEditor.principalStore,
		autoScroll:true,
		multiSelect: true,
		reserveScrollOffset: true,
		id: 'principalSelectorList',
		emptyText: _('no principals availabe'),
		columns: [{
			header: _('Principal'),
			width:.3,
			dataIndex: 'name'
		},{
			header: _('Description'),
			width:.5,
			dataIndex: 'description'
		},{
			header: _('Type'),	
			width:.2,
			dataIndex:'type'
		}]
	}),
	buttons: [{
		text: _('Add selected principals'),
		handler: function(b,e) {
			var selList = Ext.getCmp('principalSelectorList');
			var records = selList.getSelectedRecords();
			if(!records.length) {
				Ext.Msg.alert("",_("You haven't selected anything!"));
				return false;
			}	
			
			Ext.each(records,function(record) {
				AppKit.principalEditor.instance.addPrincipal(record);
			});
			selList.clearSelections();
			AppKit.principalEditor.principalList.hide();
		}
	}, {
		text: _('Clear selection'),
		handler: function() {
			var selList = Ext.getCmp('principalSelectorList');
			selList.clearSelections();
		}
	}]
	
})
AppKit.principalEditor.principalList.render(document.body);
AppKit.principalEditor.instance = new AppKit.principalEditor.principalSelector();
		