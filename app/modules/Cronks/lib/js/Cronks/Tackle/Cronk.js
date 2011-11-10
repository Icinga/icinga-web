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
            
            var type = 'host';
            
            this.objectGrid = new Icinga.Cronks.Tackle.ObjectGrid({
                region: 'center'
            });


            this.tabHeadHostInfo = new Icinga.Cronks.Tackle.Information.Head({
                type: 'host'
            });
            this.tabHeadServiceInfo = new Icinga.Cronks.Tackle.Information.Head({
                type: 'service'
            });

            this.tabCommands = new Icinga.Cronks.Tackle.Information.Commands();
            
            this.tabComments = new Icinga.Cronks.Tackle.Comment.Panel({
                type: type
            });
            
            this.tabRelations = new Icinga.Cronks.Tackle.Information.Relations();
            
            this.tabServices = new Icinga.Cronks.Tackle.Information.Services();

            this.infoTabs = new Icinga.Cronks.Tackle.InfoTabPanel();

            this.infoTabs.add([
                this.tabHeadHostInfo,
                this.tabHeadServiceInfo,
                this.tabCommands, 
                this.tabComments, 
                this.tabRelations
            ]);

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
            this.initEvents();
        },

        initEvents: function () {
            this.objectGrid.on("hostSelected", function(record) {
                this.tabComments.grid.setObjectId(record.data.HOST_OBJECT_ID);

                this.infoTabs.unhideTabStripItem(this.tabHeadHostInfo);
                this.tabHeadHostInfo.loadDataForObjectId(record.data.HOST_OBJECT_ID);
                this.infoTabs.hideTabStripItem(this.tabHeadServiceInfo);

                this.tabComments.form.setObjectData({
                    objectName : record.data.HOST_NAME,
                    objectId : record.data.HOST_ID,
                    objectInstance : record.data.INSTANCE_NAME
                });
                if (this.collapsibleFrame.collapsed === true) {
                    this.collapsibleFrame.expand(true);
                }
            },this);
            this.objectGrid.on("serviceSelected", function(record) {
                if(!record.data)
                    return;
                this.infoTabs.unhideTabStripItem(this.tabHeadServiceInfo);
                this.infoTabs.setActiveTab(this.tabHeadServiceInfo);
                this.infoTabs.hideTabStripItem(this.tabHeadHostInfo);
                //this.tabComments.grid.setObjectId(record.data.SERVICE_OBJECT_ID);
                this.tabHeadServiceInfo.loadDataForObjectId(record.data.SERVICE_OBJECT_ID);
                /*this.tabComments.form.setObjectData({
                    objectName : record.data.SERVICE_NAME,
                    objectId : record.data.SERVICE_ID,
                    objectInstance : record.data.INSTANCE_NAME
                });*/
                if (this.collapsibleFrame.collapsed === true) {
                    this.collapsibleFrame.expand(true);
                }
            },this);

        }
    });

    Ext.reg('cronks-tackle-cronk', Icinga.Cronks.Tackle.Cronk);

})();
