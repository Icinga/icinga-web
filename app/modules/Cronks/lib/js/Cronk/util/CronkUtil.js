// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
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

Ext.ns('Cronk.util');

(function () {

    "use strict";

    Cronk.util.StructUtil = function () {

        var pub = {

            extractParts: function (o, list) {
                var intersect = {};

                Ext.each(list, function (item, index, arry) {
                    if (item in o) {
                        intersect[item] = o[item];
                    } else if (!(item in intersect)) {
                        intersect[item] = {};
                    }
                });

                return intersect;
            },

            attributeString: function (o) {
                var p = [];
                Ext.iterate(o, function (k, v) {
                    p.push(String.format('{0}="{1}"', k, v));
                });
                return p.join(' ');
            }

        };

        return pub;

    }();

    Cronk.util.scriptInterface = Ext.extend(Object, function () {

        var r = null;
        var parentCall = function (method) {
                if (method in this.parentCmp && Ext.isFunction(this.parentCmp[method])) {
                    return this.parentCmp[method].createDelegate(this.parentCmp, [], true);
                }
            };

        return {
            constructor: function (parentid) {
                r = Cronk.Registry.get(parentid);

                if (r) {
                    this.parentCmp = Ext.getCmp(parentid);
                    this.parentid = parentid;
                    Ext.apply(this, r);

                    Ext.apply(this, {
                        insert: parentCall.call(this,'insert'),
                        add: parentCall.call(this,'add'),
                        doLayout: parentCall.call(this,'doLayout'),
                        registry: r
                    });

                }
            },

            applyParams: function (o) {
                if (Ext.isObject(o)) {
                    Ext.each(Cronk.defaults.CONFIG_ITEMS, function (item, index, all) {
                        if (Ext.isDefined(o[item])) {
                            delete(o[item]);
                        }
                    });

                    Ext.apply(this.params, o);
                }
            },

            applyToRegistry: function (o) {
                if (Ext.isObject(o)) {
                    Ext.apply(r, o);

                    // Keep data in sync
                    Ext.apply(this, r);
                }
            },

            getRegistryEntry: function () {
                return this.registry;
            },
 
           getParent: function () {
                return this.parentCmp;
            },

            getParameter: function (pname, vdefault) {
                if (this.hasParameter(pname) || Ext.isDefined(this.params["p["+pname+"]"])) {
                    return this.params[pname] || this.params["p["+pname+"]"];
                }
                return vdefault;
            },

            hasParameter: function (pname) {
                return Ext.isDefined(this.params[pname]) || Ext.isDefined(this.params["p["+pname+"]"]);
            },

            setStatefulObject: function (obj) {
                this.getRegistryEntry().statefulObjectId = obj.getId();
            },

            getStatefulObject: function () {
                if (Ext.isDefined(this.getRegistryEntry().statefulObjectId)) {
                    var o = Ext.getCmp(this.getRegistryEntry().statefulObjectId);
                    if (o) {
                        return o;
                    }
                }
            }

        };

    }());

    Cronk.util.initEnvironment = function (parentid, method, o) {
        var
        run = false,
            extready = false;

        o = (o || {});

        if (Ext.isObject(parentid)) {
            Ext.apply(o, parentid);
        }

        // Some options you can set withn a object as third parameter
        if (!Ext.isEmpty(o.parentid)) {
            parentid = o.parentid;
        }
        if (!Ext.isEmpty(o.run)) {
            run = true;
        }
        if (!Ext.isEmpty(o.extready)) {
            extready = true;
        }

        if (!Ext.isEmpty(o.state) && Ext.isString(o.state)) {
            var state = Ext.decode(o.state);
            if (Ext.isObject(state)) {
                delete(o.state);
                o.state = state;
            } else {
                o.state = undefined;
            }
        }

        var rc = function () {
                if (parentid) {
                    if (Ext.isFunction(method)) {
                        var lscope = new Cronk.util.scriptInterface(parentid);

                        lscope.applyToRegistry(o);

                        if (run === true || (lscope.getParent() && lscope.getRegistryEntry())) {
                            method.call(lscope);
                            return true;
                        }
                    }
                }
            };

        if (extready === true) {
            Ext.onReady(rc, this);
        } else {
            rc.call(this);
        }

        return true;
    };

    Cronk.util.InterGridUtil = function () {

        var applyParametersToGrid = function (baseParams, c) {

                if ((c.getXType() === 'grid' || c.getXType() === 'cronkgrid')) {
                    var store = c.getStore();
                    if (!("originParams" in store) || typeof (store.originParams) === "undefined") {
                        store.originParams = {};
                    }

                    Ext.iterate(baseParams, function (k, v) {
                        store.originParams[k] = v;
                        store.setBaseParam(k, v);
                    });

                    c.getStore().reload();
                }
            };
            
        var pub = {

            /**
             * Opens a cronk in tab panel based on configuration
             *
             * - First try to find the id
             * - After that checks title. If title is different create new cronk
             * - Create the cronk from config or reload the one found before
             *
             * @param {Object} config
             * @param {Object} baseParams
             * @returns {Ext.Panel}
             */
            gridFilterLink: function (config, baseParams) {
                var tabs = Ext.getCmp('cronk-tabs');
                var id = null;
                
                if (!Ext.isEmpty(config.parentid)) {
                    id = config.parentid;
                } else if (!Ext.isEmpty(config.id)) {
                    id = config.id;
                }
                
                var panel = Ext.getCmp(id);
                var panel_component = null;
                
                // disable grid autoload
                config.params.storeDisableAutoload = 1;

                if (!Ext.isDefined(config.iconCls)) {
                    config.iconCls = 'icinga-cronk-icon-cube';
                }

                if (panel && config.replace === true) {
                    tabs.remove(panel);
                    panel = null;
                }

                if (panel) {
                    if (config.title !== panel.title) {
                        var possibleContainers = tabs.findBy(function(component) {
                            return (component.title === config.title) ? true : false;
                        });

                        if (possibleContainers.length === 1) {
                            panel = possibleContainers[0];
                        } else {
                            id = Ext.id(null, id + '-');
                            panel = null;
                        }
                    }
                }

                panel_component = panel;

                if (!panel_component) {
                    config.id = id;
                    panel_component = Cronk.factory(config);

                    panel_component.on('add', function (p, c, i) {
                        applyParametersToGrid(baseParams, c);
                    });

                    //              console.log(baseParams);

                    tabs.add(panel_component);
                } else {
                    // @todo is this needed?
                    var grids = panel_component.findByType('cronkgrid');
                    if (grids[0]) {
                        applyParametersToGrid(baseParams, grids[0]);
                    }
                }

                panel_component.setTitle(config.title);
                tabs.setActiveTab(panel_component);
                // AppKit.log("Panel",panel_component)
                return panel_component;
            },

            clickGridLink: function (id, template, f, t) {
                var el = Ext.get(id);
                if (id && el) {
                    el.addClass('icinga-link');
                    el.on('click', (function () {
                        var cronk = {
                            parentid: 'click-grid-link-' + id,
                            title: (t || _('untitled')),
                            crname: "gridProc",
                            closable: true,
                            params: {
                                template: template
                            }
                        };

                        Ext.iterate(f, function (k, v) {
                            delete(f[k]);
                            if (k.match(/f\[(.*?)\-operator\]/)) {
                                return true;
                            }
                            k = k.replace(/f\[(.*?)\-value\]/, '$1');
                            f['f[' + k + '-value]'] = v;
                            f['f[' + k + '-operator]'] = 50;

                        });

                        Cronk.util.InterGridUtil.gridFilterLink(cronk, f);

                    }).createDelegate(this));
                }
            },

            clickTOLink: function (id, template, f, t) {
                var el = Ext.get(id);
                if (el && id) {

                    el.addClass('icinga-link');
                    el.on('click', (function () {

                        var p = {
                            template: template
                        };

                        f = Ext.apply({}, f);

                        Ext.iterate(f, function (k, v) {
                            if (k.match(/f\[(.*?)\-operator\]/)) {
                                return true;
                            }
                            k = k.replace(/f\[(.*?)\-value\]/, '$1');

                            p['f[' + k + '-value]'] = v;
                            p['f[' + k + '-operator]'] = 50;

                        });



                        var cronk = {
                            parentid: 'click-to-link-' + id,
                            title: (t || _('untitled')),
                            crname: 'icingaToProc',
                            closable: true,
                            params: p
                        };

                        Cronk.util.InterGridUtil.tabCronkElement(cronk);

                    }).createDelegate(this));

                }

            },

            tabCronkElement: function (config) {
                var tabs = Ext.getCmp('cronk-tabs');
                var id = config.parentid || null;
                var panel = Ext.getCmp(id);

                if (!panel) {
                    panel = Cronk.factory(config);
                    tabs.add(panel);
                }

                panel.setTitle(config.title);
                tabs.setActiveTab(panel);
                tabs.doLayout();
                return panel;
            },

            openExternalCronk: function (title, url) {

                var panel = Ext.getCmp('cronk-tabs');
                var urlTab = panel.add({
                    parentid: Ext.id(),
                    xtype: 'cronk',
                    title: title,
                    crname: 'genericIFrame',
                    closable: true,
                    params: {
                        module: 'Cronks',
                        action: 'System.ViewProc',
                        url: url
                    }
                });
                panel.doLayout();
                panel.setActiveTab(urlTab);

            }
        };

        return pub;

    }();

})();
