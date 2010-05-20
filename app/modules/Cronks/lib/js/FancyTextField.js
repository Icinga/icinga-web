Ext.ns('Ext.ux.form');


/**
 * fancy-text field for extjs
 * Date: 2009-09-09
 * Author: Christian Doebler <christian.doebler@netways.de>
 */
Ext.ux.form.FancyTextField = Ext.extend(Ext.form.TextField, {

	frame : true,
	icon : true,
	resetButton : true,

    // private
    onRender : function(ct, position){

        Ext.ux.form.FancyTextField.superclass.onRender.call(this, ct, position);

        if(!this.el){
            this.defaultAutoCreate = {
                tag: "input",
                autocomplete: "off"
            };
        }

		this.addClass("fancytextfield-input");

		var wrap = this.el.up("div");

		if (this.icon) {
			this.iconEl = Ext.DomHelper.insertBefore(wrap, {
				tag: "div",
				cls: "fancytextfield-icon"
			});			
		}

		if (this.resetButton) {
			this.buttonEl = new Ext.Button ({
				cls: "fancytextfield-reset-button"
			});
			this.buttonEl.on(
				"click",
				function () {
					this.reset();
				},
				this
			);
			this.buttonEl.render(wrap);
		}

		wrap = wrap.up("div");
		wrap.addClass("fancytextfield-container");

		if (this.frame) {
			var wrapBox = wrap.boxWrap();
			wrapBox.addClass("fancytextfield-box");
		}

    }

});

Ext.reg("fancytextfield", Ext.ux.form.FancyTextField);
