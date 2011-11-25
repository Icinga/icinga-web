/*global Ext: false, Icinga: false, _: false, AppKit: false */
Ext.ns("AppKit.Admin.Components");
(function () {
    "use strict";
    AppKit.Admin.Components.GroupRestrictionView = Ext.extend(Ext.Panel, {
        layout: 'fit',
        autoScroll: true,
        constructor: function (cfg) {
            cfg.iconCls = 'icinga-icon-' + cfg.target;
            this.store = cfg.store;
            this.fieldTarget = cfg.target;
            cfg.title = Ext.util.Format.capitalize(cfg.target);
            cfg.items = [{
                xtype: 'editorgrid',
                autoScroll: true,
                store: cfg.store,
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: false
                }),
                emptyText: _('No ') + _(cfg.target) + (' restrictions set for this ') + _(cfg.type),
                columns: [{
                    header: _('Only show members of the following ') + _(cfg.target + 's') + ':',
                    dataIndex: cfg.target,
                    editor: (function () {
                        switch (cfg.target) {
                        case 'servicegroup':
                            return Icinga.Api.ServicegroupsComboBox;
                        case 'hostgroup':
                            return Icinga.Api.HostgroupsComboBox;
                        }
                    })()
                }],
                viewConfig: {
                    forceFit: true
                }
            }];
            Ext.Panel.prototype.constructor.call(this, cfg);
        },

        tbar: [{
            text: _('Add restriction'),
            iconCls: 'icinga-icon-add',
            handler: function (c) {
                var panel = c.ownerCt.ownerCt;

                var EmptyRecord = Ext.data.Record.create([{
                    'name': panel.fieldTarget
                }]);
                var rObj = {};

                rObj[panel.fieldTarget] = 'new restriction';
                panel.store.add(new EmptyRecord(rObj), true);
            }

        }, {
            text: _('Remove selected'),
            iconCls: 'icinga-icon-cancel',
            handler: function (c) {
                var panel = c.ownerCt.ownerCt;
                var list = panel.findByType('editorgrid')[0];
                panel.store.remove(list.getSelectionModel().getSelections());
            },
            scope: this
        }],

        selectValues: function (principals) {
            var Record = Ext.data.Record.create([{
                name: this.fieldTarget
            }]);
            this.store.removeAll();
            Ext.iterate(principals, function (p) {
                if (p.target.target_name === 'Icinga' + Ext.util.Format.capitalize(this.fieldTarget)) {
                    Ext.iterate(p.values, function (v) {
                        var rObj = {};
                        rObj[this.fieldTarget] = v.tv_val;
                        this.store.add(new Record(rObj));
                    }, this);
                }
            }, this);
        }
    });
})();