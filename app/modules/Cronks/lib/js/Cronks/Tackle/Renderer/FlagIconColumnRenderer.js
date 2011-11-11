

Ext.ns('Icinga.Cronks.Tackle.Renderer');

Icinga.Cronks.Tackle.Renderer.FlagIconColumnRenderer =  function (value, metaData, record, rowIndex, colIndex, store) {
    var type = record.get('SERVICE_ID') ? 'service' : 'host'
    var isPassive = record.get(type.toUpperCase()+'_PASSIVE_CHECKS_ENABLED');
    var isFlapping = record.get(type.toUpperCase()+'_IS_FLAPPING');
    var isActive = record.get(type.toUpperCase()+'_ACTIVE_CHECKS_ENABLED');
    var value = "";
    var tpl = new Ext.XTemplate("<div class='{icon}' qtip='{tip}' style='width:20px;height:20px' id='{id}'></div>");
    var idBase = Ext.id();
    if(isPassive && !isActive) {
        value += tpl.apply({
            icon:'icinga-icon-info-passive',
            tip: _('Accepting passive checks only'),
            id: 'passive_'+idBase
        });
    }
    if(!isPassive && !isActive) {
        value += tpl.apply({
            icon:'icinga-icon-info-disabled',
            tip: _('Object is disabled'),
            id: 'disabled_'+idBase
        });
    }
    if(isFlapping) {
        value += tpl.apply({
            icon:'icinga-icon-info-flapping',
            tip: _('Object is flapping'),
            id: 'flapping_'+idBase
        });
    }



}