/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Ext.ns("Icinga.Cronks.Tackle.Renderer").CheckOutputRenderer = Ext.extend(Ext.Container, {
    constructor: function(cfg) {
        cfg = cfg || {};
        Ext.apply(this,cfg);
        Ext.Container.prototype.constructor.apply(this,arguments);
    },
    border: false,
    record: '%RECORD%'

});