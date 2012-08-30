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

Ext.ns("AppKit.Admin.Components");
AppKit.Admin.Components.RoleListingGrid = Ext.extend(Ext.grid.GridPanel,{
    title: _('Available roles'), 
    region: 'center',
    layout: 'fit',
    stateful: false,
    autoScroll: true,
    sm: new Ext.grid.RowSelectionModel(),
    iconCls: 'icinga-icon-group',
    
    constructor: function(cfg) {
        Ext.grid.GridPanel.prototype.constructor.call(this,cfg);
    },
    
    initComponent: function() {
        this.bbar = [];
        
        AppKit.Admin.Components.RoleListingGrid.superclass.initComponent.call(this);
        
        this.counterLabel = this.getBottomToolbar().add({
            xtype : 'tbtext',
            tpl: new Ext.Template(_('{0} roles loaded.'))
        });
        
        this.store.on('load', function(store, records, o) {
            this.counterLabel.update([store.getCount()]);
        }, this);
        
        var model = this.getSelectionModel();
        if (model) {
            /*
             * Before this was bound to rowclick event. If we trigger injected selection
             * rowclick was never fired, bind to rowselect is the better choice I think
             */
            model.on('rowselect', function(sm, rowIndex, r) {
                var id = this.getStore().getAt(rowIndex).get("id");
                Ext.getCmp('roleEditor').setDisabled(false);
                Ext.getCmp('btn-save-group').setText(_('Save'));
                Ext.getCmp('btn-save-group').setIconClass('icinga-icon-disk');
                Ext.getCmp('progressbar-field').setValue();
                AppKit.Admin.RoleEditForm.bindRole(id, this.roleProviderURI);
            }, this);
        }
    },
    
    deleteSelected: function() {
        Ext.Msg.confirm(_("Delete role"),_("Do you really want to delete these roles?"),function(btn) {
            if(btn != "yes")
                return false;
            var selModel = this.getSelectionModel();
            var selected = selModel.getSelections();
            var ids = [];

            Ext.each(selected,function(record) {
                ids.push(record.get("id"));
            },this);
            var uri = this.roleProviderURI+"/ids="+ids.join(",");                        
            Ext.Ajax.request({
                url: uri,
                method: 'DELETE',
                success: function() {
                    this.getStore().reload();
                },
                scope:this,
                params: ids

            });
        },this);
    },
    viewConfig : {
        scrollOffset:30,
        forceFit:true
    },
    tbar: {
        items: [{
            xtype: 'button',
            iconCls: 'icinga-icon-arrow-refresh',
            scope:this,
            text: 'Refresh',
            handler: function(c) {
                if (!Ext.isEmpty(c.ownerCt.ownerCt.store) && "reload" in c.ownerCt.ownerCt.store) {
                    c.ownerCt.ownerCt.store.reload();
                }
            }

        },{
            xtype: 'button',
            iconCls: 'icinga-icon-cancel',
            text: _('Remove selected'),
            handler: function(c,btn) {
                c.ownerCt.ownerCt.deleteSelected();
            },
            scope: this
        },' ',{
            xtype: 'button',
            iconCls: 'icinga-icon-add',
            text: _('Add new role'),
            handler: function(c) {
                AppKit.Admin.RoleEditForm.bindRole('new', c.ownerCt.ownerCt.roleProviderURI);
                Ext.getCmp('roleEditor').setDisabled(false);
                Ext.getCmp('btn-save-group').setText(_('Create role'));
                Ext.getCmp('btn-save-group').setIconClass('icinga-icon-group-add');
                Ext.getCmp('progressbar-field').setValue();
            }

        },'->',{
            xtype:'button',
            enableToggle:true,
            text: _('Hide disabled'),
            id:'hide_disabled',
            name: 'disabled',
            listeners: {
                toggle: function(btn,checked) {
                    btn.ownerCt.ownerCt.getStore().setBaseParam('hideDisabled',checked);
                    return true;
                }
            }
        }]

    },

    colModel: new Ext.grid.ColumnModel({
        defaults: {
            width:120,
            sortable:true
        },
        columns: [
            {id:'id', header: 'ID', width:75,  dataIndex: 'id'},
            {header: _('Name'), dataIndex: 'name'},
            {header: _('Description'), dataIndex: 'description'},
            {header: _('active'), dataIndex: 'disabled_icon',width:75}
        ]
    })

});