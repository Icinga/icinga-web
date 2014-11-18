// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2014 Icinga Developer Team.
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

Ext.ns('Icinga.Cronks.Tackle.Command');

Icinga.Cronks.Tackle.Command.Panel = Ext.extend(Ext.Panel, {
    
    title : _('Commands'),
    iconCls : 'icinga-icon-bricks',
    layout : 'hbox',
    layoutConfig : {
        align : 'stretch',
        pack : 'start'
    },
    
    constructor : function(config) {
        if (Ext.isEmpty(config.type)) {
                throw ("config.type is needed: host, service, hostgroup, servicegroup, process");
        }
        
        this.store = new Ext.data.JsonStore({
            url : AppKit.c.path+'/modules/appkit/dispatch',
            baseParams : {
                module : 'Api',
                action : 'ApiCommandInfo',
                outputType : 'json',
                params : {
                    extjs : 1
                }
            }
        });
        if(typeof config.standalone === "undefined")
            this.isStandaloneComponent = true; // show buttons
        else
            this.isStandaloneComponent = config.standalone;
        Icinga.Cronks.Tackle.Command.Panel.superclass.constructor.call(this, config);
    },
    
    setType : function(type) {
        this.store.setBaseParam('params', Ext.encode({
            extjs : 1,
            type : type
        }));
        
        this.store.load();
    },
    
    initComponent : function() {
        Icinga.Cronks.Tackle.Command.Panel.superclass.initComponent.call(this);
        
        this.setType(this.type);
        
        this.dataview = new Icinga.Cronks.Tackle.Command.View({
            store : this.store,
            flex : 1
        });
        
        this.dataview.on('click', this.onCommandClick, this);
        
        this.form = new Icinga.Cronks.Tackle.Command.Form({
            standalone: this.isStandaloneComponent,
            flex : 1
        });
        
        this.add(this.dataview, this.form);
        
        this.doLayout();
    },

    submit: function(targets) {
         var fPanel = this.form.form;
         var form = fPanel.getForm();
         fPanel.formAction.setTargets(targets);
         form.doAction(fPanel.formAction);
    },

    onCommandClick : function(dataView, index, node, e) {
        var record = this.store.getAt(index);
        this.form.rebuildFormForCommand(record.data.definition);
    }
});