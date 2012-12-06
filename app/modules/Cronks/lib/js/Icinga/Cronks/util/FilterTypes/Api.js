(function() {
"use strict";


Ext.ns('Icinga.Cronks.util.FilterTypes').Api = function(filterCfg,defaults) {
    var provider = Icinga.Cronks.util.FilterTypes.FieldProvider;
    return {
        xtype:'form',
        height:200,
        padding:5,
        width:300,
        items: [{
            border: false,
            html: '<div style="font-size:12px;">'+_("Add a new filter here. You can change the filter afterwards or move it in the filter tree")+
            '</div>'
        },{
            xtype: 'spacer',
            height: 25
        },{
            xtype: 'hidden',
            name: 'field',
            value: filterCfg.name
        },{
            xtype: 'hidden',
            name: 'label',
            value: filterCfg.label
        },{
            fieldLabel: _('Filter target'),
            xtype: 'textfield',
            emptyText: filterCfg.label,
            disabled: true,
            allowEmpty:false,
            anchor: '90%'
        },
            provider.getLocalComboConfig(provider.TEXT_FIELDS,"operator",defaults.operator),
            provider.getApiCombo(filterCfg,defaults.value)
        ]
    };
};

    Ext.ns('Icinga.Cronks.util.FilterTypes').Customvar = function(filterCfg,defaults) {
        var provider = Icinga.Cronks.util.FilterTypes.FieldProvider;
        return {
            xtype:'form',
            height:200,
            padding:5,
            width:300,
            items: [{
                border: false,
                html: '<div style="font-size:12px;">'+_("Add a new "+filterCfg.label+" filter here. You can change the filter afterwards or move it in the filter tree")+
                    '</div>'
            },{
                xtype: 'spacer',
                height: 25
            },{
                xtype: 'hidden',
                name: 'field',
                value: "Customvariable"

            },provider.getApiCombo({
                boxLabel: 'Customvariable',
                api_keyfield: "CUSTOMVARIABLE_NAME",
                api_target: "customvariable",
                api_valuefield: "CUSTOMVARIABLE_NAME",
                name: 'CUSTOMVARIABLE_NAME'
            }),
                provider.getLocalComboConfig(provider.TEXT_FIELDS,"operator",defaults.operator),
                provider.getApiCombo({
                    boxLabel: 'Customvariable',
                    hidden: true,
                    api_keyfield: "CUSTOMVARIABLE_NAME",
                    api_target: "customvariable",
                    api_valuefield: "CUSTOMVARIABLE_NAME",
                    name: 'CUSTOMVARIABLE_NAME'
                }),
            ]
        }
    }

})();