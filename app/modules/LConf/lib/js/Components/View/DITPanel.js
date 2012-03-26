Ext.ns("LConf.View").DITPanel = Ext.extend(Ext.TabPanel,{
    autoDestroy: true,
    resizeTabs:true,
    dnSearchField:null,
    bbar: null,
    swapConnection: null,
    urls: {},
    eventDispatcher: null,
    defaults : {
        closable: true
    },

    constructor: function(cfg) {
        
        Ext.apply(this,cfg);
        
        this.dnSearchField = new LConf.DIT.DNSearchField(cfg);
        this.bbar = new Ext.Toolbar({
            items:['->',this.dnSearchField]
        });

        Ext.TabPanel.prototype.constructor.apply(this,arguments);
        this.swapConnection = new Ext.util.DelayedTask(
            function(tab) {
                this.setActiveTab(tab.idx);
            }
        );

    },

    initTab : function(item,index) {
        var before = this.strip.dom.childNodes[index],
        p = this.getTemplateArgs(item),
        el = before ?
             this.itemTpl.insertBefore(before, p) :
             this.itemTpl.append(this.strip, p),
        cls = 'x-tab-strip-over',
        tabEl = Ext.get(el);

        tabEl.hover(function(){
            if(!item.disabled){
                tabEl.addClass(cls);
            }
        }, function(){
            tabEl.removeClass(cls);
        });

        if(item.tabTip){
            tabEl.child('span.x-tab-strip-text', true).qtip = item.tabTip;
        }
        item.tabEl = el;

        // Route *keyboard triggered* click events to the tab strip mouse handler.
        tabEl.select('a').on('click', function(e){
            if(!e.getPageX()){
                this.onStripMouseDown(e);
            }
        }, this, {preventDefault: true});

        item.on({
            scope: this,
            disable: this.onItemDisabled,
            enable: this.onItemEnabled,
            titlechange: this.onItemTitleChanged,
            iconchange: this.onItemIconChanged,
            beforeshow: this.onBeforeShowItem
        });
        new Ext.dd.DropZone(tabEl,{
            srcScope : this,
            ddGroup:'treenodes',
            getTargetFromEvent: function(e) {
                return {el:tabEl,idx:index};
            },

            onNodeEnter: function(node) {
                this.srcScope.swapConnection.delay(600,null,this.srcScope,[node]);
            },
            onNodeOut : function() {
                this.srcScope.swapConnection.cancel();
            }

        });
    },
    

    initEvents: function() {
        Ext.TabPanel.prototype.initEvents.apply(this,arguments);
        this.on({
            "tabchange": function(ac) {
                if(!ac.activeTab) {
                    this.dnSearchField.connId = null;
                    return false;
                }

                this.dnSearchField.connId = ac.activeTab.connId;
                return true;
            },
            "scope":this
        });
        this.setupConnectionOpenEvent();
    },

    setupConnectionOpenEvent: function() {
        this.eventDispatcher.addCustomListener("ConnectionStarted",function(connObj) {
            var tree = new LConf.DIT.DITTree({
                enableDD:true,
                id:connObj.id,
                title:connObj.connectionName,
                eventDispatcher: this.eventDispatcher,
                urls: this.urls,
                filterState: this.filterState,
                icons: this.icons,
                wizards: this.wizards,
                presets: this.presets
            });
            new Ext.tree.TreeSorter(tree);
            LConf.Helper.Debug.d("Connection opened",connObj);
            tree.connId = connObj.id;
            this.add(tree);
            this.setActiveTab(connObj.id);
            this.doLayout();

            tree.setRootNode(
                new Ext.tree.AsyncTreeNode({
                    id:connObj.rootNode,
                    leaf:false,
                    iconCls:'icinga-icon-world',
                    text: connObj.rootNode
                })
            );

            var that = this;
            (function() {
                that.eventDispatcher.fireCustomEvent("TreeReady",tree)
            }).defer(400);
        },this);

    }
});
