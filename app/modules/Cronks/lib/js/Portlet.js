/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */

/*
 * Resizer implementation based on user_xm
 * http://www.sencha.com/forum/showthread.php?18593-Portlet-vertical-resizing/page2
 */

Ext.ux.Portlet = Ext.extend(Ext.Panel, {
    anchor: '100%',
    frame: true,
    collapsible: true,
    draggable: true,
    cls: 'x-portlet',
    //resizer properties
    heightIncrement:16,
    pinned:false,
    duration: .6,
    easing: 'backIn',
    transparent:false,

    onRender : function(ct, position) {
        Ext.ux.Portlet.superclass.onRender.call(this,ct,position);

        //2008.1.11 xm
        var createProxyProtoType=Ext.Element.prototype.createProxy;
        Ext.Element.prototype.createProxy=function(config){
	        return Ext.DomHelper.append(this.dom, config, true);
	    };

        this.resizer = new Ext.Resizable(this.el, {
            animate: true,
            duration: this.duration,
            easing: this.easing,
            handles: 's',
            transparent:this.transparent,
            heightIncrement:this.heightIncrement,
            minHeight: this.minHeight || 100,
            pinned: this.pinned
        });
        this.resizer.on('resize', this.onResizer, this);

        Ext.Element.prototype.createProxy=createProxyProtoType;
        //2008.1.11 xm
    },

    onResizer : function(oResizable, iWidth, iHeight, e) {
        this.setHeight(iHeight);
    },

    onCollapse : function(doAnim, animArg) {
        this.el.setHeight('');
        Ext.ux.Portlet.superclass.onCollapse.call(this, doAnim, animArg);
    }
});

Ext.reg('portlet', Ext.ux.Portlet);