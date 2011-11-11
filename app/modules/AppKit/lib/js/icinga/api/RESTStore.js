/*global Ext: false, Icinga: false, _: false, AppKit: false */

Ext.ns('Icinga.Api');

(function () {
    "use strict";

    Icinga.Api.RESTStore = Ext.extend(Ext.data.JsonStore, {
        target: null,
        columns: null,
        __filter: null,

        orderColumn: null,
        orderDirection: null,
        limit: -1,
        offset: 0,
        groupBy: null,
        countColumn: null,
        withSLA: false,
        constructor: function (cfg) {
            if (Ext.isEmpty(cfg.columns) === false) {	
            	/*
            	 * Use default ext fields syntax for mapping or 
            	 * special icinga column syntax for simple api
            	 * queries 
            	 */
            	if (Ext.isArray(cfg.columns)) {
            		if (Ext.isObject(cfg.columns[0])) {
            			cfg.fields = cfg.columns;
            			cfg.columns = [];
            			Ext.each(cfg.fields, function(val, key) {
            				cfg.columns.push( (Ext.isEmpty(val.mapping) === true) ? val.name : val.mapping );
            			}, this);
            		} else {
            			cfg.fields = cfg.columns;
            		}
            	} else {
            		cfg.fields = [cfg.columns];
            	}
            }
            
            if (cfg.withSLA) {
                cfg.fields.push("SLA_STATE_AVAILABLE");
                cfg.fields.push("SLA_STATE_UNAVAILABLE");
                cfg.fields.push("SLA_STATE_0");
                cfg.fields.push("SLA_STATE_1");
                cfg.fields.push("SLA_STATE_2");
                cfg.fields.push("SLA_STATE_3");
            }
            cfg.root = 'result';
            cfg.url = AppKit.c.path + "/modules/web/api/json";
            cfg.totalProperty = "total";
            cfg.paramNames = {
                start: 'limit_start',
                limit: 'limit',
                sort: 'order_col',
                dir: 'order_dir'
            };
            Ext.data.JsonStore.prototype.constructor.call(this, cfg);

        },

        setWithSLA: function (bool) {
            this.withSLA = bool;
        },

        setColumns: function (cols) {
            this.columns = cols;
        },

        addColumn: function (col) {
            if (this.columns.indexOf(col) === -1) {
                this.columns.push(col);
            }
        },

        setCountColumn: function (field) {
            this.countColumn = field;
        },

        setTarget: function (target) {
            this.target = target;
        },

        setFilter: function (filter) {
            this.__filter = filter;
        },

        setOrderColumn: function (order) {
            this.orderColumn = order;
        },

        setOrderDirection: function (dir) {
            if (dir === "ASC") {
                this.orderDirection = dir;
            } else {
                this.orderDirection = "DESC";
            }
        },

        setDB: function (db) {
            this.db = db;
        },
        setGroupBy: function(col) {
            this.groupBy = col;
        },
        setLimit: function (limit) {
            limit = parseInt(limit, 10);
            if (limit > 0) {
                this.limit = limit;
            } else {
                this.limit = -1;
            }
        },

        setOffset: function (offset) {
            offset = parseInt(offset, 10);
            if (offset > 0) {
                this.offset = offset;
            } else {
                this.offset = 0;
            }
        },

        getWithSLA: function () {
            return this.withSLA;
        },

        getTarget: function () {
            return this.target;
        },

        getFilter: function () {
            return this.__filter;
        },

        getFilterAsJson: function () {
            return Ext.encode(this.getFilter());
        },

        getOrderColumn: function () {
            return this.orderColumn;
        },

        getOrderDirection: function () {
            return (this.orderDirection === "ASC") ? "ASC" : "DESC";
        },

        getCountColumn: function () {
            return this.countColumn;
        },

        getLimit: function ()  {
            if (this.limit < 0) {
                return null;
            }
            return parseInt(this.limit, 10);
        },

        getOffset: function () {
            if (this.offset < 1) {
                return null;
            }
            return parseInt(this.limit, 10);
        },

        getDB: function () {
            return this.db;
        },
        getColumns: function () {
            return this.columns;
        },
        getGroupBy: function() {
            return this.groupBy;
        },
        load: function (options) {
            options = options  ||   {
                params: {}
            };
            this.storeOptions(options);
            var cols = this.getColumns();

            var target = this.getTarget();
            var filter = this.getFilterAsJson();
            var order = this.getOrderColumn() ? this.getOrderColumn() + ";" + this.getOrderDirection() : null;
            var countCol = this.getCountColumn();
            var limit = this.getLimit();
            var groupBy = this.getGroupBy();
            var offset = this.getOffset();
            var db = this.getDB();
            var wSLA = this.getWithSLA();

            var cfg = {
                db: db,
                target: target
            };

            if (wSLA) {
                cfg.withSLA = true;
            }

            if (filter !== 'null' && filter) {
                cfg.filters_json = filter;
            }

            if (order) {
                cfg.order = order;
            }

            if (countCol) {
                cfg.countColumn = countCol;
            }

            if (limit) {
                cfg.limit = limit;
            }

            if (offset) {
                cfg.limit_start = offset;
            }
            if (groupBy) {
                cfg["groups[]"] = groupBy;
            }
            if (!Ext.isArray(cols)) {
                cols = [cols];
            }

            for (var i = 0; i < cols.length; i++) {
                if (Ext.isPrimitive(cols[i])) {
                    if (cols[i].substr(0, 3) === "SLA") {
                        continue;
                    }
                    cfg["columns[" + i + "]"] = cols[i];
                }
            }

            Ext.apply(options.params, cfg);

            // return Ext.data.JsonStore.prototype.load.call(this, options);
            Icinga.Api.RESTStore.superclass.load.call(this, options);
        }
    });

})();