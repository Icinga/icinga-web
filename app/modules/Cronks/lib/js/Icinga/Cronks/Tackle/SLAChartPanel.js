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

/*global Ext: false, Icinga: false, _: false, $jit: false, AppKit: false */
Ext.ns('Icinga.Cronks.Tackle.Information');

(function () {
    "use strict";
    Icinga.Cronks.Tackle.SLAChartPanel = Ext.extend(Ext.Panel, {
        title : _('SLA Information'),
        iconCls : 'icinga-icon-chart-pie',
        layout : 'hbox',
        layoutConfig : {
            align : 'stretch',
            pack : 'start'
        },
        constructor: function(cfg) {
            this.type = cfg.type;
            cfg = cfg || {};
            this.id = Ext.id();
            this.record = null;
            cfg.items = [{
                xtype: 'panel',
                title: _('Last month'),
                layout: 'fit',
                width: '33%',
                items: {
                    html: '<div id="sla_month_'+this.id+'" style="height:100%;width:100%"></div>'
                }
            }, {

                xtype: 'panel',
                title: _('Last year'),
                layout: 'fit',
                width: '33%',
                items: {
                    html: '<div id="sla_year_'+this.id+'" style="height:100%;width:100%"></div>'
                }
            }, {
                xtype: 'panel',
                title: _('Overall'),
                layout: 'fit',
                width: '34%',
                items: {
                    html: '<div id="sla_overall_'+this.id+'" style="height:100%;width:100%"></div>'
                }
            }];
            cfg.listeners = {
                show: function() {
                    this.loadSLAData();
                },
                hide: function() {
                    this.clearSLAFields();
                },
                scope: this
            };
            Ext.Panel.prototype.constructor.call(this,cfg);
        },
        updateRecord: function(record) {
            this.record = record;
            if(this.isVisible()) {
                this.loadSLAData();
            }
        },

        clearSLAFields: function() {
            if(!Ext.get('sla_year_'+this.id)) {
                return;
            }

            Ext.get('sla_year_'+this.id).update("");
            Ext.get('sla_month_'+this.id).update("");
            Ext.get('sla_overall_'+this.id).update("");
        },

        loadSLAData: function() {
            if(!this.record) {
                return;
            }
            this.clearSLAFields();
            var object_id = this.record.get(this.type.toUpperCase()+"_OBJECT_ID");
            this.requestFor(object_id,"-1 month","sla_year");
            this.requestFor(object_id,"-1 year","sla_month");
            this.requestFor(object_id,null,"sla_overall");
        },

        requestFor: function(id,timespan,cb) {
            Ext.Ajax.request({
                url: AppKit.c.path+"/web/api/sla/ids["+id+"]/"+(timespan ? "timespan["+timespan+"]" : "" )+"/json",
                success: function(result) {
                    
                    var data = Ext.decode(result.responseText);
                    this.drawPieChart(cb,data);

                },
                scope:this
            });
        },
        
        getState: function(code) {
            switch(parseInt(code,10)) {
                case 0:
                    return this.type === 'host' ? _('Up') : _('Ok');
                case 1:
                    return this.type === 'host' ? _('Down') : _('Warning');
                case 2:
                    return this.type === 'host' ? _('Unreachable') : _('Critical');
                case 3:
                    return this.type === 'host' ? '' : _('Unknown');
            }
        },
        getStateColor: function(code) {
            switch(parseInt(code,10)) {
                case 0:
                    return '#00ff00';
                case 1:
                    return this.type === 'host' ? '#ff0000' : '#ffff00';
                case 2:
                    return this.type === 'host' ? '#ffaa00' : '#ff0000';
                case 3:
                    return this.type === 'host' ? '' : '#ffaa00';
            }
        },
        slaToPieChartJSON: function(data) {

            var json = {
                label: ['SLA Overview'],
                color: [],
                values : []
            };
            
            for(var i=0;i<data.length;i++) {
                
                data[i].percentage = parseFloat(data[i].percentage.replace(",","."),10);
                if(isNaN(data[i].percentage)) {
                    AppKit.log("Object returned NaN as percentage");
                    return {};
                }

                json.values.push({
                    label: this.getState(data[i].sla_state),
                    values: data[i].percentage < 0.1 ? 0.1 : data[i].percentage
                });
                json.color.push(this.getStateColor(data[i].sla_state));
            }
            
            return json;
        },

        drawPieChart: function(targetid,data) {
          

            var pieChart = new $jit.PieChart({
              injectInto: targetid+"_"+this.id,           
              animate: true,
              
              offset: 30,
              sliceOffset: 5,
              labelOffset: 50,
              
              type: 'stacked:gradient',
              
              showLabels:true,
              updateHeights: false,
              Label: {
                type: 'HTML', //Native or HTML
                size: 12,
                family: 'arial',
                color: 'black'
              },
              //enable tips
              Tips: {
                enable: true,
                onShow: function(tip, elem) {
                   tip.innerHTML = "<b>" + elem.label + "</b>: " + elem.value;
                }
              }
            });
            //load JSON data.
            pieChart.loadJSON(this.slaToPieChartJSON(data));
        }

    });
})();
