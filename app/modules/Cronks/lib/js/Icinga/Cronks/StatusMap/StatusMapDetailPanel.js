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
/*jshint curly: true */
/*global Ext:true, AppKit:true, _:true, Cronk:true,Icinga:true*/
Ext.ns("Icinga.Cronks").StatusMapDetailPanel = function(tpl) {
    "use strict";
    var dview = new Ext.DataView({
        tpl: tpl,
        style: 'padding: 5px',
        height:230,
        overClass:'x-view-over',
        itemSelector:'div.thumb-wrap',
        emptyText: 'No node selected'
    });
    var setupLinks = function() {
        AppKit.log(this);
        var ele = this.el;
        var links = ele.select('a[subgrid]');
        links.each(function (el) {
            var subgrid = el.getAttribute('subgrid');
            if (subgrid) {
                var params = subgrid.split(':');

                el.on('click', function () {
                    Cronk.util.InterGridUtil.gridFilterLink({
                        crname: 'gridProc',
                        closable: true,
                        parentid: 'statusmap-gridproc-' + params[1],
                        title: String.format(_('Detail for {0}'), params[3]),
                        params: {
                            template: params[0]
                        }
                    }, {
                        'f[host_object_id-value]': params[2],
                        'f[host_object_id-operator]': 50
                    });
                }, this);
                el.setStyle("cursor","pointer");
                el.set({"ext:qtip":_("Click for hostgrid")});
            }
        }, this);
    };
    dview.update = dview.update.createSequence(setupLinks,dview);
    
    
    var openProblemsGrid = new Icinga.Cronks.StatusMapServiceGrid({
        filter: {
            type: 'AND',
            field: [{
                type: 'atom',
                method: ['='],
                value: '0',
                field: ['SERVICE_PROBLEM_HAS_BEEN_ACKNOWLEDGED']
            },{
                type: 'atom',
                method: ['='],
                value: '0',
                field: ['SERVICE_SCHEDULED_DOWNTIME_DEPTH']
            },{
                type: 'atom',
                method: ['!='],
                value: '0',
                field: ['SERVICE_CURRENT_STATE']
            }]
        },
        autoLoad:false,
        parent: dview
    });
    
    var panel = new Ext.Panel({
        layout:'vbox',
        region: 'east',
        width:380,
        collapsible: true,
        collapsed: true,
        unstyled:true,
        split: true,
        defaults: {
            flex: 1
        },
        listeners: {
            resize: function(cmp) {
                dview.setWidth(cmp.getWidth()-10);
                openProblemsGrid.setWidth(cmp.getWidth()-10);
                
            }
        },
        items: [
            dview,
            openProblemsGrid
        ]
    });
    
    this.update = function(node) {
        dview.update.apply(dview,arguments);
        openProblemsGrid.setHostId(node.HOST_ID);
        openProblemsGrid.getStore().load();
    };
    
    this.getPanel = function() {
        return panel;
    };
    
};