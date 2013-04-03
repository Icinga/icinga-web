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


Ext.ns("Icinga.Cronks.Tackle.Renderer").AdditionalURLColumnRenderer = function(type) {

   return function(v,metaData,record) {
        v = v.trim();
        var urls = v.split(" ");
        var text = "";
        type = type.toUpperCase();

        var html = {
            tag: 'div',
            children: []
        };
        if(record.get(type+"_NOTES_URL")) {
            html.children.push({
                tag: 'div',
                cls: 'icinga-icon-note',
                style: 'width:24px;height:24px;float:left'
            });
        }

        if(record.get(type+"_ACTION_URL")) {
            html.children.push({
                tag: 'div',
                cls: 'icinga-icon-cog',
                style: 'width:24px;height:24px;float:left'
            });
        }
        return Ext.DomHelper.markup(html);
    };
};

Ext.ns("Icinga.Cronks.Tackle.Renderer").AdditionalURLColumnClickHandler = function(type) {
    
    return function(col,grid,rowIdx,e) {
        var type = "SERVICE";
        if(/^HOST.*/.test(col.dataIndex)) {
            type = "HOST";
        }
        var row = this.getView().getRow(rowIdx);
        var record = this.getStore().getAt(rowIdx);
        var urls = {
            "Notes url" : {
                data: record.get(type+"_NOTES_URL"),
                icon: 'icinga-icon-note'
            },
            "Action url" : {
                data: record.get(type+"_ACTION_URL"),
                icon: 'icinga-icon-cog'
            }
        };
        var menu = {
            items: []
        };
        var href = Cronk.util.InterGridUtil.openExternalCronk;
        for(var urltype in urls) {
            if(!urls[urltype].data) {
                continue;
            }
            var curURLs = urls[urltype].data.split(" ");
            for(var i=0;i<curURLs.length;i++) {
                var url = curURLs[i];
                // Multiple urls are wrapped '', so we have to check those before we can use the url
                if(/^'.*'$/.test(url)) {
                    menu.items.push({
                        text: urltype+(i+1),
                        iconCls: urls[urltype].icon,
                        handler: href.createCallback(record.get(type+"_NAME")+" "+urltype,url.substring(1,url.length-1))
                    });
                } else {
                    menu.items.push({
                        text: urltype+(i+1),
                        iconCls: urls[urltype].icon,
                        handler: href.createCallback(record.get(type+"_NAME")+" "+urltype,url)
                    });
                    break;
                }
            }
        }
        if(menu.items.length) {
            (new Ext.menu.Menu(menu)).showAt(e.getXY());
        }
    };
};