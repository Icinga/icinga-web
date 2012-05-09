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
AppKit.Admin.Components.CredentialGrid = Ext.extend(Ext.Panel,{
    title: _('Credentials'),
    layout:'fit',
    iconCls: 'icinga-icon-key',
    
    
    
    constructor: function(cfg) {
        Ext.apply(this.cfg);
        cfg.tbar = [_('Define credentials and access rights to this ')+_(cfg.type)+_(' here')];
        this.selectionModel = new Ext.grid.CheckboxSelectionModel({
            width: 20,
            checkOnly: true,
            listeners: {
                selectionchange: function(_this) {
                    this.store.selectedValues = _this.getSelections();
                },
                scope:this
            }
        });
        cfg.items = [{
            xtype: 'grid',
            store: cfg.store,
            viewConfig: {
                forceFit: true
            },
            sm: this.selectionModel,

            columns: [ 
                this.selectionModel,
            {
                header: _('Credential'),
                dataIndex: 'target_name',
                width: 100
            },{
                header: _('Description'),
                dataIndex: 'target_description',
                width: 300
            }]
        }];
        Ext.Panel.prototype.constructor.call(this,cfg);
    },
    
    updateView: function() {
        if(this.store.selectedValues)
            this.selectionModel.selectRecords(this.store.selectedValues);
    },
    selectValues: function(principals) {
        this.selectionModel.clearSelections();
        this.store.selectedValues = [];
        Ext.iterate(principals, function(p) {
            if(p.target.target_type != 'credential') 
                return true;
            this.store.selectedValues.push(this.store.getById(p.target.target_id));
        },this);
        this.updateView();
    }
});
