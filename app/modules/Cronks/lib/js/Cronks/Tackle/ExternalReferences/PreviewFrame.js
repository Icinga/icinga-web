/*global Ext: false, Icinga: false, _: false */
(function() {
    "use strict";

    Ext.ns("Icinga.Cronks.Tackle.ExternalReferences").PreviewFrame = Ext.extend(Ext.Panel,{
        flex: 2,
        layout: 'fit',
        constructor: function(cfg) {
            cfg = cfg || {};
            this.anchorBtn = new Ext.Button({
                iconCls: 'icinga-icon-anchor',
                text: _('View in new page'),
                handler: this.openURL,
                scope:this,
                disabled: true
            })
            cfg.tbar = new Ext.Toolbar({
                items: [this.anchorBtn]
            });

            
            Ext.Panel.prototype.constructor.call(this,cfg);
        },
        initComponent: function() {
            Ext.Panel.prototype.initComponent.call(this);
         
            this.iFrameEl = Ext.DomHelper.createDom({
                tag: 'iframe',
                style: {
                    width: '100%',
                    height: '100%'
                }
            });
            var iFrame = new Ext.BoxComponent({contentEl: this.iFrameEl});
            this.add(iFrame);
        },
        reset: function() {
            this.setContentURL(null);
        },
        openURL: function() {
            window.open(this.iFrameEl.src);
        },
        setContentURL : function(url) {
            if(url != null) {
                this.anchorBtn.setDisabled(false);
            } else {
                this.anchorBtn.setDisabled(true);
            }
            this.iFrameEl.src = url;
        
        }

    });
})();