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

Ext.ns('Icinga.Cronks.Tackle.Relation');

Icinga.Cronks.Tackle.Relation.Head = Ext.extend(Ext.Panel, {
    title : _('Relations'),
    iconCls : 'icinga-icon-databases-relation',
        layout: 'hbox',
        layoutConfig: {
            align: 'stretch',
            pack: 'start'
    },
    
    stores : [],
    
    objectId : null,
    loaded : false,
    connection: 'icinga',
    constructor : function(config) {
        if (Ext.isEmpty(config.type)) {
                throw ("config.type is needed: host or service!");
        }
        Icinga.Cronks.Tackle.Relation.Head.superclass.constructor.call(this, config);
    },

    initStores : function() {
        
        this.contactStore = new Ext.data.GroupingStore({
            autoDestroy: true,
            reader: new Ext.data.JsonReader({
                fields : ['contact_name', 'contact_alias', 'contact_email_address', 'contact_id', 'contact_object_id', 'contactgroup_name', 'contactgroup_object_id'],
                idProperty : 'contact_id'
            }),
            groupOnSort: false,
            remoteGroup: false,
            groupField: 'contactgroup_name'
        })
        
        this.customvariableStore = new Ext.data.JsonStore({
            fields : ['customvariable_id', 'varname', 'varvalue'],
            idProperty : 'customvariable_id',
            autoDestroy : true
        });
        
        this.hostgroupStore = new Ext.data.JsonStore({
            fields : ['alias', 'hostgroup_object_id', 'name'],
            idProperty : 'hostgroup_object_id',
            autoDestroy : true
        });
        
        this.servicegroupStore = new Ext.data.JsonStore({
            fields : ['alias', 'servicegroup_object_id', 'name'],
            idProperty : 'servicegroup_object_id',
            autoDestroy : true
        });
        
        this.stores.push({store : this.contactStore, root : 'contact'});
        this.stores.push({store : this.customvariableStore, root : 'customvariable'});
        this.stores.push({store : this.hostgroupStore, root : 'hostgroup'});
        this.stores.push({store : this.servicegroupStore, root : 'servicegroup'});
    },
    
    buildContactGrid : function() {
        return this.contactGrid ? this.contactGrid : (this.contactGrid = new Ext.grid.GridPanel({
            title : _('Contacts'),
            autoScroll : true,
            store : this.contactStore,
            layout : 'fit',
            flex : 1,
            colModel : new Ext.grid.ColumnModel({
                columns : [{
                    header : _('Group'),
                    dataIndex : 'contactgroup_name',
                    xtype: 'ellipsiscolumn'
                }, {
                    header : _('Name'),
                    dataIndex : 'contact_name',
                    xtype: 'ellipsiscolumn'
                }, {
                    header : _('Alias'),
                    dataIndex : 'contact_alias',
                    xtype: 'ellipsiscolumn'
                }, {
                    header : _('Email address'),
                    dataIndex : 'contact_email_address',
                    xtype: 'ellipsiscolumn'
                }]
            }),
            
            view : new Ext.grid.GroupingView({
                forceFit: true,
                hideGroupedColumn : true,
                groupTextTpl: '{[Ext.isEmpty(values.group) === true ? "Direct contacts" : values.group]} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
            })
        }));
        
        return this.contactGrid;
    },
    
    buildCustomvarGrid : function() {
        return this.customvarGrid ? this.customvarGrid : (this.customvarGrid = new Ext.grid.GridPanel({
            title : _('Customvars'),
            autoScroll : true,
            store : this.customvariableStore,
            layout : 'fit',
            flex : 1,
            colModel : new Ext.grid.ColumnModel({
                columns : [{
                    header : _('Name'),
                    dataIndex : 'varname',
                    xtype: 'ellipsiscolumn'
                }, {
                    header : _('Value'),
                    dataIndex : 'varvalue',
                    xtype: 'ellipsiscolumn'
                }]
            }),
            viewConfig : {
                forceFit : true
            }
        }));
    },
    
    buildGroupGrid : function() {
        return this.groupGrid ? this.groupGrid : (this.groupGrid = new Ext.grid.GridPanel({
            title : this.type === 'host' ? _('Hostgroups') : _('Servicegroups'),
            autoScroll : true,
            store : this.type === 'host' ? this.hostgroupStore : this.servicegroupStore,
            layout : 'fit',
            flex : 1,
            colModel : new Ext.grid.ColumnModel({
                columns : [{
                    header : _('Name'),
                    dataIndex : 'name',
                    xtype: 'ellipsiscolumn'
                }, {
                    header : _('Alias'),
                    dataIndex : 'alias',
                    xtype: 'ellipsiscolumn'
                }]
            }),
            viewConfig : {
                forceFit : true
            }
        }));
    },
    
    initComponent : function() {
        Icinga.Cronks.Tackle.Relation.Head.superclass.initComponent.call(this);
        
        this.initStores();
        
        this.add([
            this.buildContactGrid(), 
            this.buildCustomvarGrid(),
            this.buildGroupGrid()
        ]);
        
        this.on('show', this.loadData.createDelegate(this, [], false));
        
        this.doLayout();
    },
    
    loadDataForObjectId : function(objectId,connection) {
        this.connection = connection || "icinga";
        if (Ext.isEmpty(objectId) === false && objectId === this.objectId) {
            return;
        }
        
        this.objectId = objectId;
        this.loaded = false;
        
        
        if (this.isVisible()) {
            this.loadData();
        }
    },
    
    loadData : function(id) {
        
        if (this.loaded === true) {
            return;
        }
        
        if (Ext.isEmpty(id) && this.objectId) {
            id = this.objectId;
        }
        
        Ext.Ajax.request({
            url : String.format('{0}/web/api/relation/{1}', AppKit.util.Config.get('path'), id),
            params: {
                connection: this.connection
            },
            success : function(response, opts) {
                var data = Ext.decode(response.responseText);
                var root = data.result;
                Ext.each(this.stores, function(c) {
                    c.store.loadData(root[c.root]);
                }, this);
            },
            scope : this
        })
    }
});