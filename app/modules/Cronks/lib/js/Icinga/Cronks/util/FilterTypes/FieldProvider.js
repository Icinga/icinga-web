(function() {
    "use strict";

    /**
     * Return a api combo box for IcingaWeb Api data
     * @param {Object} meta
     * @return {Cronk.grid.filter.ApiComboBox}
     */
    Ext.ns("Icinga.Cronks.util.FilterTypes.FieldProvider").getApiCombo = function (meta,defaultv) {
        AppKit.log(meta);
        return new Cronk.grid.filter.ApiComboBox({
            typeAhead: false,
            triggerAction: 'all',
            forceSelection: false,
            'name': meta.name + '-field',
            'id': meta.name + '-field',
            hiddenName: 'value',
            value: defaultv || '',
            hiddenValue: defaultv || '',
            fieldLabel: meta.boxLabel || _('Filter value')
        }, meta);
    };


    Ext.ns("Icinga.Cronks.util.FilterTypes.FieldProvider").TEXT_FIELDS = new Ext.data.ArrayStore({
        idIndex: 0,
        fields: ['id','value'],
        data: [
            [60, _('contain')],
            [50, _('is')]
        ]
    });

    Ext.ns("Icinga.Cronks.util.FilterTypes.FieldProvider").NUMBER_FIELDS = new Ext.data.ArrayStore({
        idIndex: 0,
        fields: ['id','value'],
        data: [
            [50, _('=')],
            [70, _('>')],
            [71, _('<')]
        ]
    });

    Ext.ns("Icinga.Cronks.util.FilterTypes.FieldProvider").getLocalComboConfig = function(store,name,defaultv) {
        return {
            xtype: 'combo',
            name: name,
            store: store,
            valueField: 'value',
            displayField: 'value',
            mode: 'local',
            typeAhead: true,
            value: defaultv || store.getAt(0).get('value'),
            hiddenValue: defaultv || store.getAt(0).get('value'),
            triggerAction: 'all',
            forceSelection: true
        }
    }
})();