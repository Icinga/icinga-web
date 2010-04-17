Ext.form.Action.JSONSubmit = function(form, options){
    Ext.form.Action.JSONSubmit.superclass.constructor.call(this, form, options);
};

Ext.extend(Ext.form.Action.JSONSubmit, Ext.form.Action.Submit, {
    type : 'submit',

    run : function(){
        var o = this.options;
        var method = this.getMethod();
        var isPost = method == 'POST';
        
        var json_ns = o.json_namespace || 'data';
        
        if (isPost) {
        	this.options.params[json_ns] = Ext.util.JSON.encode(this.form.getValues(false)); 
        }
        
        if(o.clientValidation === false || this.form.isValid()){
            Ext.Ajax.request(Ext.apply(this.createCallback(), {
                //form:this.form.el.dom,
                url:this.getUrl(!isPost),
                method: method,
                params:isPost ? this.getParams() : null,
                isUpload: this.form.fileUpload
            }));
        }else if (o.clientValidation !== false){  
            this.failureType = Ext.form.Action.CLIENT_INVALID;
            this.form.afterAction(this, false);
        }
    }
});