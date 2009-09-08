Ext.form.FancyTextField = Ext.extend(Ext.form.TextField, {

	frame : true,
	icon : true,
	resetButton : true,

    // private
    onRender : function(ct, position){

        Ext.form.FancyTextField.superclass.onRender.call(this, ct, position);

        if(!this.el){
            this.defaultAutoCreate = {
                tag: "input",
                autocomplete: "off"
            };
        }

		this.addClass("fancytextfield-input");

		if (this.frame) {
			this.textSizeEl = Ext.DomHelper.append(document.body, {
				tag: "pre",
				cls: "x-form-grow-sizer"
			});
		}

		var wrap = this.el.up("div");

		if (this.icon) {
			this.iconEl = Ext.DomHelper.insertBefore(wrap, {
				tag: "div",
				cls: "fancytextfield-icon"
			});			
		}

		if (this.resetButton) {			
			this.resetButtonEl = Ext.DomHelper.append(wrap, {
				tag: "div",
				cls: "fancytextfield-reset-button",
				listeners: {
					click: function (button, event) {
						this.reset();
					}
				}
			});
		}

		wrap = wrap.up("div");
		wrap.addClass("fancytextfield-container");

		var wrapBox = wrap.boxWrap();
		wrapBox.addClass("fancytextfield-box");

    }

});

Ext.reg("fancytextfield", Ext.form.FancyTextField);
