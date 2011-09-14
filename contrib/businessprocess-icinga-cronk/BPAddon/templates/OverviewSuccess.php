<script type="text/javascript">
// This is the init method called when the cronk environment is ready
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {
	Ext.Msg.minWidth = 250;

	var _parent = "<?php echo $rd->getParameter('parentid'); ?>";
	var _filter = "<?php echo $rd->getParameter('p[filters]'); ?>";
	var _bpConfig = "<?php echo $rd->getParameter('p[bpConfig]'); ?>";
	var _bpInfoTabs = <?php echo $rd->getParameter('p[externalTabs]','[]') ?>;
	var _hideConfigSelector = "<?php echo $rd->getParameter('p[bpHideSelector]'); ?>";
	
	var getSelectorConfig = function() {
		if (_hideConfigSelector == 'true') {
			return {
				xtype: 'tbtext',
				text: _('Process') + ': ' + _bpConfig
			};
		}
		else {
			return {
				/**
				 * This combobox allows the user to switch beteween different config files
				 */
				xtype: 'combo',
				width:150,
				value: _('Default cfg'),
				store: new Ext.data.JsonStore({
				    autoLoad:true,
				    url: '<?php echo $ro->gen("modules.bpAddon.configParser") ?>',
				    baseParams: {
				    	action: 'getConfigList'
				    },
				    fields: [
				    	'filename', 
				    	{name:'created',type:'date',dateFormat:'timestamp'},
				    	{name:'last_modified',type:'date',dateFormat:'timestamp',format: "Y-m-d H:i:s"}
				    ]
				}),
				displayField: 'filename',
				valueField: 'filename',
				triggerAction: 'all',
				listeners: {
					render: function(cmb) {
						if(bpLoader.bp_config)
							cmb.setValue(bpLoader.bp_config);						
					},
					select: function(cmb,rec,idx) {
						bpLoader.bp_config = rec.get('filename');
						cmb.ownerCt.ownerCt.handleStateChange();
						root.reload();
					},
					scope:this
				}
			}
		}
	}
	
	/**
	 * Set up state vals and other needed variables like parentCmp
	 */
	this.stateful = true;
	this.stateId = "state_"+this.id;
	var CE = this;
	var parentCmp = this.getParent();
	var fastMode = (Ext.isIE6 || Ext.isIE7 || Ext.isIE8);
	parentCmp.removeAll();

	var filterManager = new Cronk.bp.filterManager({filterString:_filter,icingaApiURL: '<?php echo $ro->gen('icinga.api.output',array("output"=>"json")) ?>'});
    
    var infoPanel = new Cronk.bp.infoPanel({
		containerId: Ext.id('infoPanel_container'),
		icingaApiURL: '<?php echo $ro->gen('icinga.api.output',array("output"=>"json")) ?>',
		tabItems: _bpInfoTabs
	});

   
	/**
	 * Creates a new bpLoader which handles json formatting and tree loading
	 */
	var bpLoader = new Cronk.bp.bpLoader({
		hideChildren:true,
		dataUrl: '<?php echo $t["url"]; ?>',
		uiProviders:{
			'col': Ext.ux.tree.TreeGridNodeUI
		},
		requestMethod:'GET',
		filterManager: filterManager,
		authKey: '<?php echo $t["authToken"]; ?>'
		
	})
	// If there's a config globally written to the cronk, use it 
	if(_bpConfig)
		bpLoader.bp_config = _bpConfig;
	
	/**
	 * The root node of our tree (which isn't visible by default)
	 */
	var root = new Ext.tree.AsyncTreeNode({
		text:'Tasks'
	});
	
	/**
	 * bpTreeGrid is a normal Ext.ux.tree.TreeGrid, except that it handles
	 * applyState correctly
	 */
	var bpTreeGrid = Ext.extend(Ext.ux.tree.TreeGrid,{
		constructor: function(cfg) {
			Ext.apply(this,cfg);
			this.applyState(Ext.state.Manager.get(this.id));
			Ext.ux.tree.TreeGrid.prototype.constructor.call(this,cfg);
		},
		applyState: function(state) {
			if(!state)
				return null;
			if(state.conf)
				bpLoader.bp_config = state.conf;
			
			if(!state.filters)
				return null;
			filterManager.activeFilters = state.filters;

			
		}
	});

	var visWindow = new Ext.Window({
		modal: true,
		width: Ext.getBody().getWidth()*0.9,
		height: Ext.getBody().getHeight()*0.9,
		items : Cronk.bp.processVisualizer.getContainer(),
		renderTo: Ext.getBody(),
		closeAction: 'hide',
		layout: 'fit',
		events : {
			'visualize' : true
		},
		listeners: {
			'visualize' : function(node) {
				AppKit.log(this);
				Cronk.bp.processVisualizer.getContainer().updateContent(node);
				visWindow.show();
			},
			scope: this
		}
	});

	/**
	 * The main GridList 
	 */
	var bpGridList = new bpTreeGrid({
		// autoHeigh wouldn't work correctly for me, so I adjust the size manually
		height:parentCmp.getInnerHeight(),
		rootVisible:false,
		animate:!fastMode,
		stateuid : this.stateuid+"_treegrid",
		id : this.stateuid+"_treegrid",
		stateful:false, // state handling will be done manually 
		tbar: new Ext.Toolbar({
			getPriorityMenuItems: function() {

			},
			items: [{
				/**
				 * Refresh button
				 */
				iconCls: 'icinga-icon-arrow-refresh',
				text: _('Refresh'),
				handler: function() {
					root.reload();
				},
				scope: this
			},{
				/**
				 * Auto Refreh
				 */
				iconCls: 'icinga-icon-cog',
				text: _('Settings'),
				menu: {
					items: [{
						text: _('Auto refresh'),
						checked: false,
	
						checkHandler: function(checkItem, checked) {
							if (checked == true) {
								this.trefresh = AppKit.getTr().start({
									run: function() {
										root.reload();
									},
									interval: 120000,
									scope: this
								});
							}
							else {
								AppKit.getTr().stop(this.trefresh);
								delete this.trefresh;
							}	
						}
					}]
				},
				scope: this
			}, {
				xtype: 'tbseparator'
			},{
				/**
				 * Filter handler
				 */
				text: _('Filter'),
				iconCls: 'icinga-icon-pencil',
				menu: {
					items: [{
						text: _('Edit'),
						iconCls: 'icinga-icon-application-form',
						handler:function() {
							filterManager.filterWindow();
						},
						scope:this
					},{
						text: _('Remove'),
						iconCls: 'icinga-icon-cancel',
						handler:function() {
							filterManager.removeAll();
						},
						scope:this
					}]
				}
			},{
				xtype: 'tbseparator'
			}, getSelectorConfig(), {
				xtype: 'tbseparator'
			},{
				/**
				 * Triggers the withGroups options in the loader, allowing to view
				 * The group type
				 */
				iconCls: 'icinga-icon-sitemap',
				text: _('Show groups'),
				enableToggle: true,
				toggleHandler: function(btn,state) {
					bpLoader.withGroups = state;			
					root.reload();
				}
			},{
				xtype: 'tbseparator'
			},{
				/**
				 * By default, subprocesses will be shown if their parent match 
				 * (even if they are filtered). 
				 * There's a simple reason for that: It makes sense. 
				 */
				text: _('Don\'t show filtered subprocesses'),
				enableToggle: true,
				pressed: true,
				toggleHandler: function(btn,state) {
					bpLoader.hideChildren = state;
					root.reload();
				}
			}]
			
		}),
		columns:[{
			header:_('Name'),
			width: 300 ,
			dataIndex: 'display_name'
		},{
			header: _(''),
			width:25,
			dataIndex: '_',
			cls: 'icinga-icon-structure visualizeBtn'
		},{
			header: _(''),
			width:25,
			dataIndex: '_',
			cls: 'icinga-icon-book historyBtn'
		},{
			header:_('Hardstate'),
			dataIndex: 'hardstate',
			width:100
		},{
			header:_('Host'),
			dataIndex: 'host',
			width:100
		},{
			header:_('Service'),
			dataIndex: 'service',
			width:100
		},{
			header:_('Status information'),
			dataIndex: 'external_info',	
			width:100
		},{
            header: _(''),
            width:25,
            dataIndex: 'info_url'
        	
		},{
			header:_('Priority'),
			dataIndex: 'display_prio',
			width:50
		}],

		root:root,
		
		loader: bpLoader,
		/**
		 * Creates service links via class selectors 
		 * bp_service_selector classes wil be converte to links which point to
		 * a filtered service_history cronk 
		 */
		buildServiceLinks:  function() {
			var service_selector = Ext.DomQuery.jsSelect("span.bp_service_selector");
			Ext.each(service_selector,function(item) {		
				var el = (Ext.get(item));
				// if the link is already created, don't do it again
				if(el.hasLink)
					return true;
				// create link
				el.on("click",function(ev) {
					var service_name = el.getAttribute("service");
					var host_name = el.getAttribute("service");
					var cronk = {
						parentid: Ext.id(),
						title: 'Services for '+service_name,
						crname: 'gridProc',
						closable: true,
						module: 'Cronks',
						action: 'System.ViewProc',
						params: {
							module: 'Cronks',
							action: 'System.ViewProc',
							template: 'icinga-service-template'
						}
					};
					var filter = {};
					filter["f[service_name-value]"] = service_name; 	
					filter["f[service_name-operator]"] = 50;
					
					filter["f[host_name-value]"] = host_name; 	
					filter["f[host_name-operator]"] = 50;

					Cronk.util.InterGridUtil.gridFilterLink(cronk, filter);
				},this)
				el.hasLink = true;
			},this)		
		},
		
		/**
		 * Does the same like @See buildServiceLinks, only with bp_host_selector
		 * and host_history views.
		 * 
		 * Generally, these methods could be put together in one. But as we only
		 * need this two cases, it would generate more work than use. 
		 */
		buildHostLinks:  function(host_selector) {
			host_selector = Ext.DomQuery.jsSelect("span.bp_host_selector");
			Ext.each(host_selector,function(item) {		
				var el = (Ext.get(item));
				if(el.hasLink)
					return true;
				el.on("click",function(ev) {
					var host_name = el.getAttribute("host");
					var cronk = {
						parentid: Ext.id(),
						title: 'Host '+host_name,
						crname: 'gridProc',
						closable: true,
						params: {
							module: 'Cronks',
							action: 'System.ViewProc',
							template: 'icinga-host-template'
						}
					};
					var filter = {};
					filter["f[host_name-value]"] = host_name; 	
					filter["f[host_name-operator]"] = 50;
				
					Cronk.util.InterGridUtil.gridFilterLink(cronk, filter);
				},this)
				el.hasLink = true;
			},this)					
		},
		/**
		* Parses any links provided to iframe providers
		*
		*/	
		buildExternalLinks: function() {
			var link_selector = Ext.DomQuery.jsSelect(".x-treegrid a");
			Ext.each(link_selector,function(item) {	
		
				var el = (Ext.get(item));
				if(!el.getAttribute('href') || el.getAttribute('href')== "#")
					return true;
				var replace = document.createElement("div");		
			
				replace.innerHTML = el.dom.innerHTML || '&nbsp;';
				var replaceEl = Ext.get(replace);
				replaceEl.qtip = el.qtip;
				replaceEl.addClass(el.dom.className);
				var link = el.getAttribute('href');
				replaceEl.on("click",function() {	
					var panel = Ext.getCmp('cronk-tabs');
					var urlTab = panel.add({
						parentid: Ext.id(),
						xtype: 'cronk',
						title: replace.innerHTML+'('+link+')',
						crname: 'genericIFrame',
						closable: true,
						params: {
							module: 'Cronks',
							action: 'System.ViewProc',
							url: link
						}
					});
					panel.doLayout();
					panel.setActiveTab(urlTab);	
				});	
			
				el.replaceWith(replace);
			
			});	
		},

		/**
		 * Aggregate function that creates the links in the tree
		 */
		buildSelectors: new Ext.util.DelayedTask(function(args) {
			this.buildServiceLinks();
			this.buildHostLinks()
			this.buildExternalLinks();
			
	
		},this),
		
		/**
		 * Saves the current state when called
		 */
		handleStateChange: function() {
			Ext.state.Manager.set(this.id,this.getState());
				
			root.reload()
		},
		
		listeners: {
			render:function(cmp) {
				cmp.addEvents({"statechanged" : true});
				// Save the state if a filter changes
				filterManager.addListener("filterChanged",function() {
					if(!filterManager.hasAPIFilter())
				 		cmp.handleStateChange();
					else
						this.getAPIWhitelist(cmp.handleStateChange,this);
				},this);
				
				cmp.treeGridSorter.folderSort = false;
				/**
				 * The original sort function didn't work, so we fix that here.
				 * Extending the class doesn't work, because the TreeGrid overwrites
				 * the loader on construct and therefore it's constructor had to
				 * be rewritten, too. 
				 */
				cmp.treeGridSorter.defaultSortFn = cmp.treeSortFn;
				filterManager.fireEvent("filterChanged");
			},

			/**
			 * If nodes are appended their link selector classes will be converted
			 * If there are for example 500 nodes added, the buildSelectorfunction is buffered
			 * and only called once.
			 */
			append: function(tree,parent,node,idx) {
				try {
					node.on("click",function(dView,e) {
						var target = Ext.get(e.getTarget());
						if(!target)
							return true;
						if(target.hasClass("historyBtn")) {
							Ext.getCmp(infoPanel.containerId).expand();
							infoPanel.fireEvent('displayNode',node)
						} else if(target.hasClass('visualizeBtn'))  {
							visWindow.fireEvent('visualize',node);
						}

					});
					// Build links soon...
					tree.buildSelectors.delay(100,null,tree);
				} catch(e) {AppKit.log(e)}
			},

			/**
			 * Preprocessing before appending nodes (like hiding prio 0)
			 */
			beforeappend: function(tree, _parent, node) {

				if(_parent == root && node.attributes["display_prio"] === '0')
					return false;
				
				if(node.attributes && !node.attributes.ignoreFilter 
								   && !_parent.attributes.showChildren) {
					if(this.checkIfNodeIsFiltered(node.attributes,_parent,true)) {
						if(_parent == root || bpLoader.hideChildren)		
							return false;
					}
				}
			}	
		},

		buildAPIFilterFromObject : function(obj,prefix) {
			var filter = {};
			switch(obj.operator) {
				case 60:
					filter['filters[0][column]'] =  prefix+obj.field.field2 || prefix+obj.field.field;
					filter['filters[0][relation]'] = 'like';
					filter['filters[0][value]'] = '*'+obj.value+'*';
					break;
				case 61:
					filter['filters[0][column]'] =  prefix+obj.field.field2 || prefix+obj.field.field;
					filter['filters[0][relation]'] = 'not like';
					filter['filters[0][value]'] = '*'+obj.value+'*';
					break;
				case 50:
					filter['filters[0][column]'] = prefix+obj.field.field2 || prefix+obj.field.field;
					filter['filters[0][relation]'] = '=';
					filter['filters[0][value]'] = obj.value;
					break;
				case 51:
					filter['filters[0][column]'] = prefix+obj.field.field2 || prefix+obj.field.field;
					filter['filters[0][relation]'] = '!=';
					filter['filters[0][value]'] = obj.value;
					break;
			}
			return filter;
		},

		whiteList : {
			items: [],
			instance: 0,
			cb: function() {},
			semaphore: 0
		},

	
		/**
		 *	Retrieves a list of nodes which could be displayed when filtering only with API
		 *	This list 
		 *
		 **/
		getAPIWhitelist: function(callback,scope) {
			var filters = filterManager.getAPIFilters();

			var instance = ++this.whiteList.instance;
			this.whiteList.items = [];
			// Setting semaphore (*2 because we have to fetch HOST and SERVICE separately).
			this.whiteList.semaphore = filters.length*2;	
			this.whiteList.cb = callback.createDelegate(scope);
			Ext.each(filters,function(filter) {
				this.addAllowedFromAPI(filter,'host','HOST_',instance);
				this.addAllowedFromAPI(filter,'service','SERVICE_',instance);
			
			},this)		
			
		},

		addAllowedFromAPI: function(filter,target,prefix,instance)  {
			var filterParams = this.buildAPIFilterFromObject(filter,prefix);
			if(filter.field.field2) {
				Ext.apply(filterParams,{
					'filters[1][column]' : prefix+filter.field.field,
					'filters[1][relation]' : '=',
					'filters[1][value]' : filter.value2
				});
			}

			Ext.apply(filterParams,{
				'target'	 : target,
				'columns[0]' : prefix+filter.field.field,
				'columns[1]' : prefix+'NAME'
			});
		
			if(filter.field.field2) {
				filterParams["columns[2]"] = prefix+filter.field.field2
				if(target == 'service') // We always need the host name to distinguish between services
					filterParams["columns[3]"] = "HOST_NAME"
			}
			
			Ext.Ajax.request({
				url:filterManager.icingaApiURL,
				params:	filterParams,
				success: function(response) {
					// Ignore different instance returns, this filter is not needed anymore
					if(this.whiteList.instance != instance)
						return true;
					var json = Ext.decode(response.responseText);
					this.whiteListRequestComplete(json,instance);
				},
				failure: function() {
					// Ignore different instance returns, this filter is not needed anymore
					if(this.whiteList.instance != instance)
						return true;
					AppKit.notifyMessage(
						_("Filter error"),
						_("API field ")+filter.field.field+_(" could not be fetched for")+target
					);
					this.whiteListRequestComplete();
				},
				scope:this
			});

		},

		whiteListRequestComplete : function(result,instance) {
			result = result || [];
			if(this.whiteList.instance == instance) {
				this.whiteList.items = this.whiteList.items.concat(result);
				if(!--this.whiteList.semaphore) {
					this.whiteList.cb.defer(200);

				}
			}
		},

		treeSortFn : function(n1, n2) {	
            var dsc = me.dir && me.dir.toLowerCase() == 'desc';
            var p = me.property || 'text';
            var sortType = me.sortType;
            var fs = me.folderSort;
            var cs = me.caseSensitive === true;
            var leafAttr = me.leafAttr || 'leaf';

            if(fs){
                if(n1.attributes[leafAttr] && !n2.attributes[leafAttr]){
                    return 1;
                }
                if(!n1.attributes[leafAttr] && n2.attributes[leafAttr]){
                    return -1;
                }
            }
            // catch errors 
            if(!(n1.attributes && n2.attributes))
            	return 0;
            if(!(n1.attributes[p] && n2.attributes[p]))
            	return 0;
            	
            var v1 = n1.attributes[p].toUpperCase();
            var v2 = n2.attributes[p].toUpperCase();
            if(v1 < v2){
                return dsc ? +1 : -1;
            }else if(v1 > v2){
                return dsc ? -1 : +1;
            }else{
                return 0;
            }
        },

		/**
		 * Checks if the current node is filtered, according to its attributes attr
		 */
		checkIfNodeIsFiltered: function(attrs) {			
			filters = filterManager.getFilters();
			var filterOut = false;
			for(var filter in filters ) {
				if(Ext.isObject(filters[filter].field)) {
					if(this.elemIsInWhitelist(attrs))
						continue;
				}

				if(this.filterMatches(filters[filter],attrs))
					continue;

				var childIsVisible = false;
				// recursively check if a subnode is to be displayed

				if(Ext.isArray(attrs.children)) {
					for(var i=0;i<attrs.children.length;i++) {
						var curChild = attrs.children[i];
						if(!this.checkIfNodeIsFiltered(curChild))
							childIsVisible = true;
					}
				}
				if(!childIsVisible)
					filterOut |= true;
			}
			return filterOut;
 		},

		elemIsInWhitelist: function(elem) {
			var result = false;
			Ext.each(this.whiteList.items,function(item) {
				
				if(item["SERVICE_NAME"]) {
					if(item["SERVICE_NAME"] == elem["service_name"]
						  && item["HOST_NAME"] == elem["host_name"]) {
						 result = true;
					 }
				} else if(item["HOST_NAME"] == elem["host_name"]) {
					result = true;
				}

				return !result;
			},this)

			return result;
		},

		filterMatches: function(filter,obj) {
			var objVal = obj[filter.field];
			var regExp = filter.value;
			if(filter.value2)
				return false;
			switch(filter.operator) {
				case 50:		//is
					return(objVal == filter.value);					
				case 51:		//is not
					return(objVal != filter.value);
				case 60:		//contain
					return(objVal.search(regExp) != -1);
				case 61:		//does not containe
					return(objVal.search(regExp) == -1);
				case 70:		//less than
					return(filter.value > objVal);
				case 71:		//greater than
					return(filter.value < objVal);
				case 'belongs':
					if(obj["display_name"] == filter.value) {
						obj.showChildren = true
						return true;
					}
					obj.showChildren = false;
					return false;
				case 'belongs_not':
					if(obj["display_name"] != filter.value) {
						obj.showChildren = true
						return true;
					}
					obj.showChildren = false;
					return false;
				
			}
			return true;
		},


		/**
		 * Returns the current state of this tree, its filter and the current
		 * config used for the loader
		 */
		getState: function(state) {
			return {
				cmpId: this.id,
				filters: filterManager.getFilters(),
				conf: bpLoader.bp_config,
				sorting: {
					dir: this.treeGridSorter.dir,
					property: this.treeGridSorter.property
				
				}
				
			}
		}
		
	});

	// Notify cronkbuilder for other state object
	this.setStatefulObject(bpGridList);

	// Saved state from the cronk builder
	if (Ext.isObject(this.state)) {
		bpGridList.applyState(this.state);
	}

	this.add({
		xtype:'container',
		layout: 'border',
		width:parentCmp.getInnerWidth()*0.98,
		items: [{
			region:'center',
			xtype:'container',
			layout:'fit',
			border:false,
			items: bpGridList
		},{
			xtype:'panel',
			region:'east',
			width:300,
			collapsible:true,
			split:true,
			collapsed:true,
			layout:'fit',
			id: infoPanel.containerId,
			items: infoPanel
		}]
	});
	this.doLayout.defer(300);
});


</script>
