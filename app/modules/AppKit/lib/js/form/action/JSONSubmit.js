// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}

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