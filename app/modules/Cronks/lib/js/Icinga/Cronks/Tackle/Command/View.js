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

Ext.ns('Icinga.Cronks.Tackle.Command');

Icinga.Cronks.Tackle.Command.View = Ext.extend(Ext.DataView, {
    
    tpl : new Ext.XTemplate(
        '<tpl for=".">',
        '<div class="tackle-command-view-item">',
        '<div class="tackle-command-view-item-inline icon-16 {iconCls}"></div>',
        '<div class="tackle-command-view-item-inline">{label} ({definition})</div>',
        '</div>',
        '</tpl>'
    ),
    
    itemSelector : 'div.tackle-command-view-item',
    overClass : 'tackle-command-view-item-over',
    autoScroll : true,
    
    constructor : function(config) {
        Icinga.Cronks.Tackle.Command.View.superclass.constructor.call(this, config);
    },
    
    initComponent : function() {
        Icinga.Cronks.Tackle.Command.View.superclass.initComponent.call(this);
    }
});