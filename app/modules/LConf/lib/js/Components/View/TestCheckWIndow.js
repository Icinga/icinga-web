Ext.ns("LConf.Views").TestCheckWindow = function(cfg) {
    this.grid = null;
    this.wnd = null;
    this.dn = null;
    this.commandLine = null;
    this.record = null;

    var errorTpl = new Ext.XTemplate(
        "<tpl if='.'>",
            "<div class='lconf_serviceCheck error'>",
                "<div class='message'>{output}</div>",
                    
            "</div>",
        "</tpl>"
    );
    errorTpl.compile();

    var successTpl = new Ext.XTemplate(
        "<tpl>",
            "<div class='lconf_serviceCheck success'>",
                _("Servicestatus")+": <div class='statusCode'>",
                    "{[LConf.Views.TestCheckWindow.getReturnCodeFormat(values.returnCode)]}",
                "</div>",
                _("Check output")+"<div class='message'>{output}</div>",
                "<div class='hint'>{hint}</div>",
            "</div>",
        "</tpl>"
    );
    successTpl.compile();



    this.constructor = function(cfg) {
        Ext.apply(this,cfg);

    }

    this.buildForm = function() {
        var insertVars = this.commandLine.match(/(\$.*?\$)/gi);
        var items = [{
            xtype: 'container',
            html: '<b>Checkcommand: </b><br/> '+this.commandLine
        }];
        if(insertVars) {
            for(var i =0;i<insertVars.length;i++) {
                var currentValue = insertVars[i];
                items.push(this.resolveToField(currentValue));
            }
        }
        return new Ext.form.FormPanel({
            padding:"2em",
            autoScroll:true,
            defaults: {
                anchor: '80%'
            },
            items: items
        });

    }

    this.show = function() {
        var form = this.buildForm();

        var formWindow = new Ext.Window({
            closable: true,
            closeaction: 'destroy',
            width: 400,
            constrain:true,
            autoHeight:true,
            title: 'Test check',
            items: [
               form
            ],
            buttons: [{
                text: 'Test Check result',
                iconCls: 'icinga-icon-cog',
                handler: function(btn) {
                    var values = form.getForm().getValues();
                    var jsonVals = Ext.encode(values);
                    Ext.Ajax.request({
                        url: this.grid.urls.checkCommand,
                        params: {
                            connectionId: this.grid.connId,
                            commandline: this.commandLine,
                            tokens: jsonVals
                        },
                        success: this.showCheckResult,
                        failure: this.showError,
                        scope: this
                    });
                    var wnd = btn.ownerCt.ownerCt;
                    wnd.removeAll();
                    wnd.add(this.progressbar());
                    wnd.doLayout();
                    wnd.syncSize();
                    var bbar = btn.ownerCt;
                    bbar.remove(btn);
                    bbar.items.items[0].setIconClass("icinga-icon-accept");
                    bbar.items.items[0].setText("Finish");
                },
                scope : this
            }, {
                text: 'Cancel',
                iconCls: 'icinga-icon-cancel',
                handler:function(btn) {
                    this.ownerCt.ownerCt.close();
                }
            }]
        });
        this.currentWindow = formWindow;
        var pos = Ext.EventObject.getXY();
        formWindow.setPosition(pos[0],pos[1]);
        formWindow.show();
    }

    this.showCheckResult = function(response) {
        try {
            var result = Ext.decode(response.responseText);
        } catch(e) {
            return this.showError(response,"Couldn't parse json ("+e+") ");
        }
        if(result.success == false) {
            this.updateError(result);
        } else {
            this.updateSuccess(result);
        }

    }

    this.updateSuccess = function(result) {
        this.updateResult(result,successTpl);
    }
    
    this.updateError = function(result) {
        if(Ext.isArray(result.errors))
            if(result.errors[0] === "401 unauthorized") {
                result.output = _("You need the lconf.testcheck credential to perform this action");
            }
        this.updateResult(result,errorTpl);
    }

    this.updateResult = function(result,tpl) {
        this.currentWindow.removeAll();

        var cmp = new Ext.Container({
            
            layout: 'fit',
            items: {
                html: tpl.apply(result)
            }
        });
        this.currentWindow.syncSize();
        this.currentWindow.add(cmp);
        this.currentWindow.doLayout();
        this.currentWindow.syncSize();
    }


   this.showError = function(response,text) {
        Ext.Msg.alert("Error while checking command", (text || "") +
            Ext.util.Format.ellipsis(response.responseText,400));
    }

    this.progressbar = function() {
        var bar = new Ext.ProgressBar({ 
            text: _('Please wait'),
            
            listeners: {
                render: function(el) {
                    el.wait({
                        interval: 100,
                        
                        increment:10,
                        text:_('Please wait...')
                    });
                }
            }
        });
       return new Ext.Container({
            height:300,
            layout: 'fit',
            autoDestroy: true,
            items: bar
        });
    }

    this.resolveToField = function(fieldname) {
        if(/\$ARG(\d*)\$/.test(fieldname)) {
           return {
               xtype: 'textfield',
               fieldLabel: fieldname,
               name: fieldname,
               value: this.args[/\$ARG(\d*)\$/.exec(fieldname)[1]-1]
           }
        }
        switch(fieldname) {
            case '$SERVICENAME$':
                var field = null;
                this.record.store.each(function(entry) {
                    if(entry.get("property") == "cn") {
                        field = {
                            xtype: 'textfield',
                            fieldLabel: fieldname,
                            name: fieldname,
                            value: entry.get("value")
                        };
                        return false;
                    }
                    return true;
                },this);
                return field;
                break;
           case '$HOSTNAME$':
                var combobox = new (LConf.Editors.ComboBoxFactory.create(
                    Ext.encode({"LDAP": ["objectclass="+this.prefix+"host"],"Attr": "cn"}),this.grid.urls
                ))();
                combobox.fieldLabel = fieldname;
                combobox.name = fieldname;
                combobox.getStore().setBaseParam("connectionId", this.grid.connId)
                return combobox;
           case '$HOSTADDRESS$':
                var combobox = new (LConf.Editors.ComboBoxFactory.create(
                    Ext.encode({"LDAP": ["objectclass="+this.prefix+"host"],"Attr": this.prefix+"address"}),this.grid.urls
                ))();
                combobox.fieldLabel = fieldname;
                combobox.name = fieldname;
                combobox.getStore().setBaseParam("connectionId", this.grid.connId)
                return combobox;
            default:
               return {
                   xtype: 'textfield',
                   fieldLabel: fieldname,
                   name: fieldname
               }
        }
    }


    this.constructor(cfg);
}

Ext.ns("LConf.Views").TestCheckWindow.getReturnCodeFormat = function(code) {
    var html = "Unknown";
    switch(code) {
        case 0:
            html = 'OK';
            break;
        case 1:
            html = 'Warning';
            break;
        case 2:
            html = 'Critical';
            break;
        case 3:
            html = 'Unknown';
            break;
    }
    return Ext.DomHelper.markup({
        tag: 'div',
        cls: 'icinga-status-'+html.toLowerCase(),
        html: html
    });

}