

Ext.ns('Icinga.Cronks.Tackle.Renderer');
Icinga.Cronks.Tackle.Renderer.FlagIconColumnRenderer = function(type) {

    return function (value, metaData, record, rowIndex, colIndex, store) {

        var allowPassive = parseInt(record.get(type.toUpperCase()+'_PASSIVE_CHECKS_ENABLED'),10);
        var isFlapping = parseInt(record.get(type.toUpperCase()+'_IS_FLAPPING'),10);
        var isNotifying = parseInt(record.get(type.toUpperCase()+'_NOTIFICATIONS_ENABLED'),10);
        var isActive = parseInt(record.get(type.toUpperCase()+'_ACTIVE_CHECKS_ENABLED'),10);
        var isAck = parseInt(record.get(type.toUpperCase()+'_PROBLEM_HAS_BEEN_ACKNOWLEDGED'),10);
        var inDowntime = parseInt(record.get(type.toUpperCase()+'_SCHEDULED_DOWNTIME_DEPTH'),10);
        value = "";
        var tpl = new Ext.XTemplate("<div class='{icon}' qtip='{tip}' style='width:20px;height:18px;float:left' id='{id}'></div>");
        var idBase = Ext.id();
        if(!isActive) {
            if(allowPassive) {
                value += tpl.apply({
                    icon: 'icinga-icon-info-passive',
                    tip: _('Accepting passive checks only'),
                    id: 'passive_'+idBase
                });
            } else {
                value += tpl.apply({
                    icon: 'icinga-icon-info-disabled',
                    tip: _('Object is disabled'),
                    id: 'disabled_'+idBase
                });
            }
        }
        if(!isNotifying) {
            value += tpl.apply({
                icon:'icinga-icon-info-notifications-disabled',
                tip: _('Notifications disabled'),
                id: 'notify_'+idBase
            });
        }
        if(isFlapping) {
            value += tpl.apply({
                icon:'icinga-icon-info-flapping',
                tip: _('Object is flapping'),
                id: 'flapping_'+idBase
            });
        }
        if(isAck) {
            value += tpl.apply({
                icon:'icinga-icon-info-problem-acknowledged',
                tip: _('Acknowledged'),
                id: 'ack'+idBase
            });
        }
        if(inDowntime) {
            value += tpl.apply({
                icon:'icinga-icon-info-downtime',
                tip: _('In downtime'),
                id: 'dtime_'+idBase
            });
        }
        return value;
    }
};
Icinga.Cronks.Tackle.Renderer.FlagIconColumnClickHandler =  function(col,grid,rowIdx,e) {
    var row = this.getView().getRow(rowIdx);
    var record = this.getStore().getAt(rowIdx);
    var type = record.get('SERVICE_ID') ? 'service' : 'host'

    var allowPassive = parseInt(record.get(type.toUpperCase()+'_PASSIVE_CHECKS_ENABLED'),10);
    var isFlapping = parseInt(record.get(type.toUpperCase()+'_IS_FLAPPING'),10);
    var isNotifying = parseInt(record.get(type.toUpperCase()+'_NOTIFICATIONS_ENABLED'),10);
    var isActive = parseInt(record.get(type.toUpperCase()+'_ACTIVE_CHECKS_ENABLED'),10);

    var cmdType = type == 'service' ? 'SVC' : 'HOST';
    var flagSetter = function(title,msg,cmdDef,data,target) {
        Ext.Msg.show({
            title: title,
            msg:msg,
            buttons: Ext.Msg.YESNO,
            icon: Ext.Msg.WARNING,
            fn: function(btn) {
                if(btn == "yes") {
                    var cmd =
                    {
                        command : cmdDef,
                        data : data,
                        targets : [target]
                    }
                    Icinga.Api.Command.Facade.sendCommand(cmd);
                }
            },
            scope:this
        });
    }
    var data = {
        host: record.get('HOST_NAME'),
        instance: record.get('INSTANCE_NAME')
    };
    var target = {
        host: record.get('HOST_NAME'),
        instance: record.get('INSTANCE_NAME')
    }
    if(type == 'service') {
        data.service = record.get('SERVICE_NAME');
        target.service = record.get('SERVICE_NAME');
    };

    var mItems = [];
    if(allowPassive) {
        mItems.push({
            text: _('Disable passive checks for this object'),
            iconCls: 'icinga-icon-cancel',
            handler: flagSetter.createDelegate(this,[
                _('Disable passive checks'),
                _('Disable passive checks for this object?'),
                'DISABLE_PASSIVE_'+cmdType+'_CHECKS',
                data,
                target
            ])
        });
    } else {
        mItems.push({
            text: _('Enable passive checks for this object'),
            iconCls: 'icinga-icon-info-passive',
            handler: flagSetter.createDelegate(this,[
                _('Enable passive checks'),
                _('Enable passive checks for this object?'),
                'ENABLE_PASSIVE_'+cmdType+'_CHECKS',
                data,
                target
            ])
        });
    };
    if(isActive) {
        mItems.push({
            text: _('Disable active checks for this object'),
            iconCls: 'icinga-icon-cancel',
             handler: flagSetter.createDelegate(this,[
                _('Disable active checks'),
                _('Disable active checks for this object?'),
                'DISABLE_'+cmdType+'_CHECK',
                data,
                target
            ])
        });
    } else {
        mItems.push({
            text: _('Reenable active checks for this object'),
            iconCls: 'icinga-icon-accept',
             handler: flagSetter.createDelegate(this,[
                _('Enable active checks'),
                _('Enable active checks for this object?'),
                'ENABLE_'+cmdType+'_CHECK',
                data,
                target
            ])
        });
    };

    if(!isNotifying) {
        mItems.push({
            text: _('Reenable notifications for this object'),
            iconCls: 'icinga-icon-accept',
             handler: flagSetter.createDelegate(this,[
                _('Enable notfiactions'),
                _('Enable notifications for this object?'),
                'ENABLE_'+cmdType+'_NOTIFICATIONS',
                data,
                target
            ])
        });
    }

    var menu = new Ext.menu.Menu({
        items: mItems
    }).showAt(e.getXY())

};