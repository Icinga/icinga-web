
/*
lconf.ditTreeManager = function(parentId,loaderId) {
	var dataUrl = loaderId;
	var ditPanelParent = Ext.getCmp(parentId);
	if(!ditPanelParent)
		throw(_("DIT Error: parentId ")+parentId+_(" is unknown"));

	var ditTreeLoader =
*/

Ext.ns("LConf.DIT").DITTree = Ext.extend(Ext.ux.MultiSelectTreePanel,{
    eventDispatcher: null,
    wizardManager: null,
    filterManager: null,
    searchReplaceManager: null,
    keyMap: null,
    urls: {},
    presets: {},
    /**
     * Filters that will be applied on reload
     **/
    reloadFilters: null,

    initEvents: function() {
         Ext.ux.MultiSelectTreePanel.prototype.initEvents.call(this);
      
        this.on({
            "beforeclose": this.onClose,
            "click": function(node) {
                this.eventDispatcher.fireCustomEvent("nodeSelected",node,this.id);
            },
            "startdrag": function(tree,node) {
                node.connId = this.connId;
            },
            "beforeNodeDrop": function(e) {
                e.dropStatus = true;
                this.showNodeDroppedDialog(e); // defined in mixin
                return false;
            },
            "contextmenu": function(node,e) {
                this.showGeneralNodeDialog(node,e);
            },
            "beforeappend": function(tree,parent,node) {
                if(!this.checkIfNodeIsSynced(node,parent)) {
                    (function() {node.getUI().addClass("x-node-lconf-unsynced")}).defer(200);
                }
                if(this.getNodeById(node.attributes.dn)) {
                    var rnd = ((Math.floor((Math.random()*10000))+1000)%10000);
                    node.attributes.dn = "*"+rnd+"*"+node.attributes.dn;
                    node.attributes.id = node.attributes.dn;
                    node.id = node.attributes.id;
                }
            },
            "append": function(obj,parent,node) {
                if(node.attributes.match == "noMatch") {
                    (function() {node.getUI().addClass('noMatch');}).defer(100)
                    node.expand();
                }
            },
            scope: this
        });
        this.registerCrossComponentEvents();
        this.setupKeyMap();
    },

    /**
     * Sets up handlers for external eventDispatcher events
     **/
    registerCrossComponentEvents: function() {
        this.eventDispatcher.addCustomListener("filterChanged",function(filters) {
            this.ditLoader.baseParams["filters"] = Ext.encode(filters);
            this.refreshNode(this.getRootNode(),true);
        },this)
        this.eventDispatcher.addCustomListener("refreshTree",function(node) {
            this.refreshNode(this.getRootNode(),true);
        },this);
        this.eventDispatcher.addCustomListener("searchDN",this.searchDN,this);
        this.eventDispatcher.addCustomListener("simpleSearch",function(snippet) {},this);
        this.eventDispatcher.addCustomListener("aliasMode", function(node) {

            this.reloadFilters = this.loader.baseParams["filters"];
            this.ditLoader.baseParams["filters"] = '{"ALIAS":"'+node.id+'"}';
            this.expandAllRecursive(null);
        },this);

    },

    getLastExport: function(parent) {
        try {
            var r = /LCONF->EXPORT->CLUSTER = /i
            for(var i in (parent.attributes.description || {})) {
                if(!parent.attributes.description[i].match(r))
                    continue;
                return parent.attributes.modifytimestamp[0];
            }
            return -1;
        } catch(e) {
            AppKit.log(e);
        }
    },

    checkIfNodeIsSynced: function(node,parent) {
        var r = /.*structuralobject/i
        var lastExport = -1;
        while(parent) {

            for(var i in (parent.attributes.objectclass || {})) {
                if(r.test(parent.attributes.objectclass[i])) {
                    lastExport = this.getLastExport(parent);
                } else {
                    continue;
                }
                var modified = this.getLastExport(node);
                if(modified == -1)
                    return true;

                if(lastExport < modified)
                    return false;
            }
            parent = parent.parentNode;
        }
        return true;
    },

    setupKeyMap: function() {
        this.keyMap = new LConf.DIT.Helper.KeyMap(this);
    },

    initLoader: function() {
        LConf.Helper.Debug.d("Init loader");
        this.loader = new LConf.DIT.DITTreeLoader({
            id:this.id,
            urls: this.urls,
            baseParams:{
                connectionId:this.id,
                filters: this.filterState.getActiveFilters()
            },
            icons: this.icons,
            listeners: {
                beforeload: function(obj,node,cbk) {
                    LConf.Helper.Debug.d("Loader","Beforeload",arguments,this);
                    if(node.id.match(/\*\d{4}\*/)) {
                        this.jumpToRealNode(node);
                        return false;
                    }
                    return true;
                },

                exception: function() {

                },
                scope: this
            }
        });
        this.ditLoader = this.loader; //prevent possible nullpointerexception
    },
    getConnectionId: function() {
        return this.connId
    },
    onClose: function() {
        Ext.Msg.confirm(this.title,_("Are you sure you want to close this connection?"),
            function(btn) {
                if(btn === 'yes') {
                    this.eventDispatcher.fireCustomEvent("ConnectionClosed",this.id);
                    this.destroy()
                }
            },
            this);
        return false;
    },

    constructor: function(cfg) {
        Ext.ux.MultiSelectTreePanel.prototype.constructor.apply(this,arguments);
        if(typeof cfg.urls !== "object") {
            throw("DITTree couldn't be constructed, missing url descriptor");
        }
        if(typeof cfg.eventDispatcher !== 'object') {
            throw("DITTree couldn't be constructed, missing eventDispatcher");
        }
        Ext.apply(this,cfg);
    },

    initComponent: function() {
        Ext.ux.MultiSelectTreePanel.prototype.initComponent.apply(this,arguments);
        this.initLoader();
        this.wizardManager = new LConf.DIT.Helper.NodeWizardManager(this);
        this.searchReplaceManager = new LConf.DIT.Helper.SearchReplaceManager(this);
        this.importMixins();
    },

    importMixins: function() {
        for(var mixin in LConf.DIT.Mixin) {
            var mixinInstance = new LConf.DIT.Mixin[mixin]();
            for(var classElement in mixinInstance) {
                this[classElement] = mixinInstance[classElement];
            }
        }
    },


    autoScroll:true,
    animate:false,
    containerScroll:true,
    minSize:500,
    border:false,
    ddGroup: 'treenodes',
    enableDD: true,
    root: {
        nodeType: 'async',
        disabled:false,
        enableDD:false,
        draggable:false,
        editable:false,
        text: 'Root DSE',
        leaf:true

    }
});


