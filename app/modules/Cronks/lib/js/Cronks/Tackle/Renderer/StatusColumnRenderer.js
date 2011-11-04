/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Renderer');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Renderer.StatusColumnRenderer = function (value, metaData, record, rowIndex, colIndex, store) {

        value = parseInt(value, 10);
        var downTimeQTip = _('Host is currently in a downtime');
        var acknowledgedQTip = _('Host problem has been acknowledged');
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
        var inDowntime = (parseInt(record.get('HOST_SCHEDULED_DOWNTIME_DEPTH'), 10) > 0);
        var isAcknowledged = (parseInt(record.get('HOST_PROBLEM_HAS_BEEN_ACKNOWLEDGED'), 10) > 0);


        (function () {
            var tpl;
            if (inDowntime) {
                tpl = "<div class='cancelable icinga-icon-info-downtime' style='cursor:pointer;width:25px;height:16px;margin:auto' qtip='" + downTimeQTip + "'></div>";
            }
            if (isAcknowledged) {
                tpl = "<div class='cancelable icinga-icon-info-problem-acknowledged' style='cursor:pointer;width:25px;height:16px;margin:auto' qtip='" + acknowledgedQTip + "'></div>";
            }
            if (!inDowntime && !isAcknowledged) {
                return;
            }

            var cmp = new Ext.BoxComponent({
                layout: 'fit',
                html: tpl,
                renderTo: id,
                listeners: {
                    render: function (p) {

                        // Append the Panel to the click handler's argument list.
                        p.getEl().on('mouseenter', function (e) {
                            var el = p.getEl().first('.cancelable');
                            el.addClass('icinga-icon-cancel');
                            var title, msg;
                            if (inDowntime) {
                                title = _('Cancel downtime');
                                msg = _('Do you want to cancel this downtime?');
                            }
                            if (isAcknowledged) {
                                title = _('Remove acknwoledgment');
                                msg = _('Do you want to remove this acknowledgment?');
                            }
                            el.addListener('click', function () {
                                Ext.Msg.show({
                                    title: title,
                                    msg: msg,
                                    buttons: Ext.Msg.YESNO,
                                    icon: Ext.Msg.WARNING,
                                    fn: function (btn) {
                                        if (btn === "yes") {
                                            alert("Implement me!");
                                        }
                                    }
                                });
                            }, this);
                        }, this);
                        p.getEl().on('mouseleave', function () {
                            p.update(tpl);


                        }, this);
                    }
                }
            });

        }).defer(100);
        return "<div id='" + id + "'></div>";
    };

})();