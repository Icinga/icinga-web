<script type="text/javascript">
Ext.ns("Cronk.bp");
/**
 * TODO: Refactor this mess
 */
// This is the init method called when the cronk environment is ready
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {
	Ext.Msg.minWidth = 350;
	this.stateful = true;
	this.stateId = "state_"+this.id;
	
	var fastMode = (Ext.isIE6 || Ext.isIE7 || Ext.isIE8);	
	var CE = this;
	var parentCmp = this.getParent();
	var stateId = this.id + '_bpmanager_panel';
	parentCmp.removeAll();
	var treeLoader = new Ext.ux.tree.TreeGridLoader({});
	var root = new Ext.tree.TreeNode({
		nodeText:_('Business processes'), 
		bpType: 'bp', 
		type:'root',
		iconCls:'icinga-icon-bricks',
		
		id:'root', 
		uiProvider: Ext.ux.tree.TreeGridNodeUI,
		expanded:true
	});
	
	Cronk.bp.processTree = Ext.extend(Ext.ux.tree.TreeGrid,{ 
		rootVisible:true,
		autoDestroy: true,
		enableDD: true,
		ddGroup:'filterEditor',
		autoScroll:true,
		columns:[{
			header:_('Name'),
			width:200,
			dataIndex: 'nodeText'
		},{
			header:_('Display name'),
			width:200,
			dataIndex: 'bpLongName'
		},{
			header:_('Template'),
			width:100,
			dataIndex: 'bpTemplate'
		},{
			header:_('Status information'),
			dataIndex: 'bpStatus',
			width:100
		},{
			header:_('Priority'),
			dataIndex: 'prio',
			width:50
		},{
			header:_('Type'),
			dataIndex: 'type',
			width:50
		},{
			header:_('Min'),
			dataIndex: 'min',
			width:50
		}],
		loader: treeLoader,
		constructor: function(cfg) {
			this.root = root;
			Ext.apply(this,cfg);
			Ext.tree.TreePanel.prototype.constructor.call(this,cfg);
		},
	    
	    listeners: {
	    	resize: function() {
	    		this.ownerCt.setHeight(this.ownerCt.ownerCt.ownerCt.getInnerHeight());
	    	},
	    	contextmenu: function(node,event) {
			event.preventDefault();

			var hideEdit;
			if(node.attributes)
				hideEdit = node.attributes.isAlias && !node.attributes.service;
				
	    		var ctx = new Ext.menu.Menu({
	    			items: [{
	    				text: _('Create new business process at this level'),
	    				iconCls: 'icinga-icon-chart-organisation',
	    				hidden: node.attributes.isAlias || node.attributes.serviceInfo,
	    				handler:function(btn) {
	    					if(node.attributes)
	    						Cronk.bp.processController.addBusinessProcess(node,node,event);
	    				}
	    			},{
	    				text: _('Add existing process to this level'),
	    				iconCls: 'icinga-icon-chart-organisation',
	    				hidden: node.attributes.isAlias || node.attributes.serviceInfo,
	    				handler:function(btn) {
	    					if(node.attributes)
	    						Cronk.bp.processController.addExistingBusinessProcess(node,node,event);
	    				}
	    			},{
	    				text: _('Add service'),
	    				iconCls: 'icinga-icon-cog',
	    				hidden: node.attributes.isAlias || node.attributes.serviceInfo,
	    				handler: function(btn) {
	    					if(node.attributes)
	    						Cronk.bp.processController.addService(node,node,event);
	    				}
	    			},{
	    				text: _('Edit this node'),
	    				iconCls: 'icinga-icon-page-edit',
	    				hidden: !node.parentNode || hideEdit,
	    				handler: function(btn) {
					    AppKit.log(node,node,event,node.attributes);
					    if(node.attributes.bpType == 'service')
					        Cronk.bp.processController.addService(node,node,event,node.attributes);
					    if(node.attributes.bpType == 'bp' && node.parentNode)
						Cronk.bp.processController.addBusinessProcess(node,node,event,node.attributes);
	    				},
	    				scope: this
	    			},{
	    				text: _('Remove this node'),
	    				iconCls: 'icinga-icon-delete',
	    				hidden: !node.parentNode,
	    				handler: function(btn) {
	    					var nameToDelete = '';
	    					var tree = node.getOwnerTree();
	    					if(node.attributes)
	    						if(!node.attributes.isAlias)
	    							nameToDelete = node.attributes.bpName; 
	    					node.parentNode.removeChild(node,true);
	    					// delete references if necessary
	    					if(nameToDelete) {
	    						Cronk.bp.processController.deleteAllProcessOccurences(nameToDelete,tree);
	    						
	    					}
	    				},
	    				scope: this
	    			}]
	    		}).showAt(event.getXY());
	    	},
	    	nodedragover: function(dragOverEvent) {
	    		var dragNode;
	    		if(dragOverEvent.data.selections)
					dragNode = dragOverEvent.data.selections[0].data;
				else if(dragOverEvent.data.node)
					dragNode = dragOverEvent.data.node.attributes;
				else 
					return false;
		
	    		var target = dragOverEvent.target;	    		
	    		if(target.attributes.bpType == 'service')
	    			return false;

	    		switch(dragNode.bpType) {
	    			case 'bp':    				
	    				return true;
	    			case 'service':
	    				if(target.attributes.bpType == 'bp' && dragOverEvent.point == 'append' && target.parentNode)
	    					return true;
	    				return false;
	    			default: 
	    				return false;
	    		}
	    	},
	    	beforeNodeDrop: function(event) {
	    		event.cancel = false;
	    		event.dropNode = [];
	    		
	    		if(event.data.node) {
	    			event.dropNode = [event.data.node];
	    			return true;
	    		}
	    		var dropNode  = event.data.selections[0];

	    		switch(dropNode.get('bpType')) {
	    			case 'service':
	    				Cronk.bp.processController.addService(dropNode,event.target,event.rawEvent);
	    				return false;
	    			case 'bp':
					Cronk.bp.processController.displayAddMenu(dropNode,event);
	    				return false;
	    			default:
	    				return false;
	    		}
	    	},
	    	remove: function() {
	    		Cronk.bp.processController.fireEvent('changed');
	    	},
	    	append: function() {
	    		Cronk.bp.processController.fireEvent('changed');
	    	},
	    	movenode: function() {
	    		Cronk.bp.processController.fireEvent('changed');
	    	},
	    	textchange: function() {
	    		Cronk.bp.processController.fireEvent('changed');
	    	}
	    }
	});
	
	/**
	 * Controller class for adding nodes, parsing the config, and all things
	 * that isn't view specific
	 */
	Cronk.bp.processController = new (Cronk.bp.processControllerClass = Ext.extend(Ext.util.Observable,{
		
		changed: false,
		baseTitle: null,
		
		constructor: function() {
			Cronk.bp.processControllerClass.superclass.constructor.call(this);
			
			this.on('changed', this.onChanged, this);
			
			this.baseTitle = parentCmp.title.replace(/\s+\([^\)]+\)$/, '');
			
			Ext.EventManager.addListener(window, 'blur', this.handleBlur, this);
			
			Ext.getCmp('cronk-tabs').on('beforetabchange', function(tabpanel, newTab, currentTab) {
				if (currentTab == parentCmp) {
					this.handleBlur(function() {
						Ext.getCmp('cronk-tabs').setActiveTab(parentCmp);
					})
				}
			}, this);
			
		},
		
		changeTitle: function(configName) {
			var title = String.format('{0} ({1})', this.baseTitle, configName);
			parentCmp.setTitle(title);
			parentCmp.doLayout();
		},
		
		onChanged: function() {
			this.changed=true;
		},
		
		hasChanged: function() {
			return this.changed;
		},
		
		processLoad: function(configName) {
			Cronk.bp.processController.getConfigJson(configName, function() {
				Cronk.bp.processController.curConfigName = configName;
				Cronk.bp.processController.changed = false;
				this.changeTitle(configName);
				Ext.state.Manager.set(stateId, bpManager.getState());
			}, this);
		},
		
		processSave: function(configName) {
			configName = configName || Cronk.bp.processController.curConfigName;
			if (configName) {
				this.createConfigForTree(configName);
				this.changed = false;
				this.changeTitle(configName);
				Ext.state.Manager.set(stateId, bpManager.getState());
			}
			else {
				this.processSaveAs();
			}
			
			Cronk.bp.processController.curConfigName = configName;
		},
		
		processSaveAs: function() {
			var _this = this;
			
			Ext.Msg.prompt(_('Filename'), _('Please enter a name for the file:'), function(btn, text){
			    if (btn == 'ok'){
			    	if(!text.match(/^[A-Za-z0-9_-]{3,25}$/)) {
				    	Ext.Msg.alert(_("Error"),_("Please provide a valid file name (min 3, max 25 chars, alphanumeric)"));
				   		return false;
			    	}
			    	_this.processSave(text);
			    }
			});
		},
		
		processNew: function() {
			var _this = this;
			var changed = (Cronk.bp.processController.curConfigName || this.changed)
			
			var doRemove = function() {
				_this.changeTitle(_('New'));
				root.removeAll();
				_this.changed = false;
		   		Cronk.bp.processController.curConfigName = null;
		   		Ext.state.Manager.set(stateId, bpManager.getState());
			}
			
			if (changed!== false) {
				Ext.Msg.show({
					title: _('Creating new'),
					msg: _('Process exists, creating new one?'),
					buttons: Ext.Msg.YESNO,
					fn: function(button) {
						if (button=='yes') {
		   					doRemove();
		   				}
					},
					icon: Ext.MessageBox.QUESTION
				});
				
			}
			else {
				doRemove();
			}
		},
		
		handleBlur: function(callback) {
			if (Cronk.bp.processController.hasChanged()) {
				
				var e = Ext.EventObject;
				e.setEvent(window.event);
				e.stopEvent();
				
				Ext.Msg.show({
					title: _('Save Changes?'),
					msg: 'You are closing a tab that has unsaved changes. Would you like to save your changes?',
					buttons: Ext.Msg.YESNOCANCEL,
					fn: function(button) {
						if (button=='yes') {
		   					Cronk.bp.processController.processSave();
		   				}
		   				else if (button=='cancel') {
		   					if (Ext.isFunction(callback)) {
		   						callback.call(this);
		   					}
		   				}
					},
					icon: Ext.MessageBox.QUESTION
				});
			}
		},
		
		hostStore: new Ext.data.JsonStore({
			autoDestroy:false,
			url: '<?php echo $ro->gen("icinga.api");?>'+'/json',
			idProperty: 'HOST_ID',
			baseParams: {
				'target' : 'host',
				'columns[0]' : 'HOST_ID',
				'columns[1]' : 'HOST_NAME'
			},
			fields: ['HOST_ID','HOST_NAME']
		}),
		
		serviceStore: new Ext.data.JsonStore({
			autoDestroy:false,
			url: '<?php echo $ro->gen("icinga.api");?>'+'/json',
			idProperty: 'SERVICE_ID',
			baseParams: {
				'target' : 'service',
				'columns[0]' : 'SERVICE_ID',
				'columns[1]' : 'SERVICE_NAME'
			},
			fields: ['SERVICE_ID','SERVICE_NAME']
		}),
		getBusinessProcessList: function(target) {
			var tree = target.getOwnerTree();
			var arr = {}
			tree.getRootNode().cascade(function(node) {
				// avoid circular references
				if(target.isAncestor(node) || target == node)
					return true;
				if(node.attributes.bpType == 'bp')
					arr[node.attributes.bpName] = {bp_name:node.attributes.bpName, bp_node:node};
			})
			var returnObj = []
			for(var bpName in arr) {
				returnObj.push(arr[bpName]);
			} 
			
			return returnObj;
		},
		deleteAllProcessOccurences: function(nodename,tree) {
			var alias = this.getProcessByName(nodename,tree);
			while(alias) {
				alias.parentNode.removeChild(alias,true);
				alias = this.getProcessByName(nodename,tree);
			}
			
		},
		getProcessByName: function(nodename,tree,presetNode) {
			var foundNode = false;
			tree.getRootNode().cascade(function(node) {
				if(!node.attributes.bpName || foundNode)
					return true;
				if(presetNode)
					if(presetNode.bpName == node.attributes.bpName)
						return true;
				if(node.attributes.bpName == nodename) {
							
					foundNode = node; 
					return false;
				}
			})
			return foundNode;
		},
		displayAddMenu: function(dropNode,event) {
	    		(new Ext.menu.Menu({
	    			items: [{
				    text: _('Add new node'),
				    iconCls: 'icinga-icon-chart-organisation',
				    handler: function() {
						Cronk.bp.processController.addBusinessProcess(dropNode,event.target,event.rawEvent);
				    }
				},{
				    text: _('Add existing node'),
				    iconCls: 'icinga-icon-chart-organisation',
				    handler: function() {
						Cronk.bp.processController.addExistingBusinessProcess(dropNode,event.target,event.rawEvent);
				    }
				}]
			})).showAt(event.rawEvent.getXY());
		},

		addBusinessProcess: function(node,target,event,presets) {;
			var curId = Ext.id("","bpAdder");
			
			var tree = target.getOwnerTree();
			var bpCfg = {
				items: [{
					xtype: 'container',
					layout: 'fit',
					html:'<h1 style="margin:2px;font-size:12px;">'+(presets ? 'Edit' : 'Add')+' Business Process</h1>'
				},
				new Ext.form.FormPanel({
					id:curId,
					padding:5,
					height:350,
					autoScroll:true,
					items: [{
					    xtype:'fieldset',
					    id: curId+"_newBP_fs",
					    items: [{
						    xtype:'textfield',
						    name:'newBP_name',
						    allowBlank:false,
						    width:130,
						    value: presets ? presets.bpName : null,
						    regex: /[A-Za-z_]*/,
						    regexText: _('Your process name contains invalid characters'),
						    fieldLabel: _('Business Process name'),
						    validator: (function(val) {
							    if(!(new RegExp("[A-Za-z_]*?")).test(val))
								return _("Invalid name!");
							    if(!val)
								return _("A name is required");

							    if(this.getProcessByName(val,tree,presets))
								    return _("A business process with this name already exists!")
						    }).createDelegate(this)
					    },{
						    xtype:'textfield',
						    name:'newBP_longName',
						    allowBlank:false,
						    width:130,
						    value: presets ? presets.bpLongName : null,
						    fieldLabel: _('Displayname'),
						    validator: (function(val) {
							    if(!val)
								    return _("A display name is required");

						    }).createDelegate(this)
					    },{
						    xtype:'textfield',
						    name:'newBP_status',
						    allowBlank:true,
						    width:130,
						    value: presets ? presets.bpStatus : null,
						    fieldLabel: _('Status (optional)')

					    },{
						    xtype:'textfield',
						    name:'newBP_template',
						    allowBlank:true,
						    width:130,
						    value: presets ? presets.bpTemplate : null,
						    fieldLabel: _('Template (optional)')

					    },{
						    xtype:'combo',
						    name:'newBP_type',
						    forceSelection:true,
						    store: new Ext.data.ArrayStore({
							    fields: ['type'],
							    idIndex:0,
							    autoDestroy:true,
							    data:[['AND'],['OR'],['MIN']]
						    }),
						    mode:'local',
						    allowBlank:false,
						    width:130,
						    displayField:'type',
						    valueField:'type',
						    triggerAction:'all',
						    value:presets ? presets.type : null,
						    fieldLabel: _('Type'),
						    listeners: {
							    select: function(cmb,record) {
								    if(record.get('type') == 'MIN')
									    Ext.getCmp(curId+"_newBP_minField").setDisabled(false);
								    else
									    Ext.getCmp(curId+"_newBP_minField").setDisabled(true);
							    }
						    }
					    },{
						    xtype:'numberfield',
						    width:30,
						    name:'newBP_min',
						    value: presets ? presets.min : null,
						    id:curId+"_newBP_minField",
						    fieldLabel: _('Min'),
						    validator: function(val) {
							    if(val < 1 && !Ext.getCmp(curId+"_newBP_minField").disabled)
								    return _("Please submit a valid number")
						    },
						    disabled: presets ? presets.type != "MIN" : true
					    },{
						    xtype:'numberfield',
						    width:30,
						    allowBlank:false,
						    value: presets ? presets.prio : '0',
						    name:'newBP_prio',
						    fieldLabel: _('Priority')
					    }]
				    }],
				    buttons:[{
					text: _('Close'),
					iconCls: 'icinga-icon-close',
					handler: function(btn) {

					    btn.ownerCt.ownerCt.ownerCt.container.remove();
					},
					scope: this
				    },{
					    text:(presets ? 'Edit' : 'Add')+' Process',
					    iconCls:'icinga-icon-add',
					    id: curId+'_btn',
					    handler:function(btn) {
						    var cmp = Ext.getCmp(curId);
						    var values = cmp.getForm().getFieldValues();
						    var success = false;
						    //
						    if(presets) {
							success = this.editBusinessProcessNode(target,values);

						    } else {

							var field = Ext.getCmp(curId+"_newBP_fs");
							var valid = true;
							field.cascade(function() {
								if(!valid)
									return false;

								if(this.validate)
									valid = this.validate();
							})
							if(!valid)
								return false;
							success = this.createNewProcessNode(target,tree,values);


						    }
						    if(success) {
							    // hangle up the DOM tree until the layer is reached and remove it
							    var owner = btn.ownerCt;
							    while(owner.ownerCt)
								    owner = owner.ownerCt;
							    owner.container.remove();
						    }
					    },
					    scope:this
				    }]
				})]
			}
			var item =  this.createFunkyInputLayer(event.getPageX(),event.getPageY(),330,400,bpCfg)
			item.show();
		},
		
		addExistingBusinessProcess: function(node,target,event,presets) {
		    var curId = Ext.id("","bpAdder");

		    var tree = target.getOwnerTree();
		    var bpCfg = {
			items: [{
			    xtype: 'container',
			    layout: 'fit',
			    html:'<h1 style="margin:2px;font-size:12px;">'+(presets ? 'Edit' : 'Add')+' Business Process</h1>'
			},
			new Ext.form.FormPanel({
			    id:curId,
			    padding:5,
			    height:350,
			    autoScroll:true,
			    items: [{
				xtype:'fieldset',
				id: curId+"_existingBP_fs",

				forceLayout:true,
				items: [{
				    xtype:'combo',
				    id: curId+"_existingBP_name",
				    name:'existingBP_name',
				    store: new Ext.data.JsonStore({
					    fields: ['bp_name','bp_node'],
					    idIndex:0,
					    autoDestroy:true,
					    data:this.getBusinessProcessList(target)
				    }),
				    mode:'local',
				    width:130,
				    allowBlank:false,
				    forceSelection:true,
				    displayField:'bp_name',
				    hideParent:true,
				    forceLayout:true,
				    valueField:'bp_name',
				    triggerAction:'all',
				    fieldLabel: _('Process')
				}],
				listeners: {
				    show: function() {
					Ext.getCmp(curId+"_existingBP_name").show();
					this.doLayout();
				    }
				},
				scope:this
			    }],
			    buttons:[{
				text: _('Close'),
				iconCls: 'icinga-icon-close',
				handler: function(btn) {

				    btn.ownerCt.ownerCt.ownerCt.container.remove();
				},
				scope: this
			    },{
				text:(presets ? 'Edit' : 'Add')+' Process',
				iconCls:'icinga-icon-add',
				id: curId+'_btn',
				handler:function(btn) {
					var cmp = Ext.getCmp(curId);
					var values = cmp.getForm().getFieldValues();
					var success = false;
					//
					if(presets) {
					    success = this.editBusinessProcessNode(target,values);

					} else {
					    var cmp = Ext.getCmp(curId+"_existingBP_name");
					    if(!cmp.validate())
						    return false;
					    success = this.createBusinessNodeAlias(target,tree,cmp.getValue());
					}
					if(success) {
					    // hangle up the DOM tree until the layer is reached and remove it
					    var owner = btn.ownerCt;
					    while(owner.ownerCt)
						    owner = owner.ownerCt;
					    owner.container.remove();
					}
				},
				scope:this
			    }]
			})]
		    }
		    var item =  this.createFunkyInputLayer(event.getPageX(),event.getPageY(),330,400,bpCfg)
		    item.show();
		},
		editBusinessProcessNode: function(node,values) {
			var tree = node.getOwnerTree();
			var oldname =  node.attributes.bpName;
			var pn = node.parentNode;
			AppKit.log(values);
			var alteredNodeAttributes = {
				nodeText : values.newBP_name || values.bpName,
				bpName: values.newBP_name || values.bpName,
				bpLongName: values.newBP_longName || values.bpLongName,
				bpStatus: values.newBP_status || values.bpStatus,
				bpTemplate: values.newBP_template || values.bpTemplate,
				type: values.newBP_type || values.type,
				loaded:true,
				min: values.newBP_min || values.min,
				prio: values.newBP_prio || values.prio,
				iconCls: Cronk.bp.processElements.prototype.getIconCls('bp'),
				uiProvider: Ext.ux.tree.TreeGridNodeUI,
				bpType: 'bp',
				leaf: false
			}

			Ext.apply(node.attributes,alteredNodeAttributes);
			node.setText(alteredNodeAttributes.nodeText);
			//rename aliases
			var alias;
			if(oldname != values.newBP_name) {
				do {
				 	alias = this.getProcessByName(oldname,node.getOwnerTree())
				 	if(alias) {
						var aliasParent = alias.parentNode;
						aliasParent.removeChild(alias);
				 		this.createBusinessNodeAlias(aliasParent,tree,values.newBP_name);
				 	}
				 	
				} while(alias);
			}
			
			return true;
		},
		
		createNewProcessNode: function(target,tree,values) {
	
			var node = tree.loader.createNode({
				nodeText : values.newBP_name || values.bpName,
				bpName: values.newBP_name || values.bpName,
				bpLongName: values.newBP_longName || values.bpLongName,
				bpStatus: values.newBP_status || values.bpStatus,
				bpTemplate: values.newBP_template || values.bpTemplate,
				type: values.newBP_type || values.type,
				loaded:true,
				min: values.newBP_min || values.min,
				prio: values.newBP_prio || values.prio,
				iconCls: Cronk.bp.processElements.prototype.getIconCls('bp'),
				uiProvider: Ext.ux.tree.TreeGridNodeUI,
				bpType: 'bp',
				leaf: false
			});
			
			target.appendChild(node);
			if(values.children) {
				Ext.each(values.children, function(child) {
					if(child.service) {
						var servicenode = tree.loader.createNode({
							uiProvider: Ext.ux.tree.TreeGridNodeUI,
							nodeText: child.host+" : "+child.service,
							isAlias:true,
							host: child.host,
							service: child.service,
							iconCls: Cronk.bp.processElements.prototype.getIconCls('service'),
							nodeType:'node',
							bpType: 'service',
							loaded:true,
							leaf: true
						});
						node.appendChild(servicenode);
					} else if(child.isAlias) {
						this.createBusinessNodeAlias(node,tree,child.bpName)	
					} else if(child.bpName) {
						this.createNewProcessNode(node,tree,child);
					}
					
				},this);
			}
			
			return node;
		},
		
		createBusinessNodeAlias: function(target,tree,bp_name) {
			var node = tree.loader.createNode({
				nodeText :bp_name,
				bpName: bp_name,
				isAlias:true,
				loaded:true,
				uiProvider: Ext.ux.tree.TreeGridNodeUI,
				iconCls: Cronk.bp.processElements.prototype.getIconCls('bp'),
				bpType: 'bp',
				leaf: true
			});
			target.appendChild(node);
			
			return node;
		},
		
		addService: function(node,target,event,presets) {
			var curId = Ext.id("","serviceAdder");
			var tree = target.getOwnerTree();
			var serviceCfg = {
				items: [{
					xtype: 'container',
					layout: 'fit',
					html: '<h1 style="margin:2px;font-size:12px;">'+(presets ? 'Edit' : 'Add')+'Service</h1>'
				},new Ext.form.FormPanel({
					padding:5,
					id: curId+"_form",
					items:[{
						xtype:'combo',
						id: curId+"_hostPanel",
						fieldLabel: _('Host'),
						name:'host_name',
						forceSelection:true,
						store: this.hostStore,
						allowBlank:false,
						width:150,
						value: presets ?  presets.host : null, 
						triggerAction: 'all',
						mode: 'remote',
						displayField: 'HOST_NAME',
						valueField: 'HOST_NAME',
						listeners: {
							select: function(sel,rec) {
								var combo = Ext.getCmp(curId+"_servicePanel");
								this.serviceStore.setBaseParam("filters[0][column]","HOST_NAME")
								this.serviceStore.setBaseParam("filters[0][relation]","=")
								this.serviceStore.setBaseParam("filters[0][value]",rec.get('HOST_NAME'))
								combo.setDisabled(false);
								combo.clearValue();
							},				
							scope: this
						}
					},{
						xtype:'combo',
						id: curId+"_servicePanel",
						fieldLabel: _('Service'),
						name:'service_name',
						iconCls:'icinga-icon-cog',
						forceSelection:true,
						store: this.serviceStore,
						allowBlank:false,
						width:150,
						value: presets ?  presets.service : null, 
						triggerAction: 'all',
						disabled:presets ? false : true,
						mode: 'remote',
						displayField: 'SERVICE_NAME',
						valueField: 'SERVICE_NAME',
						listeners: {
							select: function(sel,rec) {
								Ext.getCmp(curId+'_btn').setDisabled(false);
							},
							beforequery: function(qe){
								delete qe.combo.lastQuery;
							}					
						}
					}],
					buttons:[{
						text: _('Close'),
						iconCls: 'icinga-icon-close',
						handler: function(btn) {

						    btn.ownerCt.ownerCt.ownerCt.container.remove();
						},
						scope: this
					    },{
						text:(presets ? 'Edit ' : 'Add ')+' Service',
						iconCls:'icinga-icon-add',
						id: curId+'_btn',
						disabled: true,
						handler:function(btn) {
							var form = Ext.getCmp(curId+"_form");
							var bForm = form.getForm();
							if(!bForm.isValid())
								return false;
							var ids = bForm.getFieldValues();
							var values = bForm.getValues();
							if(presets) {
								target.setText(values.host_name+" : "+values.service_name);
								Ext.apply(target.attributes,{
									nodeText: values.host_name+" : "+values.service_name,
									loaded:true,
									host: values.host_name,
									service: values.service_name
		
								})
								
							} else {
								var node = tree.loader.createNode({
									uiProvider: Ext.ux.tree.TreeGridNodeUI,
									nodeText: values.host_name+" : "+values.service_name,
									host: values.host_name,
									service: values.service_name,
									isAlias:true,
									iconCls: Cronk.bp.processElements.prototype.getIconCls('service'),
	    							nodeType:'node',
	    							bpType: 'service',
	    							loaded:true,
									leaf: true
								});
								target.appendChild(node);
								
							}
							// hangle up the DOM tree until the layer is reached and remove it
							var owner = this.ownerCt;
							while(owner.ownerCt)
								owner = owner.ownerCt;
							owner.container.remove();
						}
					}]
				})]
			}
			var item =  this.createFunkyInputLayer(event.getPageX(),event.getPageY(),300,130,serviceCfg)
			item.show(true);
			
		},
		
		getConfigJson : function (filename, callback, scope) {
			Ext.Ajax.request({
				url: '<?php echo $ro->gen("modules.cronks.bpAddon.configParser") ?>',
				params: {
					action: 'parseCfg',
					filename: filename
				},
				success: function(resp) {
					var data = Ext.decode(resp.responseText);
					root.removeAll();
					for(var i =0;i<data.length;i++) {
						var node = data[i];
						this.createNewProcessNode(root,root.getOwnerTree(),node);					
					}
					
					if (Ext.isFunction(callback)) {
						callback.call(scope || {});
					}
				},
				failure: function(resp) {
					Ext.Msg.alert(_("An error occured"),resp.responseText);
				},
				scope:this
			});
		},
		removeConfig: function (filename) {
			Ext.Msg.confirm(_("Removing ")+filename,_("Are you sure you want to delete ")+filename+"?",function(btn) {
				if(btn != 'yes') 
					return false;
				Ext.Ajax.request({
					url: '<?php echo $ro->gen("modules.cronks.bpAddon.configParser") ?>',
					params: {
						action: 'removeCfg',
						filename: filename
					},
					success: function(resp) {
						Cronk.bp.configFileListing.store.load();
					},
					failure: function(resp) {
						Ext.Msg.alert(_("An error occured"),resp.responseText);
					},
					scope:this
				});
			});
		},
		createFunkyInputLayer: function(x,y,w,h,cfg) {
			var funkyInputLayer = new Ext.Layer({
				shadow:!AppKit.util.fastMode(),
				constrain:true
			});
			funkyInputLayer.setBounds(x,y,w,0);
			funkyInputLayer.setStyle({
				'-moz-border-radius': '5px',
				'-webkit-border-radius': '5px',
				'border': '1px solid #cecece',
				'background-color':'#dedede',
				'padding' : '5px',
				'overflow' : 'hidden'
			});
			funkyInputLayer.on("DOMNodeInserted",function() {(function() {this.setHeight(h,!fastMode)}).defer(100,this)},funkyInputLayer)
			funkyInputLayer.container = new Ext.Container(Ext.apply({
				renderTo:funkyInputLayer,
				layout:'form'
			},cfg));

			/*Ext.EventManager.on(document,"mousedown",function(e,t) {
				
				if(!e.within(funkyInputLayer)) {
					if(Ext.DomQuery.is(t,"div.x-combo-list-item") || Ext.DomQuery.is(t,"div.x-combo-list-inner"))				
						return true;

					funkyInputLayer.remove();
				}
			})*/
			return funkyInputLayer;
		},
		
		createConfigForTree: function(filename) {
			var tree = Cronk.bp.curTree;
			var root = tree.getRootNode();
			if(!root.hasChildNodes()) {
				Ext.Msg.alert(_("Error"),_("Can't parse an empty tree"));
				return false;
			}
			var errors = this.checkTreeConsistencyErrors(tree);
			if(errors) {
				var errorMsg = "";
				Ext.each(errors,function(errorObj) {
					errorMsg += "<li>In "+errorObj.bp+" : "+errorObj.msg+"</li>";
				})
				Ext.Msg.alert(_("Invalid config"),_("There some errors in your tree :<br/><ul>"+errorMsg+"</ul>"))
				return false;				
			}
			var json = this.buildJsonFromTree(tree);

			Ext.Ajax.request({
				url: '<?php echo $ro->gen("modules.cronks.bpAddon.configParser") ?>',
				params: {
					action: filename ? 'parseJSON_save' : 'parseJSON_show',
					json: json,
					filename: filename
				},
				success: function(resp) {
					if(!filename) {
						var decoded = Ext.decode(resp.responseText);
						var cfgBox = "<div style='width:500px;height:200px;font-size:9px;overflow:scroll;font-family:monospace,arial;background-color:white;border:1px solid #acacac'>";
						cfgBox += "<pre>"+decoded.config+"</pre>";
						cfgBox += " </div>";
						if(decoded.errors) { 
							cfgBox += 
							 	"<div style='margin:auto;text-align:center;width:150px;background-color:red;border:1px solid black;padding:3px'>Consistency errors!</div>"+
						 		"<br/><div style='width:500px;height:50px;color:red;overflow:scroll;font-size:9px;font-family;:monospace;background-color:white;border:1px solid #acacac'>"+
							 		"<pre>"+decoded.errors+"</pre>"+
					 			"</div>"
						} else  {
							cfgBox += 
								"<div style='margin:auto;text-align:center;width:100px;background-color:green;border:1px solid black;height:15px;padding:3px'>ALL FINE!</div>";
						}
						Ext.Msg.alert(_("Config file created"),_("The current config generated from the tree:<br/>")+cfgBox);
					} else {
						Ext.Msg.alert(_("Config file created"),_("Config file created sucessfully"))
						Cronk.bp.configFileListing.store.load();
					}
				},
				failure: function(resp) {
					Ext.Msg.alert(_("An error occured"),resp.responseText);
				}
			});
		},
		
		checkTreeConsistencyErrors : function(tree) {
			var root = tree.getRootNode();
			var errors = []
			root.cascade(function(node) {
				if(!node.attributes)
					return true;
				if(node.attributes.service || node.attributes.isAlias)
					return true;
				if(!node.hasChildNodes())
					errors.push({bp:node.attributes.nodeText,msg:_('Process has no services/subprocesses attached')});
			
			});
			if(Ext.isEmpty(errors))
				return false;
			return errors;	
		},
		
		buildJsonFromTree: function(tree) {
			var root = tree.getRootNode();
			var jsonObj = this.treeToObj(root,true);

			return Ext.encode(jsonObj);
		},
		
		treeToObj: function(node,root) {
			var obj = {};
			var parseAttrs = [
				"bpName","isAlias","bpLongName","bpStatus","bpTemplate","min","prio","type","service","host"
			]
			if(!root)
				Ext.copyTo(obj,(node.attributes ? node.attributes : node.attributes.serviceInfo),parseAttrs);
			
			obj.children = [];
			node.eachChild(function(childNode) {
				if(childNode.attributes.nodeText)
					obj.children.push(this.treeToObj(childNode))
			},this)
			
			return obj;
		}
		
	}));
	
	Cronk.bp.configFileListing = new (Ext.extend(Ext.DataView,{
		autoScroll: true,
		store: new Ext.data.JsonStore({
		    autoLoad:true,
		    url: '<?php echo $ro->gen("modules.cronks.bpAddon.configParser") ?>',
		    baseParams: {
		    	action: 'getConfigList'
		    },
		    fields: [
		    	'filename', 
		    	{name:'created',type:'date',dateFormat:'timestamp'},
		    	{name:'last_modified',type:'date',dateFormat:'timestamp',format: "Y-m-d H:i:s"}
		    ]
		}),
		tpl: new Ext.XTemplate(
			'<tpl for=".">',
			'<div class="bp_cfgPanel" >',
				'<div class="bp_thumb">',
				'</div>',
				'<span>{filename}</span>',
			'</div>',
			'</tpl>'),
		multiSelect: false,
		itemSelector: 'div.bp_cfgPanel',
		overClass: 'x-over',
		emptyText: _('No configs exist yet'),
		listeners: {
			click: function(dview,idx,node,event) {
				var clicked = dview.getRecord(node);
				(new Ext.menu.Menu({
					items: [{
						iconCls: 'icinga-icon-page-edit',
						text: _('Edit this config'),
						handler: function() {
							Cronk.bp.processController.processLoad(clicked.get('filename'));
						}
					},{
						iconCls: 'icinga-icon-delete',
						text: _('Remove this config'),
						handler: function() {
							Cronk.bp.processController.removeConfig(clicked.get('filename'));
						}
					}]
				})).showAt(event.getXY());
			},
			scope:this
		}
	}))();
	
	Cronk.bp.processElements = Ext.extend(Ext.grid.GridPanel,{
		
		getIconCls: function(v) {
			
			switch(v) {
				case 'logic':
					return 'icinga-icon-bricks';
				case 'bp':
					return 'icinga-icon-chart-organisation';
				case 'service':
					return 'icinga-icon-cog';
				default:
					return 'icinga-icon-brick';
			}	
		},
		
		constructor: function(cfg) {
			cfg = cfg || {};

			Ext.apply(cfg,{
				enableDragDrop: true,
				autoDestroy: true,
				ddGroup:'filterEditor',
				store: new Ext.data.ArrayStore({
					fields: ['name','bpType'],
					idIndex: 0,
					data: this.getAvailableFiltersArray()
				}),
				selModel: new Ext.grid.RowSelectionModel({
					singleSelect:true
				}),
				colModel: new Ext.grid.ColumnModel({
					
					columns: [{
						header:_(''),
						dataIndex: 'bpType',
						menuDisabled:true,
						
						renderer: {
							fn: function(value, metaData) {
								
								metaData.css = this.getIconCls(value),
								value = ''
								return value;
							},
							scope:this
						},
						width:16
						
					},{
						header: _('Type'),
						dataIndex: 'name'
					}]
				
				})
			});
			Ext.grid.GridPanel.prototype.constructor.call(this,cfg);
		},
		getAvailableFiltersArray: function(record) {
			var basic = [
/*				['AND','logic'],
				['OR','logic'],
				['NOT','logic'],
				['x OF','logic'],*/
				['Business Process','bp'],
				['Service','service']
			];
			
			return basic;
		}
	});
	Cronk.bp.curTree = null;
	Cronk.bp.processEditor = Ext.extend(Ext.Panel,{
		constructor: function(cfg) {
			Cronk.bp.curTree = new Cronk.bp.processTree();
			cfg = cfg || {};
			borders:false,
			
			cfg.items = {
				autoHeight:true,
				layout:'column',
				defaults: {
					borders:false,
					layout: 'fit'
				},
				items:	[{
					xtype:'panel',
					layout: 'fit',
					height:500,
					columnWidth:.8,
					items: Cronk.bp.curTree
				},{
					xtype:'panel',
					layout: 'fit',
					height:500,					
					columnWidth:.2,
					items: new Cronk.bp.processElements()
				}]
			}
			Ext.Container.prototype.constructor.call(this,cfg);
		}

	});
	
	var bpManager = new Ext.Panel({
		height:parentCmp.getInnerHeight()*0.98,
		width:parentCmp.getInnerWidth()*0.98,
		layout: 'border',
		
		defaults : {
			split:true
		},
		
		stateful: false,
		stateid: stateId,
		
		getState: function() {
			return {
				configName: Cronk.bp.processController.curConfigName
			}
		},
		
		applyState: function(state) {
			if (Ext.isObject(state) && !Ext.isEmpty(state.configName)) {
				Cronk.bp.processController.processLoad(state.configName);
			} 
		},
		
		items: [{
			xtype: 'panel',
			region:'center',
			layout:'fit',
			height:parentCmp.getInnerHeight(),
			title: _('Editor'),
			items: new Cronk.bp.processEditor(),
			buttons: [{
				text: _('Show Config'),
				iconCls: 'icinga-icon-zoom',
				handler: function(btn) {
					Cronk.bp.processController.createConfigForTree();
				},
				scope:this
			},{
				text: _('Save'),
				iconCls: 'icinga-icon-disk',
				handler: function(btn) {
				    Cronk.bp.processController.processSave();
				},
				scope:this
			},{
				text: _('Save as'),
				iconCls: 'icinga-icon-disk',
				handler: function(btn) {
					Cronk.bp.processController.processSaveAs();
				},
				scope:this
			}, {
				text: _('New'),
				iconCls: 'icinga-icon-add',
				handler: function(btn) {
					Cronk.bp.processController.processNew();
				}	
			}]
		},{
			xtype: 'panel',
			region:'east',
			layout:'fit',
			collapsible:true,
			width:200,
			height:parentCmp.getInnerHeight(),
			title: _('Available configs'),
			autoScroll:true,
			items: Cronk.bp.configFileListing
		}]
	});
	
	bpManager.on('render', function(panel) {
		panel.applyState(Ext.state.Manager.get(stateId));
	});
	
	parentCmp.on("resize", function() {
		this.setHeight(parentCmp.getInnerHeight()*0.98);
		this.setWidth(parentCmp.getInnerWidth()*0.98);
		this.doLayout();
	},bpManager);
	
	this.add(bpManager);

	this.doLayout();
});
</script>
