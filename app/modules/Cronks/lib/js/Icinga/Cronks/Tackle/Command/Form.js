// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
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

Icinga.Cronks.Tackle.Command.Form = Ext.extend(Ext.Panel, {
    title : _('Commands'),
    
    formBuilder : null,
    record : {},
    autoScroll : true,
    
    constructor : function(config) {
        Ext.apply(this,config);
        Icinga.Cronks.Tackle.Command.Form.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
        Icinga.Cronks.Tackle.Command.Form.superclass.initComponent.call(this);
        
        this.formBuilder = new Icinga.Api.Command.FormBuilder();
    },
    
    setRecord : function(record) {
        this.record = record;
    },
    
    rebuildFormForCommand : function(commandName) {
        var title = String.format(_('Command: {0}'), commandName);
        this.setTitle(title);
        
        this.removeAll();
        
        if (this.record) {
            this.target = this.record;
        }
        
        this.form = this.formBuilder.build(commandName, {
            renderSubmit: this.standalone,
            targets: Ext.isArray(this.target) ? this.target : [this.target]
        });
        
        this.add(this.form);
        
        this.doLayout();
    }
});
