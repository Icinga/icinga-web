/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Comment');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Comment.Panel = Ext.extend(Ext.Panel, {
        title: _('Comments'),
        iconCls: 'icinga-icon-comment',
        type: null,

        constructor: function (config) {

            if (Ext.isEmpty(config.type)) {
                throw ("config.type is needed: host or service!");
            }

            config.layout = 'border';

            Icinga.Cronks.Tackle.Comment.Panel.superclass.constructor.call(this, config);
        },

        initComponent: function () {
            Icinga.Cronks.Tackle.Comment.Panel.superclass.initComponent.call(this);

            this.grid = new Icinga.Cronks.Tackle.Comment.Grid({
                type: this.type,
                parentCmp: this,
                region: 'center'
            });

            this.form = new Icinga.Cronks.Tackle.Comment.CreateForm({
                type: this.type,
                parentCmp: this,
                region: 'east',
                width: 400,
                collapsed: true,
                collapsible: true
            });

            this.add(this.grid, this.form);

            this.doLayout(false, true);
        }
    });

})();