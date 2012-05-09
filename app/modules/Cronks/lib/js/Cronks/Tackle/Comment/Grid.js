// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
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

/*global Ext: false, Icinga: false, _: false */

Ext.ns('Icinga.Cronks.Tackle.Comment');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Comment.Grid = Ext.extend(Ext.Panel, {
        type: null,
        dataLoaded: false,
        objectId: null,
        border: false,

        constructor: function (config) {

            if (Ext.isEmpty(config.type)) {
                throw ("config.type is needed: host or service!");
            }

            Icinga.Cronks.Tackle.Comment.Grid.superclass.constructor.call(this, config);
        },

        initComponent: function () {

            this.tbar = [{
                text: _('Refresh'),
                iconCls: 'icinga-action-refresh',
                scope: this,
                handler: function () {
                    this.store.load();
                }
            }];

            Icinga.Cronks.Tackle.Comment.Grid.superclass.initComponent.call(this);

            this.on('activate', this.onActivate, this);

            this.setLayout(new Ext.layout.FitLayout(Ext.apply({}, this.layoutConfig)));

            this.store = new Icinga.Api.RESTStore({
                autoDestroy: true,
                idIndex: 0,
                target: 'comment',
                columns: ['INSTANCE_NAME', 'COMMENT_ID', 'COMMENT_DATA', 'COMMENT_AUTHOR_NAME', 'COMMENT_TIME', 'COMMENT_OBJECT_ID', 'COMMENT_TYPE'],
                listeners : {
                	load : this.onStoreLoad.createDelegate(this)
                }
            });

            this.expander = new Ext.ux.grid.RowExpander({
                tpl: '<div class="x-icinga-comment-expander-row">{COMMENT_DATA}</div>'
            });

            this.grid = new Ext.grid.GridPanel({
                layout: 'fit',
                store: this.store,
                colModel: new Ext.grid.ColumnModel({
                    defaults: {

                    },
                    columns: [this.expander, {
                        header: _('Id'),
                        dataIndex: 'COMMENT_ID'
                    }, {
                        header: _('Entry time'),
                        dataIndex: 'COMMENT_TIME'
                    }, {
                        header: _('Author'),
                        dataIndex: 'COMMENT_AUTHOR_NAME'
                    }, {
                        header: "",
                        dataIndex: "",
                        width: 16,
                        fixed: true,
                        renderer: Icinga.Cronks.Tackle.Renderer.Generic.showIconCls.createDelegate(this, [{
                            iconCls: 'icinga-icon-delete',
                            qtip: _('Delete comment')
                        }], true),
                        listeners: {
                            click: this.onDeleteComment.createDelegate(this)
                        }
                    }]
                }),

                viewConfig: {
                    forceFit: true
                },

                plugins: [
                this.expander]
            });

            this.add(this.grid);

            this.doLayout();
        },
        
        onStoreLoad : function(store, records, options) {
        	this.updateParentTitle(records.length);
        },
        
        updateParentTitle : function(count) {
        	if (this.parentCmp) {
        		if (count > 0) {
        			this.parentCmp.setTitle(String.format(_('Comments ({0})'), count));
        		} else {
        			this.parentCmp.setTitle(_('Comments'));
        		}
        	}
        },

        onDeleteComment: function (renderer, grid, rowIndex, event) {

            var data = grid.store.getAt(rowIndex).data;
            var command = "DEL_"+(this.type=="service" ? "SVC" : this.type.toUpperCase())+"_COMMENT";

            Ext.Msg.show({
                title: _('Confirmation'),
                msg: String.format(_('Delete comment {0}?'), data.COMMENT_ID),
                buttons: Ext.Msg.YESNO,
                icon: Ext.MessageBox.QUESTION,
                scope: this,
                fn: function (buttonId, text, opt) {
                    if (buttonId === 'yes') {
                        Icinga.Api.Command.Facade.sendCommand({
                            command: command,
                            targets: [{
                                instance: data.INSTANCE_NAME
                            }],
                            data: {
                                comment_id: data.COMMENT_ID
                            }
                        });

                        this.store.reload();
                    }
                }

            });
        },

        recordUpdated: function (record) {
            var oid = record.get(this.type.toUpperCase()+"_OBJECT_ID");
            if(!oid)
                return;
            if (oid !== this.objectId) {
                this.record = record;
                this.objectId = oid;
                this.dataLoaded = false;
                this.updateParentTitle(0);
                if (this.isVisible() === true) {
                    this.onActivate();
                }
            }
        },

        getObjectId: function () {
            return this.objectId;
        },

        onActivate: function () {
            if (this.dataLoaded === false && this.objectId) {
                this.loadCommentsForObjectId(this.objectId);
            }
        },

        loadCommentsForObjectId: function (oid) {
            this.store.setFilter({
                type: 'AND',
                field: [{
                    type: 'atom',
                    field: ['COMMENT_OBJECT_ID'],
                    method: ['='],
                    value: [oid]
                }]

            });

            this.store.load();
        }
    });

    Ext.reg('cronks-tackle-comment-grid', Icinga.Cronks.Tackle.Comment.Grid);

})();