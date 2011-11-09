/*global Ext: false, Icinga: false, _: false */
Ext.ns('Icinga.Cronks.Tackle.Information');

(function () {
    "use strict";

    Icinga.Cronks.Tackle.Information.Perfdata = Ext.extend(Ext.Panel, {
        title: _("Perfdata"),
        tpl: new Ext.XTemplate('<tpl for=".">', '<div style="margin: 5px;">', '{object_perfdata}', '</div>', '</tpl>')
    });

})();