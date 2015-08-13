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

/*global Ext: false, Icinga: false, _: false */
(function() {
 //   "use strict";

    Ext.ns("Icinga.Cronks.Tackle.ExternalReferences").UrlList = Ext.extend(Ext.list.ListView,{
        layout : 'fit',
        flex : 1,

        events: {
            "urlselected" : true
        },

        constructor: function(cfg) {
            cfg = cfg || {};
            Ext.apply(cfg,{
                columns: [{
                    header: 'Type',
                    width: .2,
                    dataIndex: 'type'
                },{
                    header: 'URL',
                    width: .8,
                    dataIndex: 'url'
                }]
            });
            cfg.title = _("Url list");
            cfg.singleSelect = true;
            Ext.list.ListView.prototype.constructor.call(this,cfg);
        },
        initComponent: function() {
            Ext.list.ListView.prototype.initComponent.apply(this,arguments);
            this.store = new Ext.data.JsonStore({
                fields: ['type','url']
            });

        },

        getStore: function() {
            return this.store;
        },

        viewConfig : {
            forceFit : true
        }

    });
})();
