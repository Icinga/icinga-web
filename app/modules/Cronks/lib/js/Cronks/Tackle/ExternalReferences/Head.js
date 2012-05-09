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

/*global Ext: false, Icinga: false, _: false */
/* 
 * Displays references to external files (like notes/action_urls)
 */

(function() {
    "use strict";

    Ext.ns("Icinga.Cronks.Tackle.ExternalReferences").Head = Ext.extend(Ext.Panel,{
        layout: 'hbox',
        layoutConfig: {
            align: 'stretch',
            pack: 'start'
        },
        fields: [],
        title: _('External References'),
        iconCls: 'icinga-icon-anchor',
        store: null,

        constructor: function (config) {
            if (Ext.isEmpty(config.type)) {
                throw ("config.type is needed: host or service!");
            }
            this.targetType = config.type;
            this.connection = config.connection || "icinga";
            Icinga.Cronks.Tackle.ExternalReferences.Head.superclass.constructor.call(this, config);
        },

        initComponent: function () {
            Icinga.Cronks.Tackle.ExternalReferences.Head.superclass.initComponent.call(this);
            var field_target = this.targetType.toUpperCase();
            
            this.urlList = new Icinga.Cronks.Tackle.ExternalReferences.UrlList();
            this.iFrame = new Icinga.Cronks.Tackle.ExternalReferences.PreviewFrame();
            
            this.urlList.on("selectionchange",function() {
                var record = this.urlList.getSelectedRecords()[0];
                if(typeof record === "undefined") {
                    return false;
                }
                this.iFrame.setContentURL(record.get("url"));
                return true;

            },this,{buffer:true});

            this.store = new Icinga.Api.RESTStore({
                autoDestroy: true,
                idIndex: 0,
                target: this.targetType,
                columns: [field_target+"_ACTION_URL",field_target+"_NOTES_URL"],
                connection: this.connection
            });
            this.store.on("load",this.updateURLList,this);

            this.add(this.urlList);
            this.add(this.iFrame);
        },

        updateURLList: function(store,resultSet) {
            if(resultSet.length == 0) {
                return;
            }
            this.iFrame.reset();
            var result = [];
            for(var type in resultSet[0].data) {
                var data = resultSet[0].data[type];
                if(data == "")
                    continue;
                var urlType = null;
                if(/.*notes_url$/i.test(type)) {
                    urlType = 'Notes';
                } else if(/.*action_url$/i.test(type)) {
                    urlType = 'Action';
                }
                if(urlType == null)
                    continue;
                if(data[0] == "'")
                    data = data.substr(1,data.length-2);

                var dataSet = data.split(/' +'/);
                for(var i=0;i<dataSet.length;i++) {
                    result.push({
                        type: urlType,
                        url: dataSet[i]
                    });
                }
            }
            this.urlList.getStore().loadData(result);

        },

        loadDataForObjectId: function (oid,connection) {
            var field = String.format('{0}_OBJECT_ID', this.type.toUpperCase());
            this.store.connection = connection;
            this.store.setFilter({
                type: 'AND',
                field: [{
                    type: 'atom',
                    field: [field],
                    method: ['='],
                    value: [oid]
                }]
            });

            this.store.load();
        }
    });

})();



