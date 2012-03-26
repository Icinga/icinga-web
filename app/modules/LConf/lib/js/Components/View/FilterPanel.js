Ext.ns("LConf.View").FilterPanel = Ext.extend(Ext.Panel, {
    
    filterState: null,
    dView : null,
    title: _('Filter'),
    constructor: function(cfg) {
        if(typeof cfg !== "object")
            cfg = {}
        if(typeof cfg.filterState !== "object")
            cfg.filterState = new LConf.Filter.FilterState();

        this.filterState = cfg.filterState;
        cfg.tbar = this.buildTopBar();
        Ext.Panel.prototype.constructor.apply(this,[cfg]);

    },

    initComponent: function() {
        Ext.Panel.prototype.initComponent.apply(this,arguments);
        
        this.add(this.buildFilterListing());
    },

    getTemplate: function() {
        return new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="ldap-filter" ext:qtip="{filter_name}" id="conn_{filter_id}">',
                '<div class="thumb"></div>',
                '<span class="X-editable">{filter_name}</span>',
                '<tpl if="filter_isglobal == 1"> (global)</tpl>',
            '</div>',
            '</tpl>'
        );
    },

    getStore: function() {
        if(this.filterState === null)
            throw ("FilterPanel::getStore called before filterState was ready");
        return this.filterState.getStore();
    },

    buildFilterListing: function() {
        if(this.filterState === null)
            throw ("Invalid state in FilterPanel: FilterListing created before FilterState was available");

        var scopedThis = this;
        this.dView = new Ext.DataView({
            id: 'view-'+this.id,
            store: this.getStore(),
            tpl: this.getTemplate(),
            autoHeight:true,
            
            overClass:'x-view-over',
            multiSelect: false,
            itemSelector:'div.ldap-filter',
            emptyText: _('No filter defined yet'),
            cls: 'ldap-data-view',
            listeners: {
                click: function(view,idx,node,event) {
                    event.preventDefault();
                    var el = new Ext.Element(node);
                    var record = view.getStore().getAt(idx);
                    new Ext.menu.Menu({
                        items: [{
                            text: _('Activate'),
                            iconCls: 'icinga-icon-accept',
                            handler: function() {
                                node.isActivated = true;
                                this.filterState.activateFilter(record.get('filter_id'));
                                el.addClass("isActive");
                            },
                            scope:scopedThis,
                            hidden: node.isActivated || scopedThis.filterState.bypassed
                        },{
                            text: _('Deactivate'),
                            iconCls: 'icinga-icon-stop',
                            handler: function() {
                                node.isActivated = false;
                                this.filterState.deactivateFilter(record.get('filter_id'));
                                el.removeClass("isActive");
                            },
                            scope:scopedThis,
                            hidden: !node.isActivated || scopedThis.filterState.bypassed
                        },{
                            text: _('Edit filter'),
                            iconCls: 'icinga-icon-page-edit',
                            hidden: record.get('filter_isglobal') == '1',
                            handler: function() {
                                scopedThis.filterManager(record);
                            },
                            scope:this
                        },{
                            text: _('Delete'),
                            iconCls: 'icinga-icon-delete',
                            hidden: record.get('filter_isglobal') == '1' || scopedThis.filterState.bypassed,
                            handler: function() {
                                Ext.Msg.confirm(
                                    _("Delete filter"),
                                    _("Do you really want to delete this filter?"),
                                    function(btn) {
                                        if(btn == "yes") {
                                            var store = record.store;
                                            store.remove(record);
                                            store.save();
                                        }
                                    }
                                )
                            },
                            scope:scopedThis
                        }]
                    }).showAt(event.getXY());

                }
            }
        });
        return this.dView;
    },

    buildTopBar: function() {
        return new Ext.Toolbar({
            title: _('Create filter'),
            items: [{
                xtype: 'button',
                iconCls: 'icinga-icon-add',
                text:_('New'),
                handler:function() {this.filterManager()},
                scope:this
            },{
                xtype: 'button',
                enableToggle:true,
                allowDepress: true,
                iconCls: 'icinga-icon-stop',
                text:_('Bypass'),
                scope: this,
                toggleHandler: function(btn,active) {
                    if(this.filterState.active) {
                        this.filterState.bypassAll();
                        this.dView.getEl().addClass('lconf-panel-disabled');
                    } else {
                        this.filterState.removeBypass();
                        this.dView.getEl().removeClass('lconf-panel-disabled');
                    }
                }
           }]
        });
    },

    filterManager: function(record) {
        if(record) {
            this.showFilterManagerWindow(record);
        } else {
            this.showFilterManagerWindow();
        }
    },


    showFilterManagerWindow: function(record) {
        var presets = null;
        var filter_name = null;

        if(record) {
            presets = Ext.decode(record.get('filter_json'));
            filter_name = record.get('filter_name');
        }
        
        var tree = new LConf.Filter.FilterTree({
            presets: presets,
            filter_name: filter_name,
            filterState: this.filterState
        });

        var filterManagerWindow = new Ext.Window({
            modal:true,
            height: Ext.getBody().getHeight()*0.9 > 500 ? 500 : Ext.getBody().getHeight()*0.9,
            autoDestroy: true,
            constrain:true,
            resizable:true,
            autoScroll:true,
            defaults: {
                autoScroll:true
            },
            width:700,
            renderTo: Ext.getBody(),
            layout:'fit',
            items: {
                layout:'column',
                items:	[{
                    title:_('Filter'),
                    columnWidth:.8,
                    items: tree
                },{
                    title:_('Available Elements'),
                    columnWidth:.2,
                    items: tree.getAvailableElementsList(record || false)
                }]
            },
            buttons: [{
                text: _('Save filter'),
                iconCls: 'icinga-icon-disk',
                handler: function(btn) {
                    var obj = tree.treeToFilterObject();
                    if(!obj)
                        return false;
                    Ext.Msg.prompt(
                        _('Save filter'),
                        _('Please enter the name for this filter'),
                        function(pbtn,text) {
                            if(pbtn == 'ok') {
                                this.filterState.saveFilter(obj,text,record);
                                btn.ownerCt.ownerCt.close();
                            }
                        },
                        this,
                        false,
                        filter_name || ''
                    );

                    return true;
                },
                scope: this
            }]
        });
        filterManagerWindow.show();
    }

});
