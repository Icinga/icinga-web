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

Ext.ns('Icinga.Reporting.abstract');

Icinga.Reporting.abstract.ResizedContainer = Ext.extend(Ext.Container, {

    constructor : function(config) {
        Icinga.Reporting.abstract.ResizedContainer.superclass.constructor.call(this, config);
    },

    initComponent : function() {
        Icinga.Reporting.abstract.ResizedContainer.superclass.initComponent.call(this);

        this.on('afterrender', function() {
            var p = this.findParentByType('tabpanel');

            var setSize = false;

            p.on('resize', function(tb, adjWidth, adjHeight, rawWidth, rawHeight) {
                if (setSize == false) {
                    this.setHeight(adjHeight-53);
                    setSize = true;
                } else {
                    setSize = false;
                }
            }, this);

        }, this, { single : true });



        var resizeFn = function(c) {
            var p = this.findParentByType('tabpanel');
            if (p) {
                this.setHeight(p.getInnerHeight()-26);
            }
        }

        this.on('afterrender', resizeFn, this, { single : true });
//      this.on('resize', resizeFn, this, { single : true });
//      Ext.EventManager.onWindowResize(resizeFn, this);
    }

});
