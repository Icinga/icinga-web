
(function() {
"use strict";

var ns = Ext.ns('Icinga.Cronks.util.FilterTypes');


var baseDescription = {
    border: false,
    html: '<div style="font-size:12px;">'+_("Add a new filter here. You can change the filter afterwards or move it in the filter tree")+
        '</div>'
};

ns.Number = function(filterCfg,defaults) {
    defaults = defaults || {};
    var provider = ns.FieldProvider;
    return {
        xtype: 'form',
        height: 170,
        width: 200,
        padding: 5,
        items: [
            baseDescription,
        {
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
            anchor: '90%'
        },  
            provider.getLocalComboConfig(provider.NUMBER_FIELDS,"operator",defaults.operator),
        {
            fieldLabel: _('Filter value'),
            xtype: 'numberfield',
            name: "value",
            width: 100,
            value: defaults.value || {},
            allowEmpty: false
        }]
        
    };
}

ns.Text = function(filterCfg,defaults) {
    var provider = ns.FieldProvider;

    defaults = defaults || {};
    return {
        xtype: 'form',
        height: 170,
        width: 200,
        padding: 5,
        items: [
            baseDescription,
        {
            xtype: 'spacer',
            height :25
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
            allowEmpty: false,
            disabled: true,
            anchor: '90%'
        },  
            provider.getLocalComboConfig(provider.TEXT_FIELDS,"operator",defaults.operator),
        {
            fieldLabel: _('Filter value'),
            xtype: 'textfield',
            name: "value",
            allowEmpty:false,
            width: 100,
            value: defaults.value || {}
        }]
    };
}

ns.Bool = function(filterCfg,defaults) {
    return {
        xtype: 'form',
        height: 120,
        width: 200,
        padding: 5,
        items: [
            baseDescription,
        {
            xtype: 'spacer',
            height :25
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
            value: 'is'
        },{
            fieldLabel: filterCfg.label,
            name: 'value',
            checked: defaults.value,
            xtype: 'checkbox'
        }]
    };
}

ns.DowntimeType = function(filterCfg) {
    return {
        xtype: 'form',
        height: 120,
        width: 200,
        padding: 5,
        items: [
            { html: 'Downtime type is '},
            {
                xtype: 'spacer',
                height :25
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
                value: 'is'
            },{
                fieldLabel: _("fixed"),
                name: 'value',
                checked: defaults.value,
                xtype: 'radio'
            },{
                fieldLabel: _("flexible"),
                name: 'value',
                checked: defaults.value,
                xtype: 'radio'
            }]
    };
}

ns.Downtime_type_fixed = ns.DowntimeType;

})();