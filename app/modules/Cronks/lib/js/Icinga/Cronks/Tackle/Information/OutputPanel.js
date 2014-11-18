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

/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Information');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Information.OutputPanel = Ext.extend(Ext.Panel, {
        autoScroll: true,
        
        constructor: function(c) {
            Icinga.Cronks.Tackle.Information
                .OutputPanel.superclass.constructor.call(this, c);
        },
        
        initComponent: function() {
            
            if (!Ext.isEmpty(this.tplField)) {
                this.tpl = new Ext.XTemplate(
                    '<tpl for=".">', 
                    '<div style="margin: 5px;">', 
                    '{' + this.tplField + '}', 
                    '</div>', 
                    '</tpl>'
                );
            }
            
            Icinga.Cronks.Tackle.Information
                .OutputPanel.superclass.initComponent.call(this);
        }
    });

})();