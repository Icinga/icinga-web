/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Information');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Information.PluginOutput = Ext.extend(Ext.Panel, {
        title: _("Plugin output"),
        tpl: new Ext.XTemplate('<tpl for=".">', '<div style="margin: 5px;">', '{object_output}', '</div>', '</tpl>')
    });

})();