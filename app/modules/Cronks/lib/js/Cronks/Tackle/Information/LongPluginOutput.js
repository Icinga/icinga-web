/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Information');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Information.LongPluginOutput = Ext.extend(Ext.Panel, {
        title: _("Long plugin output"),
        tpl: new Ext.XTemplate('<tpl for=".">', '<div style="margin: 5px;">', '{object_long_output}', '</div>', '</tpl>')
    });

})();