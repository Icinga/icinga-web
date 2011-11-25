/*global Ext: false, Icinga: false, _: false, AppKit: false */
Ext.ns("AppKit.Admin.Components");

(function () {

    "use strict";

    AppKit.Admin.Components.CVGridPanel = Ext.extend(Ext.Panel, {
        title: _('Customvariable'),
        iconCls: 'icinga-icon-bricks',
        layout: 'fit',
        constructor: function (cfg) {
            Ext.apply(this, cfg);
            cfg.items = [{
                xtype: 'grid',
                multiSelect: true,
                store: cfg.store,
                viewConfig: {
                    forceFit: true
                },
                columns: [{
                    header: ' ',
                    width: 20,
                    dataIndex: 'target',
                    renderer: function (v) {
                        return '<div class="icon-16 icinga-icon-' + v + '"></div>';
                    }
                }, {
                    header: _('Variable'),
                    dataIndex: 'name'
                }, {
                    header: _('Value'),
                    dataIndex: 'value'
                }, {
                    header: _('Affects'),
                    dataIndex: 'target'
                }]
            }];
            Ext.Panel.prototype.constructor.call(this, cfg);
        },
        tbar: [{
            text: _('Add customvariable restriction'),
            iconCls: 'icinga-icon-add',
            menu: [{
                text: _('Host customvariable'),
                handler: function (c) {
                    var panel = c.ownerCt.ownerCt.ownerCt.ownerCt;
                    panel.showCVWindowForTarget(c, 'host');
                },
                iconCls: 'icinga-icon-host',
                scope: this
            }, {
                text: _('Service customvariable'),
                handler: function (c) {
                    var panel = c.ownerCt.ownerCt.ownerCt.ownerCt;
                    panel.showCVWindowForTarget(c, 'service');
                },
                iconCls: 'icinga-icon-service',
                scope: this
            }]

        }, {
            text: _('Remove selected'),
            iconCls: 'icinga-icon-cancel',
            handler: function (c) {
                var panel = c.ownerCt.ownerCt;
                var grid = panel.findByType('grid')[0];
                panel.store.remove(grid.getSelectionModel().getSelections());
            }
        }],

        selectValues: function (principals) {
            var Record = Ext.data.Record.create([{
                name: 'value'
            }, {
                name: 'name'
            }, {
                name: 'target'
            }]);
            this.store.removeAll();
            Ext.iterate(principals, function (p) {
                if (p.target.target_name === 'IcingaHostCustomVariablePair' || p.target.target_name === 'IcingaServiceCustomVariablePair') {
                    var entry = new Record({
                        target: p.target.target_name === 'IcingaHostCustomVariablePair' ? 'host' : 'service'
                    });
                    Ext.iterate(p.values, function (value) {
                        switch (value.tv_key) {
                        case 'cv_name':
                            entry.set('name', value.tv_val);
                            break;
                        case 'cv_value':
                            entry.set('value', value.tv_val);
                            break;
                        }
                    }, true);
                    this.store.add(entry);
                }
            }, this);
        },
        showCVWindowForTarget: function (c, target) {
            target = target || 'host';

            var valueField = new Icinga.Api.RESTFilterComboBox({
                targetField: target.toUpperCase() + '_CUSTOMVARIABLE_VALUE',
                target: 'host',
                name: 'value',
                fieldLabel: _(Ext.util.Format.capitalize(target) + ' customvariable value'),
                width: 300,
                disabled: true
            });

            var nameField = new Icinga.Api.RESTFilterComboBox({
                targetField: target.toUpperCase() + '_CUSTOMVARIABLE_NAME',
                target: target,
                width: 300,
                name: 'name',
                fieldLabel: Ext.util.Format.capitalize(target) + _(' customvariable'),
                allowBlank: false,
                listeners: {
                    select: function (v, record) {
                        var value = record.get(v.displayField);
                        valueField.filter(v.displayField, value, true);
                        valueField.setDisabled(false);
                        valueField.reset();
                        valueField.getStore().removeAll();
                    }
                }
            });

            new Ext.Window({
                title: _('Add ' + target + ' customvariable'),
                width: 500,
                height: 180,
                layout: 'fit',
                modal: true,
                items: [{
                    xtype: 'form',
                    padding: 5,
                    border: false,
                    items: [
                    nameField, valueField]
                }],
                buttons: [{
                    text: _('Add customvariable'),
                    iconCls: 'icinga-icon-add',
                    handler: function (b) {
                        var Record = Ext.data.Record.create([{
                            name: 'id'
                        }, {
                            name: 'name'
                        }, {
                            name: 'value'
                        }, {
                            name: 'target'
                        }]);
                        var form = b.ownerCt.ownerCt.findByType('form')[0].getForm();
                        if (form.isValid()) {
                            this.store.add(
                            new Record(
                            Ext.apply(form.getValues(), {
                                target: target
                            })));
                            b.ownerCt.ownerCt.close();
                        }

                    },
                    scope: this
                }, {
                    text: _('Cancel'),
                    iconCls: 'icinga-icon-cancel',
                    handler: function (c) {
                        c.ownerCt.ownerCt.close();
                    }
                }]
            }).show(document.body);
        }
    });

})();