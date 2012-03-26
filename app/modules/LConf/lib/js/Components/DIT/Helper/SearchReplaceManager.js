Ext.ns("LConf.DIT.Helper").SearchReplaceManager = function(tree) {
    this.tree = tree;

    var getSearchReplaceForm = function() {
        return new Ext.form.FormPanel({
            layout: 'form',
            borders: false,
            labelWidth:300,
            padding:5,
            items: [{
                xtype:'textfield',
                fieldLabel: _('Search RegExp:'),
                name: 'search',
                allowBlank: false
            },{
                xtype: 'textfield',
                fieldLabel: _('Attributes to include (comma-separated)'),
                name: 'fields',
                allowBlank: false
            },{
                xtype: 'textfield',
                fieldLabel: _('Replace String:'),
                name: 'replace',
                allowBlank: true
            }]
        });
    }

    this.execute = function() {
        var curid = Ext.id();
        var form = getSearchReplaceForm();
        
        new Ext.Window({
            modal:true,
            id : 'wnd_'+curid,
            autoDestroy:true,
            constrain:true,
            height:150,
            width:600,
            title: _("Search/Replace"),
            renderTo: Ext.getBody(),
            layout:'fit',
            items: form,
            buttons: [{
                text: _('Sissy mode (Just show me what would be done)'),
                handler :function() {
                    var _bForm = form.getForm();
                    if(!_bForm.isValid())
                        return false;
                    this.callSearchReplace(_bForm.getValues(),true);
                    return true;
                },
                scope:this
            },{
                text: _('Execute'),
                handler :function() {
                    var _bForm = form.getForm();
                    if(!_bForm.isValid())
                        return false;
                    this.callSearchReplace(_bForm.getValues());
                    Ext.getCmp('wnd_'+curid).close();
                    return true;
                },
                scope:this
            }]
        }).show();
    }

    this.callSearchReplace = function(values,SissyMode) {
        var mask = new Ext.LoadMask(Ext.getBody(),_("Please wait"));
        mask.show();
        Ext.Ajax.request({
            url: tree.urls.searchreplace,
            params: {
                search: values["search"],
                fields: values["fields"],
                replace: values["replace"],
                filters: tree.filterState.getActiveFilters(),
                connectionId: tree.connId,
                sissyMode: SissyMode
            },

            success: function(resp) {
                mask.hide();
                if(SissyMode)
                    Ext.Msg.alert(_("Search/Replace"),_("The following changes would be made:<br/>")+resp.responseText);
                else if(resp.responseText  != 'success') {
                    var error = Ext.decode(resp.responseText);
                    var msg = "<div class='lconf_infobox'><ul>";
                    Ext.each(error,function(err){
                        err = Ext.util.Format.ellipsis(err,200,true);
                        msg += "<li>"+err+"</li>";
                    });
                    msg += "</ul></div>";
                    Ext.Msg.alert(_('Search/Replace error'),_("The following errors were reported:<br/>"+msg));
                } else {
                    Ext.Msg.alert(_("Success"),_("Seems like everything worked fine!"));
                }
                tree.refreshNode();
            },
            failure: function(resp) {
                mask.hide();
                var error = Ext.util.Format.ellipsis(resp.responseText,400);

                Ext.Msg.alert(_("Error"),error);
            },
            scope:this
        });
    }

}