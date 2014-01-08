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

/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Comment');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Comment.Panel = Ext.extend(Ext.Panel, {
        title: _('Comments'),
        iconCls: 'icinga-icon-comment',
        type: null,

        constructor: function (config) {

            if (Ext.isEmpty(config.type)) {
                throw ("config.type is needed: host or service!");
            }

            config.layout = 'border';

            Icinga.Cronks.Tackle.Comment.Panel.superclass.constructor.call(this, config);
        },

        initComponent: function () {
            Icinga.Cronks.Tackle.Comment.Panel.superclass.initComponent.call(this);

            this.grid = new Icinga.Cronks.Tackle.Comment.Grid({
                type: this.type,
                parentCmp: this,
                region: 'center'
            });

            this.form = new Icinga.Cronks.Tackle.Comment.CreateForm({
                type: this.type,
                parentCmp: this,
                region: 'east',
                width: 400,
                collapsed: true,
                collapsible: true
            });

            this.add(this.grid, this.form);

            this.doLayout(false, true);
        }
    });

})();