Ext.ns("LConf.View").PropertyManager = Ext.extend(Ext.grid.EditorGridPanel,{
	activeDN :  null,
	minHeight: 200,
	forceFit:true,
	ctCls: 'lconfGrid',
    parentCmp: null,
    root: 'properties',
    id: 'properties_grid',
    
	constructor: function (config) {
		config.sm = new Ext.grid.RowSelectionModel();
		Ext.apply(this,config);
		this.initializeGridSettings();
		Ext.grid.EditorGridPanel.prototype.constructor.call(this,config);

	},

	/**
	* http://extjs.com/forum/showthread.php?t=22218
	* For non-IE browsers, this is fixed with a CSS addition.
	*/
	reenableTextSelection : function(){
		if(Ext.isIE){
            try {
                this.colModel.store.on("load", function(){
                    try {
                        var elems=Ext.DomQuery.select("div.dnSelectable", this.parentCmp.el.dom);
                        for(var i=0, len=elems.length; i<len; i++){
                            elems[i].unselectable = "off";
                            elems[i].parentNode.unselectable = "off";

                        }
                    }catch(e) {}
                 });

            } catch(e) {
                // ignore any errors, it's better to have no text selection in this case
                // than crashing the whole UI
                AppKit.log(e);

            }
		}
	},


	viewConfig: {
		getRowClass: function (record,index) {
			if(record.get('parent') != "") {
				return 'inherited';
			}
            return "";
		}
	},

	connId: null,

    getColumnDefinitions: function() {
        var columns = [
            {
                id:'property',
                header:'Property',
                width:300,
                sortable:true,
                dataIndex:'property',
                editor:Ext.form.TextField
            },{
                id:'value',
                header:'Value',
                width:400,
                sortable:false,
                dataIndex:'value',
                editor:Ext.form.TextField,
                renderer:function(value,metaData,record) {
                    if(record.get('property') == "dn")
                        value = "<div class='dnSelectable'>"+value+"</div>";

                    return value;
                }
            }
        ];
        var extender = new LConf.PropertyGrid.PropertyGridExtender(columns);
        extender.extendColumns();
        return columns;
    },

	initializeGridSettings: function () {
        LConf.Helper.Debug.d("Setting up PropertyManager \w",this,arguments);
		this.ds =  new Ext.data.JsonStore({
			proxy: new Ext.data.HttpProxy({
				url : this.urls.modifyproperty,
				api: {
                    read: this.urls.properties
                }
			}),

			autoSave: false,
			storeId: Ext.id()+'_store',
			root: this.root,
			baseParams: {
			    'connectionId' : this.connId
			},

			idProperty: 'id',
			fields: ['id','property','parent','value'],
			writer: new Ext.data.JsonWriter({
				encode:true,
				writeAllFields:true,
				autoSave:true
			}),
			listeners: {

			    beforesave: function () {
				//	LConf.LoadingLayer.show();
			    },
			    save : function () {
				//	LConf.LoadingLayer.hide();
			    },
			    exception: function () {
				//	LConf.LoadingLayer.hide();
			    }
			}
		});


		this.colModel = new Ext.grid.ColumnModel({
			store:this.ds,
			isCellEditable : function (col,row) {
				if(this.store.getAt(row).id == 'dn'
                        || this.store.getAt(row).id == 'dn_dn'
                        || this.store.getAt(row).get('parent')) {
					return false;
                }
				return  Ext.grid.ColumnModel.prototype.isCellEditable.call(this,col,row);
			},
			columns: this.getColumnDefinitions()
		});

		this.fbar = new Ext.Toolbar({
			disabled:!this.enableFb,
			items:[{
				xtype: 'button',
				text: _('Add property'),
				iconCls: 'icinga-icon-add',
				handler: function () {
					var record = Ext.data.Record.create(['id','property','value']);
					this.getStore().add(new record());
				},
				scope: this
			},{
				xtype: 'button',
				text: _('Remove properties'),
				iconCls: 'icinga-icon-delete',
				handler: this.clearSelected,
				scope: this
			},{
				xtype:'tbseparator'
			},{
				xtype:'button',
				text: _('Save Changes'),
				iconCls: 'icinga-icon-disk',
				handler: function () {
					if(!this.validate())
						return false;
					this.getStore().save();
					this.eventDispatcher.fireCustomEvent("refreshTree");
                    return true;
				},
				scope:this
			}]
		});
		if(this.parentNode) {
			this.ds.setBaseParam("parentNode",this.parentNode);
        }
		this.reenableTextSelection();
	},

	validate: function () {
		var store = this.getStore();
		var valid = true;
		store.each(function(rec) {
			var prop = rec.get("property");

			if(!rec.data.value) {
				Ext.Msg.alert(_("Invalid property set"),
					_("Please submit a value for ")+prop);
				valid = false;
				return false;
			}
			return true;
		})
		return valid;
	},

	inheritedMenu: function (grid,idx,event) {
		var record = this.getStore().getAt(idx);
		if(!record.get("parent"))
			return true;

		var ctx = new Ext.menu.Menu({
			items: [{
				iconCls: 'icinga-icon-arrow-redo',
				text:_('Jump to inherited node'),
				handler: function () {
                    this.eventDispatcher.fireCustomEvent("searchDN",record.get("parent"));
                }
			}]
		});
		ctx.showAt(event.getXY());
		return true;
	},

	initEvents: function (){
		Ext.grid.EditorGridPanel.prototype.initEvents.call(this);

		if(!this.noLoad) {
			this.eventDispatcher.addCustomListener("nodeSelected",this.viewProperties,this,{buffer:true});
            this.on("rowclick",this.inheritedMenu,this);    
        }
		
        this.eventDispatcher.addCustomListener("ConnectionClosed",this.disable,this);
		this.eventDispatcher.addCustomListener("invalidNode",this.disable,this);
		
		
		this.getStore().on("add",function (store,rec,index) {this.getSelectionModel().selectLastRow();},this)
		this.getStore().on("load", this.getDNFromRecord,this);
		this.getStore().on("exception",function (proxy,type,action,opt,resp,arg) {
			if(resp.status != '200') {
				Ext.Msg.alert('Process failed!',Ext.util.Format.ellipsis(resp.responseText,700));
            }
		});
		if(!this.noLoadOnSave) {
			this.getStore().on("save",function () {this.reload()},this.getStore());
        }
        this.on("beforeedit",this.setupEditor,this);

	},


	clearSelected: function () {
		this.getStore().remove(this.getSelectionModel().getSelections());
	},

	disable: function () {
		this.fbar.setDisabled(true);
		this.getStore().removeAll(false);
	},
	getDNFromRecord : function (store,records,options) {
		var activeRecord;

		for(var index in records) {
			activeRecord = records[index];
			// Check if we're on an alias node
			if(!activeRecord.get) {
				this.eventDispatcher.fireCustomEvent("invalidNode");
				break;
			}
			if(!activeRecord.get("property"))
				continue;

			if(activeRecord.get("property").toLowerCase() == "dn") {
				this.activeDN = activeRecord.get("value");
				break;
			}
		}
	},

	setConnectionId: function (connId) {
		this.getStore().setBaseParam('connectionId',connId);
	},

	viewProperties: function (selectedDN,connection,noAsk) {
		var store = this.getStore();
		if(!store)
			return null;
		if(store.modified[0] && !noAsk) {
			Ext.Msg.confirm(_("Unsaved changes pending"),_("Save changes?"),function (btn) {
				if(btn == 'yes') {
					store.save();
					store.on("save",function () {this.viewProperties(selectedDN,connection,true);},this,{single:true});
				} else
					this.viewProperties(selectedDN,connection,true);
			},this);
			return false;
		}
		this.connId = connection;
		this.selectedNode = selectedDN;
		var id = selectedDN.attributes["aliasdn"] || selectedDN.id;
		id = id.replace(/^\*\d{4}\*/,"");

		this.getStore().setBaseParam('node', id);
		this.getStore().setBaseParam('connectionId',connection);
		this.getStore().load();
		this.fbar.setDisabled(false);
	},


	/**
	 * Here's the magic: this function is triggered on beforeEdit and dynamically changes the Editor
	 */
	setupEditor: function (e) {
		var column = e.grid.getColumnModel().columns[e.column];
		var editor = null;

		if(e.field == "property") {
			editor = new AppKit.GridTreeEditorField({
				url: this.urls.ldapmetaprovider,
				grid: this
			});
		} else {
			if(!e.record.get("property")) {
				AppKit.notifyMessage(_("Please select the attribute type first"),"");
				return false;
			}
			var type = e.record.get("property").split("_")[0];
			var objClasses = [];
			for(var i=0;i<e.grid.store.data.items.length;i++) {
				var dataItem = e.grid.store.data.items[i];
				if(dataItem.data.property.toLowerCase() == "objectclass")
					objClasses.push(dataItem.data.value);
			}

			editor = LConf.Editors.EditorFieldManager.getEditorFieldForProperty(type,null,objClasses);
		}

		if(editor.store) {
			editor.store.setBaseParam("connectionId",this.connId || this.store.baseParams.connectionId)
		}

		column.setEditor(editor);
	}
});
