// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-2015 Icinga Developer Team.
// All rights reserved.
//
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}
/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */
Ext.ns('Icinga.Cronks.System');

(function () {

    "use strict";

    Icinga.Cronks.System.CronkListingPanel = function (c) {

        var CLP = this;

        Icinga.Cronks.System.CronkListingPanel.superclass.constructor.call(this, c);

        this.stores = {};

        this.cronks = {};

        this.categories = {};

        var fillStore = function (storeid, data) {

            if (Ext.isEmpty(CLP.stores[storeid])) {
                CLP.stores[storeid] = new Ext.data.JsonStore({
                    autoDestroy: true,
                    autoLoad: false,
                    root: 'rows',
                    idProperty: 'cronkid',
                    fields: [{
                        name: 'shortname',
                        mapping: 'name',
                        convert: function (v, record) {
                            return Ext.util.Format.ellipsis(v, 15, false);
                        }
                    },

                        'cronkid', 'description', 'module', 'action', 'system', 'owner', 'categories', 'groupsonly', 'state', 'name', {
                            name: 'image_id',
                            convert: function (v, record) {
                                return record.image;
                            }
                        }, {
                            name: 'ae:parameter',
                            convert: function (v, record) {
                                if (!Ext.isObject(v)) {
                                    return v;
                                }
                                for (var i in v) {
                                    if (Ext.isObject(v[i]) || Ext.isArray(v[i])) {
                                        v[i] = Ext.encode(v[i]);
                                    }
                                }
                                return v;
                            }
                        }, {
                            name: 'image',
                            convert: function (v, record) {
                                return AppKit.util.Dom.imageUrl(v);
                            }
                        }, {
                            name: 'statusclass',
                            convert: function (v, record) {
                                var cls = 'cronk-preview';

                                if (record.owner === true) {
                                    cls += ' cronk-item-owner';
                                }

                                if (record.system === true) {
                                    cls += ' cronk-item-system';
                                }

                                if (!record.system && !record.owner) {
                                    cls += ' cronk-item-shared';
                                }

                                return cls;
                            }
                        }
                    ]
                });
            }

            var store = CLP.stores[storeid];

            store.loadData(data);

            // Building a collection of all cronks available
            Ext.iterate(data.rows, function (val, key) {
                Cronk.Inventory.add(val.cronkid, val);
            });
        };

        var createView = function (storeid, title, collapsed) {

            var store = CLP.getStore(storeid);

            // Update css style
            if (!CLP.cronkliststyle) {
                CLP.cronkliststyle = AppKit.getPrefVal('org.icinga.cronk.liststyle') || 'list';
            }

            // Update settings list
            var checkItem = CLP.settingsButton.menu.getComponent('cronkliststyle-menu-' + CLP.cronkliststyle);
            if (checkItem) {
                checkItem.setChecked(true);
            }

            var cls = 'cronk-data-view';

            cls = cls + ' ' + cls + '-' + CLP.cronkliststyle;

            // templates
            var template_text;
            if (CLP.cronkliststyle && CLP.cronkliststyle === 'icon') {
                template_text =
                    '<tpl for=".">' +
                        '<div class="{statusclass}" id="{name}" ext:qtip="{name}: {description}">' +
                        '<div class="cronk-status-icon">' +
                        '<div class="thumb"><img src="{image}"></div>' +
                        '<span class="x-editable">{shortname}</span>' +
                        '</div>' +
                        '</div>' +
                        '</tpl>' +
                        '<div class="x-clear"></div>';
            } else {
                template_text =
                    '<tpl for=".">' +
                        '<div class="{statusclass}" id="{name}" ext:qtip="{name}: {description}">' +
                        '<div class="cronk-status-icon">' +
                        '<div class="thumb"><img src="{image}"></div>' +
                        '<span class="x-editable">{name}</span>' +
                        '</div>' +
                        '</div>' +
                        '</tpl>' +
                        '<div class="x-clear"></div>';
            }
            var template = new Ext.XTemplate(template_text);

            CLP.add({
                title: String.format('{0} ({1})', title, store.getCount()),
                collapsed: collapsed ? true : false,
                autoScroll: false,
                catid: storeid,

                /*
                 * Bubbeling does not work because it collapse the
                 * parent panel all the time
                 */
                listeners: {
                    collapse: function (panel) {
                        CLP.saveState();
                    },
                    expand: function (panel) {
                        CLP.saveState();
                    }
                },

                items: new Ext.DataView({
                    store: store,
                    tpl: template,
                    overClass: 'x-view-over',
                    itemSelector: 'div.cronk-preview',
                    emptyText: 'No data',
                    cls: cls,
                    border: false,

                    // Create the drag zone
                    listeners: {
                        render: CLP.initCronkDragZone.createDelegate(CLP),
                        click: CLP.dblClickHandler.createDelegate(CLP),
                        contextmenu: CLP.handleContextmenu.createDelegate(CLP)
                    }
                }),
                border: false
            });

        };

        this.loadData = function (url) {

            var mask = null;

            if (this.getEl()) {
                mask = new Ext.LoadMask(this.getEl(), {
                    msg: _('Loading Cronks ...')
                });
                mask.show();
            }

            Ext.Ajax.request({
                url: url,
                callback: function (o, s, r) {
                    if (mask) {
                        mask.hide();
                    }
                    mask = null;
                },
                success: function (r, o) {
                    var data = Ext.decode(r.responseText);
                    if (Ext.isDefined(data.categories) && Ext.isDefined(data.cronks)) {

                        CLP.categories = data.categories;

                        CLP.cronks = data.cronks;

                        var i = 0;

                        Ext.each(data.categories, function (item, index, arry) {
                            if (Ext.isDefined(data.cronks[item.catid])) {
                                if (this.getStore(item.catid)) {
                                    fillStore(item.catid, data.cronks[item.catid]);
                                } else {
                                    fillStore(item.catid, data.cronks[item.catid]);

                                    var collapsed = false;

                                    // if something in state, calculate based on state
                                    if (!Ext.isEmpty(this.collapsed_categories)) {
                                        Ext.each(this.collapsed_categories, function(catid, index, l) {
                                            if (catid === item.catid) collapsed = true;
                                        }, this);
                                    }
                                    // or use default settings from xml
                                    else if(Ext.isDefined(item.collapsed) && item.collapsed === true) {
                                        collapsed = true;
                                    }

                                    // actually create the item
                                    createView(item.catid, item.title, collapsed);
                                }

                                i++;

                            }
                        }, this);

                        this.doLayout();
                        this.loadHelloCronk();
                    }
                },
                failure: function (r, o) {
                    var str = String.format(
                        _('Could not load the cronk listing, following error occured: {0} ({1})'), r.status, r.statusText);

                    AppKit.notifyMessage('Ajax Error', str, {
                        waitTime: 20
                    });
                },
                scope: CLP
            });

        };

        var cb = Cronk.util.CronkBuilder.getInstance();

        cb.addListener('writeSuccess', function () {
            CLP.reloadAll();
        });

        // loading the cronk data after component has been rendered
        this.on("render", function() {
            this.loadData(this.combinedProviderUrl);
        }, this);
    };

    Ext.extend(Icinga.Cronks.System.CronkListingPanel, Ext.Panel, {
        customCronkCredential: false,
        isCronkAdmin: false,
        isCategoryAdmin: false,

        autoScroll: true,
        border: false,

        defaults: {
            border: true,
            collapsible: true,
            titleCollapse: true
        },

        bodyCfg: {
            cls: 'icinga-cronk-list-panel-body'
        },

        stateful: true,

        stateEvents: ['startTabSlider', 'stopTabSlider'],
        bubbleEvents: [],

        tbar: [{
            iconCls: 'icinga-icon-arrow-refresh',
            tooltip: _('Reload'),
            handler: function (b, e) {
                var p = Ext.getCmp('cronk-listing-panel');
                p.reloadAll();
            }
        }, {
            text: _('Settings'),
            iconCls: 'icinga-icon-cog',
            ref: '../settingsButton',
            menu: [{
                text: _("Cronks as list"),
                checked: true,
                group: 'cronkliststyle',
                id: 'cronkliststyle-menu-list',
                checkHandler: function (checkItem, checked) {
                    if (checked === true) {
                        Ext.getCmp('cronk-listing-panel').applyCronkStyle('list');
                    }
                }
            }, {
                text: _("Cronks as icons"),
                checked: false,
                group: 'cronkliststyle',
                id: 'cronkliststyle-menu-icon',
                checkHandler: function (checkItem, checked) {
                    if (checked === true) {
                        Ext.getCmp('cronk-listing-panel').applyCronkStyle('icon');
                    }
                }
            }, {
                xtype: 'menuseparator'
            }, {
                text: _("Tab slider"),
                checked: false,
                id: 'tabslider',
                itemId: 'tabslider',
                checkHandler: function (checkItem, checked) {
                    if (checked === true) {
                        Ext.getCmp('cronk-listing-panel').startTabSlider();
                    } else {
                        Ext.getCmp('cronk-listing-panel').stopTabSlider();
                    }
                }
            }]
        }
        ],

        initComponent: function () {
            Icinga.Cronks.System.CronkListingPanel.superclass.initComponent.call(this);

            this.addEvents({
                startTabSlider: true,
                stopTabSlider: true
            });
        },

        applyState: function (state) {
            if (!Ext.isEmpty(state.collapsed_categories)) {
                this.collapsed_categories = state.collapsed_categories;
            }
            if (!Ext.isEmpty(state.cronkliststyle)) {
                this.applyCronkStyle(state.cronkliststyle, false);
            }
            if (Ext.isDefined(state.tabslider_active) && state.tabslider_active === true) {
                this.startTabSlider();
                Ext.getCmp('tabslider').setChecked(true);
            }
        },

        getState: function () {
            this.collapsed_categories = [];
            this.items.each(function (item, index, l) {
                if (item.catid && item.collapsed && item.collapsed === true) {
                    this.collapsed_categories.push(item.catid);
                }
            }, this);

            return {
                collapsed_categories: this.collapsed_categories,
                cronkliststyle: this.cronkliststyle ? this.cronkliststyle : null,
                tabslider_active: Ext.isDefined(this.sliderTask) ? true : false
            };
        },

        applyCronkStyle: function (style, reload) {
            // change style and save
            if (typeof (reload) === "undefined") {
                reload = true;
            }
            this.cronkliststyle = style;

            // set Panel Size + MaxSize
            var view = Ext.getCmp('view-container'),
                west = Ext.getCmp('west-frame');
            if (style === 'icon') {
                // viewport
                view.layout.west.minSize = 220;
                view.layout.west.maxSize = 400;
                // cronk
                west.setSize(300);
                // menu
                Ext.getCmp('cronkliststyle-menu-icon').setChecked(true, true);
            } else {
                // viewport
                view.layout.west.minSize = 200;
                view.layout.west.maxSize = 200;
                // cronk
                west.setSize(200);
                // menu
                Ext.getCmp('cronkliststyle-menu-list').setChecked(true, true);
            }
            // force layout re-render to avoid glitching
            AppKit.util.Layout.doLayout(null, 10);

            view.saveState();
            west.saveState();

            // Reload the data if required
            if (reload) {
                this.reloadAll();
            }
        },

        setCategoryAdmin: function (grant) {

            if (grant === true) {
                this.isCategoryAdmin = true;
            } else {
                this.isCategoryAdmin = false;
            }

            if (this.isCategoryAdmin === true) {
                this.getTopToolbar().insert(1, {
                    text: _('Categories'),
                    iconCls: 'icinga-icon-category',
                    handler: function (b, e) {
                        this.showCategoryEditor(b.getEl());
                    },
                    scope: this
                });
            }
        },

        setCronkAdmin: function (grant) {
            if (grant === true) {
                this.isCronkAdmin = true;
            } else {
                this.isCronkAdmin = false;
            }
        },

        showCategoryEditor: function (where) {
            if (this.isCategoryAdmin !== true) {
                return false;
            }

            if (!Ext.isDefined(this.categoryEditor)) {
                this.categoryEditor = new Icinga.Cronks.util.CategoryEditor({
                    id: this.id + '-category-editor'
                });
            }

            if (this.categoryEditor.isVisible()) {
                this.categoryEditor.hide(where);
            } else {
                this.categoryEditor.show(where);
            }

        },

        getStore: function (storeid) {
            if (Ext.isDefined(this.stores[storeid])) {
                return this.stores[storeid];
            }
        },

        loadHelloCronk : function() {
            var tp = Ext.getCmp('cronk-tabs');
            var cronkName = ( AppKit.getPrefVal('org.icinga.cronk.default') || 'portalHello' );
            var cronksOnlyUrl = this.combinedProviderUrl.replace('combined', '');

            if (tp.items.getCount() > 0) {
                return false;
            }

            Ext.Ajax.request({
                url: cronksOnlyUrl,
                success: function (r, o) {
                    try {
                        var data = Ext.decode(r.responseText);
                    } catch (e) {
                        console.log('CronkListingPanel/loadHelloCronk: ' + e);
                        return false;
                    }
                    var cstore = new Ext.data.JsonStore({
                        autoDestroy: true,
                        autoLoad: false
                    });
                    cstore.loadData(data);
                    var id = cstore.find('cronkid', cronkName);

                    if (id < 0) {
                        id = cstore.find('cronkid', 'portalHello');
                        console.log('CronkListingPanel/loadHelloCronk: Could not found Cronk: ' + cronkName);
                    }

                    if (id > 0) {
                        var record = cstore.getAt(id);
                        var cronk = {
                            xtype: 'cronk',
                            iconCls: Cronk.getIconClass(record.data.image),
                            title: record.data.name,
                            crname: record.data.cronkid,
                            closable: true,
                            params: Ext.apply({}, record.data['ae:parameter'], {
                                module: record.data.module,
                                action: record.data.action
                            })
                        };
                        tp.setActiveTab(tp.add(cronk));
                    }
                }
            });

            return true;
        },

        dblClickHandler: function (oView, index, node, e) {
            var record = oView.getStore().getAt(index);

            var tabPanel = Ext.getCmp('cronk-tabs');
            if (tabPanel) {
                var cronk = {
                    xtype: 'cronk',
                    iconCls: Cronk.getIconClass(record.data.image_id),
                    title: record.data.name,
                    crname: record.data.cronkid,
                    closable: true,
                    params: Ext.apply({}, record.data['ae:parameter'], {
                        module: record.data.module,
                        action: record.data.action
                    })
                };
                Cronk.util.InterGridUtil.gridFilterLink(cronk, {});
            }
        },

        initCronkDragZone: function (v) {
            v.dragZone = new Ext.dd.DragZone(v.getEl(), {
                ddGroup: 'cronk',

                getDragData: function (e) {
                    var sourceEl = e.getTarget(v.itemSelector, 10);

                    if (sourceEl) {
                        var d = sourceEl.cloneNode(true);
                        d.id = Ext.id();
                        v.dragData = {
                            sourceEl: sourceEl,
                            repairXY: Ext.fly(sourceEl).getXY(),
                            ddel: d,
                            dragData: v.getRecord(sourceEl).data
                        };
                        return v.dragData;

                    }

                },

                getRepairXY: function () {
                    return this.dragData.repairXY;
                }

            });
        },

        getContextmenu: function () {

            var idPrefix = this.id + '-context-menu';

            var isCronkAdmin = Boolean(this.isCronkAdmin);

            var cronkUrlWindow = new Icinga.Cronks.util.CronkUrlWindow({
                baseUrl: this.cronkUrlBase,
                separator: '/'
            });

            var cronkPermissionWindow = new Icinga.Cronks.util.CronkPermissionWindow();

            var ctxMenu = null;

            if (!Ext.isDefined(this.contextmenu)) {
                ctxMenu = new Ext.menu.Menu({
                    customCronkCredential: this.customCronkCredential, // Copy attribute because scope is changing

                    setItemData: function (view, index, node) {
                        this.ctxView = view;
                        this.ctxIndex = index;
                        this.ctxNode = node;
                    },

                    getItemRecord: function () {
                        return this.ctxView.getStore().getAt(this.ctxIndex);
                    },

                    getItemData: function () {
                        var r = this.getItemRecord();
                        if (Ext.isDefined(r.data)) {
                            return r.data;
                        }
                    },

                    getListing: function () {
                        return this.listing;
                    },

                    id: idPrefix,

                    items: [{
                        id: idPrefix + '-button-edit',
                        text: _('Edit'),
                        iconCls: 'icinga-icon-pencil',
                        handler: function (b, e) {
                            var cb = Cronk.util.CronkBuilder.getInstance();

                            if (Ext.isObject(cb)) {
                                cb.setCronkData(ctxMenu.getItemData());
                                cb.show(b.getEl());
                            } else {
                                AppKit.notifyMessage(_('Error'), _('CronkBuilder has gone away!'));
                            }
                        }
                    }, {
                        id: idPrefix + '-button-delete',
                        text: _('Delete'),
                        iconCls: 'icinga-icon-delete',
                        handler: function (b, e) {
                            var item = ctxMenu.getItemData();
                            Ext.Msg.confirm(_('Delete cronk'), String.format(_('Are you sure to delete {0}'), item.name), function (btn) {
                                if (btn === 'yes') {
                                    Ext.Ajax.request({
                                        url: AppKit.c.path + '/modules/cronks/provider/cronks/',
                                        params: {
                                            xaction: 'delete',
                                            cid: item.cronkid,
                                            name: item.name,
                                            categories: item.categories,
                                            description: item.description,
                                            image: item.image,
                                            module: item.module,
                                            action: item.action
                                        },
                                        success: function (response, options) {
                                            AppKit.notifyMessage(_('Cronk deleted'), String.format(_('We have deleted your cronk "{0}"'), item.name));

                                            ctxMenu.getListing().reloadAll();
                                        },
                                        failure: function (response, options) {
                                            var o = Ext.decode(response.responseText);
                                            if (Ext.isObject(o) && Ext.isDefined(o.errors)) {
                                                AppKit.notifyMessage(_('Error'), String.format(_('Some error: {0}'), o.errors[0]));
                                            }
                                        }
                                    });
                                }
                            });
                        }
                    }, {
                        id: idPrefix + '-button-security',
                        text: _('Permissions'),
                        iconCls: 'icinga-icon-lock',
                        handler: function (b, e) {
                            cronkPermissionWindow.update(ctxMenu.getItemData());
                            cronkPermissionWindow.alignTo(e.getTarget(), 'tl?');

                            cronkPermissionWindow.on('load', function () {
                                cronkPermissionWindow.show();
                            }, this, {
                                single: true
                            });

                            cronkPermissionWindow.on('save', function () {
                                ctxMenu.getListing().reloadAll();
                            }, this, {
                                single: true
                            });
                        }
                    }, '-', { // SEPARATOR
                        id: idPrefix + '-button-url',
                        text: _('Get CronkUrl'),
                        iconCls: 'icinga-icon-anchor',
                        handler: function (b, e) {
                            cronkUrlWindow.update(ctxMenu.getItemData());
                            cronkUrlWindow.alignTo(e.getTarget(), 'tl?');
                            cronkUrlWindow.show();
                        }
                    }
                    ],

                    listeners: {
                        show: function (ctxm) {
                            if (this.customCronkCredential === true) {
                                if (this.getItemData().system === true || (this.getItemData().owner === false && isCronkAdmin === false)) {
                                    this.items.get(idPrefix + '-button-edit').setDisabled(true);
                                    this.items.get(idPrefix + '-button-delete').setDisabled(true);
                                } else {
                                    this.items.get(idPrefix + '-button-edit').setDisabled(false);
                                    this.items.get(idPrefix + '-button-delete').setDisabled(false);
                                }
                            } else {
                                this.items.get(idPrefix + '-button-edit').setDisabled(true);
                                this.items.get(idPrefix + '-button-delete').setDisabled(true);
                            }

                            if (isCronkAdmin === true) {
                                this.items.get(idPrefix + '-button-security').setDisabled(false);
                            } else {
                                this.items.get(idPrefix + '-button-security').setDisabled(true);
                            }
                        }
                    }
                });

                this.contextmenu = ctxMenu;
            }

            return this.contextmenu;
        },

        handleContextmenu: function (view, index, node, e) {
            e.stopEvent();

            var ctxMenu = this.getContextmenu();

            ctxMenu.setItemData(view, index, node);

            ctxMenu.listing = this;

            ctxMenu.showAt(e.getXY());
        },

        reloadAll: function () {
            this.removeAll();

            Ext.iterate(this.stores, function (storeid, store) {
                store.destroy();
                delete(this.stores[storeid]);
            }, this);

            this.loadData(this.combinedProviderUrl);
        },

        /**
         * Start a task to iterate active tabs
         */
        startTabSlider: function() {

            if (Ext.isDefined(this.sliderTask)) {
                return;
            }

            var refresh = AppKit.getPrefVal('org.icinga.tabslider.changeTime') || 60;

            var tp = Ext.getCmp('cronk-tabs');

            this.sliding_tab = tp.getActiveTabIndex();

            this.sliderTask = {
                run: function () {
                    this.sliding_tab++;
                    if (this.sliding_tab >= tp.items.getCount()) {
                        this.sliding_tab = 0;
                    }

                    tp.setActiveTab(this.sliding_tab);
                },
                interval: (refresh * 1000),
                scope: this
            };

            AppKit.getTr().start(this.sliderTask);

            this.fireEvent('startTabSlider');
        },

        /**
         * Stop the tasks which iterate tabs
         */
        stopTabSlider: function() {
            if (Ext.isDefined(this.sliderTask)) {
                AppKit.getTr().stop(this.sliderTask);
                delete(this.sliderTask);
                this.fireEvent('stopTabSlider');
            }
        }
    });

})();
