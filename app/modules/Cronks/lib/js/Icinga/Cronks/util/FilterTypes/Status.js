(function() {
    
var getStatusForm = function(filterCfg, radiobtns) {

    return {
        xtype: 'form',
        width: 160,
        height: 150,
        padding:5,
        items: [{
            border: false,
            html: '<div style="font-size:12px;font-weight:bold">'+_("Filter by host status:")+'</div><br/>'
        },{
            xtype: 'hidden',
            name: 'field',
            value: filterCfg.name
        },{    
            xtype: 'hidden',
            name: 'label',
            value: filterCfg.label
        },{
            xtype: 'hidden',
            name: 'operator',
            value: '='
        },{
            name: 'value',
            xtype: 'radiogroup',
            getValue: function() {
                for(var i=0;i<this.items.items.length;i++) {
                    var field = this.items.items[i];
                    if(field.getValue()) {
                        return i;
                    }
                };
                return 0
                

            },
            columns: 1,
            items: radiobtns
        }]
    }

}

Ext.ns('Icinga.Cronks.util.FilterTypes').Hoststatus = function(filterCfg,defaults) {
    return getStatusForm(filterCfg,[{
        checked: defaults['value'] == 0 || defaults == {},
        inputValue: 0,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Up')
    },{
        checked: defaults['value'] == 1,
        inputValue: 1,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Down')
    },{
        checked: defaults['value'] == 2,
        inputValue: 2,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Unreachable')
    }]);
};

Ext.ns('Icinga.Cronks.util.FilterTypes').Servicestatus = function(filterCfg,defaults) {
   return getStatusForm(filterCfg,[{
        checked: defaults['value'] == 0 || defaults == {},
        inputValue: 0,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('OK')
    },{
        checked: defaults['value'] == 1,
        inputValue: 1,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Warning')
    },{
        checked: defaults['value'] == 2,
        inputValue: 2,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Critical')
    },{
        checked: defaults['value'] == 3,
        inputValue: 2,
        name: 'state_radio',
        xtype: 'radio',
        boxLabel: _('Unknown')
    }]);
       
};

})();