/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Cronk = Ext.extend(Ext.Panel, {

        constructor: function (config) {

            config = Ext.apply(config || {}, {
                layout: 'border',
                border: false,
                defaults: {
                    border: false
                }
            });

            Icinga.Cronks.Tackle.Cronk.superclass.constructor.call(this, config);
        },

        initComponent: function () {
            Icinga.Cronks.Tackle.Cronk.superclass.initComponent.call(this);

            this.objectGrid = new Icinga.Cronks.Tackle.ObjectGrid({
                region: 'center'
            });

            this.objectGrid.on('rowclick', this.rowSingleClickHandler, this);

            this.tabDefaults = new Icinga.Cronks.Tackle.Information.Default();
            this.tabCommands = new Icinga.Cronks.Tackle.Information.Commands();
            this.tabComments = new Icinga.Cronks.Tackle.Information.Comments({
                type: 'host'
            });
            this.tabRelations = new Icinga.Cronks.Tackle.Information.Relations();
            this.tabServices = new Icinga.Cronks.Tackle.Information.Services();

            this.infoTabs = new Icinga.Cronks.Tackle.InfoTabPanel();

            this.infoTabs.add([
                this.tabDefaults, 
                this.tabServices, 
                this.tabCommands, 
                this.tabComments, 
                this.tabRelations]
            );

            this.collapsibleFrame = new Ext.Panel({
                layout: 'fit',
                iconCls: 'icinga-icon-universal',
                region: 'south',
                title: _('Object'),
                height: 300,
                minSize: 300,
                maxSize: 600,
                collapsible: true,
                split: true,
                border: false,
                items: this.infoTabs
            });

            this.add([this.collapsibleFrame, this.objectGrid]);
        },

        rowSingleClickHandler: function (grid, index, e) {
            var store = grid.getStore();
            var record = store.getAt(index);
            var object_id = record.data.HOST_OBJECT_ID;

            // Notify all other tabs
            this.tabComments.setObjectId(object_id);

            if (this.collapsibleFrame.collapsed === true) {
                this.collapsibleFrame.expand(true);
            }
        }
    });

    Ext.reg('cronks-tackle-cronk', Icinga.Cronks.Tackle.Cronk);

})();