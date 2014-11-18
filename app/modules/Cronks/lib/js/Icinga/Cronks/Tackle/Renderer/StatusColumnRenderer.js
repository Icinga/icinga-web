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
Ext.ns('Icinga.Cronks.Tackle.Renderer');

(function () {
    "use strict";

    var openProblemHandler = {
        applies: function(record) {
            var type = record.get('SERVICE_ID') ? 'SERVICE' : 'HOST';
            return (parseInt(record.get(type+'_CURRENT_PROBLEM_STATE'), 10) > 0);
        },
        getTpl: function() {
            return "<div class='cancelable icinga-icon-exclamation-red' style='cursor:pointer;width:16;height:16px;margin:auto' ext:qtip='" +_('Open Problem')+ "'></div>";
        },
        applyClickHandler: function(el,record) {
            var type = record.get('SERVICE_ID') ? 'SERVICE' : 'HOST';
            var targets = [{instance: record.get('INSTANCE_NAME'), host: record.get('HOST_NAME')}];
            targets[0][type.toLowerCase()] = record.get(type+'_NAME');

            el.addListener('click', function (e) {
                (new Ext.menu.Menu({
                    items: [{
                        text: _('Acknowledge problem'),
                        iconCls: 'icinga-icon-info-problem-acknowledged',
                        handler: function() {
                            var w = new Ext.Window({title: _('Acknowledge problem'), width: 500, height: 400, renderTo: Ext.getBody(), layout:'fit'});
                            var b = new Icinga.Api.Command.FormBuilder();
                            var i = b.build('ACKNOWLEDGE_'+(type == 'SERVICE' ? 'SVC' : type)+'_PROBLEM', {
                                renderSubmit : true,
                                targets : targets,
                                cancelHandler : function(form, action) {w.hide();}

                            });
                            w.add(i);
                            i.toolbars[0].items.get(0).on("click",function() {
                                w.hide();
                            },this);
                            w.show(Ext.getBody());
                        }
                    },{
                        text: _('Add comment'),
                        iconCls: 'icinga-icon-comment',
                        handler: function() {
                            var w = new Ext.Window({title: _('Add comment'), width: 500, height: 300, renderTo: Ext.getBody(), layout:'fit'});
                            var b = new Icinga.Api.Command.FormBuilder();
                            var i = b.build('ADD_'+(type == 'SERVICE' ? 'SVC' : type)+'_COMMENT', {
                                renderSubmit : true,
                                targets : targets,
                                cancelHandler : function(form, action) {w.hide();}
                            });
                            w.add(i);
                            i.toolbars[0].items.get(0).on("click",function() {
                                w.hide();
                            },this);
                            w.show(Ext.getBody());
                        }
                    }]
                 })).showAt(e.getXY());
                
            }, this);
        }
    };

    /**
     * Column strategy for acked-hosts
     */
    var ackHandler = {
        applies: function(record) {
            var type = record.get('SERVICE_ID') ? 'SERVICE' : 'HOST';
            return (parseInt(record.get(type+'_PROBLEM_HAS_BEEN_ACKNOWLEDGED'), 10) > 0);
        },
        getTpl: function() {
            return "<div class='cancelable icinga-icon-info-problem-acknowledged' style='cursor:pointer;width:25px;height:16px;margin:auto' ext:qtip='" +_('Problem has been acknowledged')+ "'></div>";
        },
        applyClickHandler: function(el,record) {
            var type = record.get('SERVICE_ID') ? 'SERVICE' : 'HOST';
            el.addClass('icinga-icon-cancel');
            el.addListener('click', function () {
                 Ext.Msg.show({
                    title:  _('Remove acknwoledgement'),
                    msg:_('Do you want to remove this acknowledgment?'),
                    buttons: Ext.Msg.YESNO,
                    icon: Ext.Msg.WARNING,
                    fn: function(btn) {
                        if(btn == "yes") {
                            var cmd =
                            {command : 'REMOVE_'+(type === 'SERVICE' ? 'SVC' : type)+'_ACKNOWLEDGEMENT',
                                data : {
                                    host: record.get('HOST_NAME')
                                },
                                targets : [{
                                    instance: record.get('INSTANCE_NAME'),
                                    host: record.get('HOST_NAME')
                                }]
                            };
                            if(type == 'SERVICE') {
                                cmd.data['service'] = record.get(type+'_NAME');
                                cmd.targets[0]['service'] = record.get(type+'_NAME');
                            }
                            Icinga.Api.Command.Facade.sendCommand(cmd);
                        }
                    },
                    scope:this
                });
            }, this);
        }
    };
    /**
     * Column strategy for downtime-hosts
     */
    var dTimeHandler = {
        applies: function(record) {
            var type = record.get('SERVICE_ID') ? 'SERVICE' : 'HOST';
            return (parseInt(record.get(type+'_SCHEDULED_DOWNTIME_DEPTH'), 10) > 0);
        },
        getTpl: function() {
            return "<div class='cancelable icinga-icon-info-downtime' style='cursor:pointer;width:25px;height:16px;margin:auto' ext:qtip='" +_('Object is currently in a downtime')+ "'></div>";
        },
        applyClickHandler: function(el,record) {
            el.addClass('icinga-icon-cancel');
            var type = 'HOST';
            if(record.get('SERVICE_ID'))
                type = 'SERVICE';
            el.addListener('click', function () {
                 Ext.Msg.show({
                    title:  _('Cancel downtime'),
                    msg: _('Do you want to cancel this downtime?'),
                    buttons: Ext.Msg.YESNO,
                    icon: Ext.Msg.WARNING,
                    fn: function(btn) {
                        if(btn == "yes") {
                            var cmd = {
                                command : 'DEL_DOWNTIME_BY_'+(type == 'SERVICE') ? 'SVC' : 'HOST'+'_NAME',
                                data : {host: record.get(type+'_NAME')},
                                targets : [{
                                    instance: record.get('INSTANCE_NAME'),
                                    host: record.get(type+'_NAME')
                                }]
                            };
                            if(type == 'SERVICE') {
                                cmd.data['service'] = record.get(type+'_NAME');
                                cmd.targets[0]['service'] = record.get(type+'_NAME');
                            }
                            Icinga.Api.Command.Facade.sendCommand(cmd);
                        }
                    },
                    scope:this
                });  
            }, this);
        }
    };

    var statusColRenderStrategies = [
        ackHandler,
        dTimeHandler,
        openProblemHandler
    ];

    Icinga.Cronks.Tackle.Renderer.StatusColumnRenderer = function (value, metaData, record, rowIndex, colIndex, store) {
        var type = record.get('SERVICE_ID') ? 'SERVICE' : 'HOST';
        value = parseInt(value, 10);

        switch (value) {
            case 0:
                metaData.css = 'icinga-status-up';
                break;
            case 1:
                if(type == 'SERVICE')
                    metaData.css = 'icinga-status-warning';
                else
                    metaData.css = 'icinga-status-down';
                break;
            case 2:
                if(type == 'SERVICE')
                    metaData.css = 'icinga-status-critical';
                else
                    metaData.css = 'icinga-status-unreachable';
                break;
            case 3:
                metaData.css = 'icinga-status-unreachable';
                break;
            case 99:
                metaData.css = 'icinga-status-pending';
                break;
        }
        var id = Ext.id();

        var render = function (nrOfTry) {
            nrOfTry = nrOfTry || 1;

            if(!Ext.get(id)) {
                if(nrOfTry < 4)
                    render.defer(100,this,[nrOfTry+1]);
                return false;
            }

            var tpl;

            // apply status column render strategy
            for(var i = 0;i<statusColRenderStrategies.length;i++) {
                if(statusColRenderStrategies[i].applies(record))
                    tpl = statusColRenderStrategies[i].getTpl();
            }

            var cmp = new Ext.BoxComponent({
                layout: 'fit',
                html: tpl,
                renderTo: id,
                listeners: {
                    render: function (htmlCmp) {

                        // Append the Panel to the click handler's argument list.
                        htmlCmp.getEl().on('mouseenter', function (e) {
                            var el = cmp.getEl().first('.cancelable');
                            // apply status column render strategy
                            for(var i = 0;i<statusColRenderStrategies.length;i++) {
                                if(statusColRenderStrategies[i].applies(record))
                                    statusColRenderStrategies[i].applyClickHandler(el, record);
                            }

                        }, this);
                        htmlCmp.getEl().on('mouseleave', function () {
                            htmlCmp.update(tpl);
                        }, this);
                    },
                    scope: this
                }
            });
        };
        
        render.defer(100);
        return "<div id='" + id + "'></div>";
    };

})();