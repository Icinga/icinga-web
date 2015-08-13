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

Ext.ns('Icinga.Reporting.util');

Icinga.Reporting.util.ScheduleTaskList = Ext.extend(Ext.Panel, {
    constructor : function(config) {
        Icinga.Reporting.util.ScheduleTaskList.superclass.constructor.call(this, config);
    },

    initComponent : function() {
        Icinga.Reporting.util.ScheduleTaskList.superclass.initComponent.call(this);
        this.taskListStore = new Ext.data.JsonStore({
            autoDestroy : true,
            url : this.scheduler_list_url,
            autoLoad : false
        });

        this.taskGrid = new Ext.grid.GridPanel({
            border: false,
            store : this.taskListStore,
            layout : 'fit',
            autoFill : true,
            autoHeight : true,
            colModel: new Ext.grid.ColumnModel({
                defaults : {
                    width: 120,
                    sortable : false
                },

                columns : [{
                    id : 'id',
                    header : _('Id'),
                    dataIndex : 'id',
                    width: 40
                }, {
                    header : _('Job name'),
                    dataIndex : 'label',
                    width : 250
                }, {
                    header : _('Owner'),
                    dataIndex : 'username',
                    width : 80
                }, {
                    header : _('State'),
                    dataIndex : 'state',
                    renderer : {
                        fn : function(value, metaData, record, rowIndex, colIndex, store) {
                            s = new String(value);
                            return s.charAt(0) + s.substr(1).toLowerCase();
                        },
                        scope : this
                    },
                    width : 60
                }, {
                    header : _('Last run'),
                    dataIndex : 'previousFireTime',
                    width : 120
                }, {
                    header : _('Next run'),
                    dataIndex : 'nextFireTime',
                    width : 120
                }]
            }),
            viewConfig: {
                forceFit: true
            },
            sm: new Ext.grid.RowSelectionModel({singleSelect:true})
        });

        this.add(this.taskGrid);

        this.doLayout();
    },

    reload : function() {
        this.taskListStore.reload();
    },

    loadJobsForUri : function(uri) {
        this.taskListStore.removeAll();
        this.taskListStore.setBaseParam('uri', uri);
        this.taskListStore.load();
    },

    getGrid : function() {
        return this.taskGrid;
    },

    getStore : function() {
        return this.taskListStore();
    }
});
