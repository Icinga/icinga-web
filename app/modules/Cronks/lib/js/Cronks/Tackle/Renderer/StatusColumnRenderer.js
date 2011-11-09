/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Renderer');

(function () {
    "use strict";

    var openProblemHandler = {
        applies: function(record) {
            return (parseInt(record.get('HOST_CURRENT_PROBLEM_STATE'), 10) > 0);
        },
        getTpl: function() {
            return "<div class='cancelable icinga-icon-exclamation-red' style='cursor:pointer;width:16;height:16px;margin:auto' qtip='" +_('Open Problem')+ "'></div>";
        },
        applyClickHandler: function(el,record) {
            el.addListener('click', function (e) {
                (new Ext.menu.Menu({
                    items: [{
                        text: _('Acknowledge host problem'),
                        iconCls: 'icinga-icon-info-problem-acknowledged',
                        handler: function() {
                            var w = new Ext.Window({ title: _('Acknowledge Host problem'), width: 500, height: 400, renderTo: Ext.getBody(), layout:'fit' });
                            var b = new Icinga.Api.Command.FormBuilder();
                            var i = b.build('ACKNOWLEDGE_HOST_PROBLEM', {
                                renderSubmit : true,
                                targets : [{ instance : record.get('INSTANCE_NAME'), host: record.get('HOST_NAME')}],
                                cancelHandler : function(form, action) {w.hide(); }

                            });
                            w.add(i);
                            w.show(Ext.getBody());
                        }
                    },{
                        text: _('Add host comment'),
                        iconCls: 'icinga-icon-comment',
                        handler: function() {
                            var w = new Ext.Window({ title: _('Add host comment'), width: 500, height: 300, renderTo: Ext.getBody(), layout:'fit' });
                            var b = new Icinga.Api.Command.FormBuilder();
                            var i = b.build('ADD_HOST_COMMENT', {
                                renderSubmit : true,
                                targets : [{ instance : record.get('INSTANCE_NAME'), host: record.get('HOST_NAME')}],
                                cancelHandler : function(form, action) {w.hide(); }

                            });
                            w.add(i);
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
            return (parseInt(record.get('HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED'), 10) > 0);
        },
        getTpl: function() {
            return "<div class='cancelable icinga-icon-info-problem-acknowledged' style='cursor:pointer;width:25px;height:16px;margin:auto' qtip='" +_('Host problem has been acknowledged')+ "'></div>";
        },
        applyClickHandler: function(el,record) {
            el.addClass('icinga-icon-cancel');
            el.addListener('click', function () {
                 Ext.Msg.show({
                    title:  _('Remove acknwoledgment'),
                    msg:_('Do you want to remove this acknowledgment?'),
                    buttons: Ext.Msg.YESNO,
                    icon: Ext.Msg.WARNING,
                    fn: function(btn) {
                        if(btn == "yes")
                            Icinga.Api.Command.Facade.sendCommand({
                                command : 'REMOVE_HOST_ACKNOWLEDGEMENT',
                                data : {host: record.get('HOST_NAME')},
                                targets : [{
                                    instance: record.get('INSTANCE_NAME'),
                                    host: record.get('HOST_NAME')
                                }]
                             });
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
            return (parseInt(record.get('HOST_SCHEDULED_DOWNTIME_DEPTH'), 10) > 0);
        },
        getTpl: function() {
            return "<div class='cancelable icinga-icon-info-downtime' style='cursor:pointer;width:25px;height:16px;margin:auto' qtip='" +_('Host is currently in a downtime')+ "'></div>";
        },
        applyClickHandler: function(el,record) {
            el.addClass('icinga-icon-cancel');
            el.addListener('click', function () {
                 Ext.Msg.show({
                    title:  _('Cancel downtime'),
                    msg: _('Do you want to cancel this downtime?'),
                    buttons: Ext.Msg.YESNO,
                    icon: Ext.Msg.WARNING,
                    fn: function(btn) {
                        if(btn == "yes")
                            Icinga.Api.Command.Facade.sendCommand({
                                command : 'DEL_DOWNTIME_BY_HOST_NAME',
                                data : {host: record.get('HOST_NAME')},
                                targets : [{
                                    instance: record.get('INSTANCE_NAME'),
                                    host: record.get('HOST_NAME')
                                }]
                             });
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
    ]

    Icinga.Cronks.Tackle.Renderer.StatusColumnRenderer = function (value, metaData, record, rowIndex, colIndex, store) {

        value = parseInt(value, 10);

        switch (value) {
        case 0:
            metaData.css = 'icinga-status-up';
            break;
        case 1:
            metaData.css = 'icinga-status-down';
            break;
        case 2:
            metaData.css = 'icinga-status-unreachable';
            break;
        case 99:
            metaData.css = 'icinga-status-pending';
            break;
        }

        var id = Ext.id();

        (function () {
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

        }).defer(100);
        return "<div id='" + id + "'></div>";
    };

})();