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
/*jshint browser:true, curly:false */
(function() {
    
var getStatusForm = function(filterCfg, radiobtns) {

    // Dynamic label by config for host- or servicestatus
    var label = String.format(_("Filter by {0}:"), filterCfg.label);

    return {
        xtype: 'form',
        width: 160,
        height: 150,
        padding:5,
        items: [{
            border: false,
            html: '<div style="font-size:12px;font-weight:bold">'+ label +'</div><br/>'
        },{
            xtype: 'hidden',
            name: 'field',
            value: filterCfg.name
        },{    
            xtype: 'hidden',
            name: 'label',
            value: filterCfg.label
        },{
            xtype: 'hidden',
            name: 'operator',
            value: '='
        },{
            name: 'value',
            xtype: 'radiogroup',
            getValue: function() {
                for(var i=0;i<this.items.items.length;i++) {
                    var field = this.items.items[i];
                    if(field.getValue()) {
                        return i;
                    }
                };
                return 0
                

            },
            columns: 1,
            items: radiobtns
        }]
    }

}

Ext.ns('Icinga.Cronks.util.FilterTypes').Statetype = function(filterCfg,defaults) {
    //console.log(filterCfg);
    return getStatusForm(filterCfg,[{
        checked: defaults['value'] == 0 || defaults == {},
        inputValue: 0,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Soft')
    },{
        checked: defaults['value'] == 1,
        inputValue: 1,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Hard')
    }]);
};

Ext.ns('Icinga.Cronks.util.FilterTypes').Hoststatus = function(filterCfg,defaults) {
    //console.log(filterCfg);
    return getStatusForm(filterCfg,[{
        checked: defaults['value'] == 0 || defaults == {},
        inputValue: 0,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Up')
    },{
        checked: defaults['value'] == 1,
        inputValue: 1,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Down')
    },{
        checked: defaults['value'] == 2,
        inputValue: 2,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Unreachable')
    }]);
};

Ext.ns('Icinga.Cronks.util.FilterTypes').Servicestatus = function(filterCfg,defaults) {
   return getStatusForm(filterCfg,[{
        checked: defaults['value'] == 0 || defaults == {},
        inputValue: 0,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('OK')
    },{
        checked: defaults['value'] == 1,
        inputValue: 1,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Warning')
    },{
        checked: defaults['value'] == 2,
        inputValue: 2,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Critical')
    },{
        checked: defaults['value'] == 3,
        inputValue: 2,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Unknown')
    }]);
       
};

})();
