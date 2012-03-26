/**
 * Grid extension that adds a column to test checks 
 * 
 */

Ext.ns("LConf.PropertyGrid.Extensions").TestCheckCommand = {
    xtype: 'action',
    appliesOn: {
        object: {
            "objectclass": ".*?service$"
        },
        properties: ".*servicecheckcommand$"
    },
    iconCls: 'icinga-icon-cog',
    qtip: _('Test this definition'),
    grid: null,
    
    handler: function(grid) {
        var checkValue = this.record.get("value");
        var checkCmd = checkValue.replace(/^(.*?)!.*/,"$1");
        var argumentRegExp = /!([^!]*)/g
        var args = [];
        while(result = argumentRegExp.exec(checkValue)) {
            args.push(result[1]);
        }
        var me =  LConf.PropertyGrid.Extensions.TestCheckCommand;
        if(typeof checkValue !== "string")
            return;
        me.grid = grid;

        Ext.Ajax.request({
            url: grid.urls.ldapmetaprovider,
            params: {
                field: Ext.encode({"LDAP":["objectclass=lconfCommand","cn="+checkCmd],"Attr":"*"}),
                connectionId: grid.connId
            },

            success: function(result) {
                var resultSet = Ext.decode(result.responseText);
                var ldapEntry = null

                if(resultSet.total > 0) {
                    ldapEntry = resultSet.result[0].entry;
                }

                me.showCheckCommandWindow(
                    ldapEntry,
                    this.record,
                    checkValue,
                    args
                );
            },
            scope: this
        });
    },

    

    showCheckCommandWindow: function(ldapEntry,record, directCheckCmd,args) {
        var commandLine = null;
        var prefix = "";
        var me = LConf.PropertyGrid.Extensions.TestCheckCommand;
        var dn = "";
        if(ldapEntry === null) {
            commandLine = record.get("value");
            prefix = record.get("property").match(/(.*?)service.*$/i)[1];
        } else {
            for(var i in ldapEntry) {
                if(i == "dn")
                    dn = ldapEntry[i];
                if(/(.*?)(commandline)$/i.test(i)) {
                    commandLine = ldapEntry[i][0];
                    if(Ext.isObject(commandLine))
                        commandLine = commandLine.data["property"];
                    prefix = i.match(/(.*?)(commandline)$/i)[1];

                }
            };
        }
        if(commandLine === null)
            return Ext.Msg.alert("Error",_("Couldn't find commandline"));
        
        var wnd = new LConf.Views.TestCheckWindow({
            grid: me.grid,
            record: record,
            dn: dn,
            prefix: prefix,
            commandLine: commandLine,
            args: args
        });
        wnd.show();
      
    }

};
