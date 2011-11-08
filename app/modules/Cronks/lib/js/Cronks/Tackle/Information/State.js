/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Information');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Information.State = Ext.extend(Ext.grid.PropertyGrid, {
        title: _('State information'),

        constructor: function (config) {
            Icinga.Cronks.Tackle.Information.State.superclass.constructor.call(this, config);
        },

        initComponent: function () {
            Icinga.Cronks.Tackle.Information.State.superclass.initComponent.call(this);
        },

        setSource: function (source) {
            source = this.translateNames(source);
            Icinga.Cronks.Tackle.Information.State.superclass.setSource.call(this, source);
        },

        translateNames: function (source) {
            var newSource = {};
            Ext.iterate(source, function (key, val) {
                newSource[Icinga.Cronks.Tackle.Translation.get(key)] = val;
            }, this);
            return newSource;
        }
    });

})();