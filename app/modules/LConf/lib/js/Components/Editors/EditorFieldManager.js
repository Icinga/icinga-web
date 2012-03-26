Ext.ns("LConf.Editors").EditorFieldManager = new function() {
    this.urls = {};

    /**
     * Sets the base routes for all providers that display ldap information
     * via AutoComplete,lists, etc.
     **/
    this.setupURLs = function(urls) {
        this.urls = urls;
        
        for(var i in registeredEditorFields) {
            var editorField = registeredEditorFields[i];
            
            if(typeof editorField.setBaseRoute === "function") {
                editorField.setBaseRoute(urls.ldapmetaprovider);
            }
        }
    }
     /**
     * Private static map of property=>editorfield relations
     */
    var registeredEditorFields = {}

    this.registerEditorField = function(property,editor) {
        registeredEditorFields[property.toLowerCase()] = editor;
    }

    this.unregisterEditorField = function(property) {
        delete(registeredEditorFields[property.toLowerCase()]);
    }

    this.getEditorFieldForProperty = function(property,cfg,objectclass) {
        var field;
        objectclass = objectclass || [];
        if(!Ext.isArray(objectclass))
            objectclass = [objectclass]; 

        for(var i=0;i<objectclass.length;i++) {
            if(!objectclass[i])
                continue; // skip empty fields
            field = registeredEditorFields[objectclass[i].toLowerCase()+"."+property.toLowerCase()];
            if(Ext.isDefined(field))
                break;
        }

        if(!Ext.isDefined(field))
            field = registeredEditorFields[property.toLowerCase()];
        if(Ext.isDefined(field)) {
            return new field(cfg);
        }
    
        return registeredEditorFields["default"];
    }
}
