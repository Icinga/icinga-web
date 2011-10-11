(function() {
var getApiKey = function() {

    var _string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghiklmnopqrstuvwxyz1234567890";
    var rnd = parseInt(Math.random()*5,10)+25;
    var key = "";

    while(rnd--) {
        var nr = parseInt(Math.random()*_string.length,10);
        key += _string[nr];
    }
    return key;
}

Ext.ns("AppKit.Admin");

var setInternalFieldsEnabled = function(bool) {
    var field = Ext.getCmp('form_user_disabled');
    var passfield = Ext.getCmp('password_fieldset');

    field.setVisible(bool);
    passfield.setVisible(bool);
    field.setDisabled(!bool);
    passfield.setDisabled(!bool);

}



AppKit.Admin.UserEditForm = function(cfg) {
    var authTypes = [];
    Ext.iterate(cfg.authTypes, function(type) {
        authTypes.push([type]);
    });
    
    var userFlags = [{
        icon: 'icinga-icon-user-delete',
        principal: 'IcingaCommandRo',
        id: 'flag-command-only',
        text: _('Disable commands for this user')
    }, {
        icon: 'icinga-icon-group', 
        principal: 'IcingaContactgroup',
        id: 'flag-contacts-only',
        text: _('Only show items that contain a contact with this name '+
            ' in their contactgroup definitions'
        )
    }];
    var userRoleStore = new Ext.data.JsonStore({
        idProperty: 'id',
        fields: ['name','active','description','id']
    });
 
    var userCredentialStore = new Ext.data.JsonStore({
        idProperty: 'target_id',
        fields: ['target_id','target_name','target_description'],
        data: cfg.availablePrincipals
    });
 
    var userHostgroupPrincipalStore = new Ext.data.JsonStore({
        idProperty: 'hostgroup',
        fields: ['hostgroup']
    });
    var userServicegroupPrincipalStore = new Ext.data.JsonStore({
        idProperty: 'servicegroup',
        fields: ['servicegroup']
    });
    
    var userCustomvarPrincipalStore = new Ext.data.JsonStore({
        fields: ['id','name','value','target']
    });
    
    var userStore = new Ext.data.JsonStore({
        root: 'user',
        idProperty: 'id',
        url: 'none',
        fields: [
            'id',
            'name',
            'firstname',
            {name: 'disabled', type:'boolean'},
            'lastname',
            'modified',
            'created',
            'email',
            'authsrc',
            'authkey',
            'roles',
            'principals'
        ],
        newUser: function() {
            Ext.iterate(this.fields.keys,function(key) {
                var field = Ext.getCmp("form_user_"+key);
                if(!field)
                    return;
                field.setValue("");
            },this);
            Ext.getCmp("form_user_id").setValue('new');
            userRoleStore.removeAll()
            credentialView.selectValues([]);
            hostgroupPrincipalsView.selectValues([]);
            servicegroupPrincipalsView.selectValues([]);
            customVariableView.selectValues([]);
            userRestrictionFlagsView.selectValues([]);
        },
        listeners:{
            load: function(store,records, options) {
                var record = records[0];
                if(!record)
                    return;

                Ext.iterate(record.fields.keys,function(key) {
                    var field = Ext.getCmp("form_user_"+key);
                    if(!field)
                        return;
                    field.setValue(record.get(key));
                },this);
                
                userRoleStore.loadData(record.get('roles'))
                var principals = record.get('principals');
                credentialView.selectValues(principals);
                hostgroupPrincipalsView.selectValues(principals);
                servicegroupPrincipalsView.selectValues(principals);
                customVariableView.selectValues(principals);
                userRestrictionFlagsView.selectValues(principals);
            },
            scope:this
        }
       
    });
    
    var hostgroupPrincipalsView = new Ext.Panel({
        iconCls: 'icinga-icon-hostgroup',
        title: 'Hostgroups',
        layout: 'fit',
        autoScroll:true,
        tbar: [{
            text: _('Add restriction'),
            iconCls: 'icinga-icon-add',
            handler: function() {
                var emptyRecord = Ext.data.Record.create([{'name': 'hostgroup'}]);
                userHostgroupPrincipalStore.add(new emptyRecord({hostgroup: 'new restriction'}),true);
            }
        },{
            text: _('Remove selected'),
            iconCls: 'icinga-icon-cancel',
            handler: function(c) {
                var panel = c.ownerCt.ownerCt;
                var list = panel.findByType('editorgrid')[0];
                userHostgroupPrincipalStore.remove(list.getSelectionModel().getSelections());
                
            },
            scope:this
        }],
        items: [{
            xtype: 'editorgrid',
            autoScroll:true,
            sm: new Ext.grid.RowSelectionModel({singleSelect:false}),
            store: userHostgroupPrincipalStore,
            emptyText: _('No hostgroup restrictions set for this user'),
            columns: [{
                header: _('Only show members of the following hostgroups:'),
                dataIndex: 'hostgroup',
                editor: Icinga.Api.HostgroupsComboBox  
            }],
            viewConfig: {
                forceFit: true
            }
            
        }],
        selectValues: function(principals) {
            var record = Ext.data.Record.create([{name: 'hostgroup'}]);
            userHostgroupPrincipalStore.removeAll();
            Ext.iterate(principals, function(p) {
                if(p.target.target_name == 'IcingaHostgroup')
                    Ext.iterate(p.values,function(v) {
                        userHostgroupPrincipalStore.add(new record({hostgroup: v.tv_val}));
                    });
            },this);
        }
    });
    
    var servicegroupPrincipalsView = new Ext.Panel({
        iconCls: 'icinga-icon-servicegroup',
        title: 'Servicegroups',
        layout: 'fit',
        autoScroll:true,
        tbar: [{
            text: _('Add restriction'),
            iconCls: 'icinga-icon-add',
            handler: function() {
                var emptyRecord = Ext.data.Record.create([{'name': 'servicegroup'}]);
                userServicegroupPrincipalStore.add(new emptyRecord({servicegroup: 'new restriction'}),true);
            }
        },{
            text: _('Remove selected'),
            iconCls: 'icinga-icon-cancel',
            handler: function(c) {
                var panel = c.ownerCt.ownerCt;
                var list = panel.findByType('editorgrid')[0];
                userServicegroupPrincipalStore.remove(list.getSelectionModel().getSelections());
            },
            scope:this
        }],
   
        items: [{
            xtype: 'editorgrid',
            autoScroll:true,
            store: userServicegroupPrincipalStore,
            sm: new Ext.grid.RowSelectionModel({singleSelect:false}),
            emptyText: _('No servicegroup restrictions set for this user'),
            columns: [{
                header: _('Only show members of the following servicegroups:'),
                dataIndex: 'servicegroup',
                editor: Icinga.Api.ServicegroupsComboBox  
            }],
            viewConfig: {
                forceFit: true
            }
            
        }],
        selectValues: function(principals) {
            var record = Ext.data.Record.create([{name: 'servicegroup'}]);
            userServicegroupPrincipalStore.removeAll();
            Ext.iterate(principals, function(p) {
                if(p.target.target_name == 'IcingaServicegroup')
                    Ext.iterate(p.values,function(v) {
                        userServicegroupPrincipalStore.add(new record({servicegroup: v.tv_val}));
                    });
            },this);
        }
    });
   
    var showCVWindowForTarget = function(c,target) {
        target = target || 'host';
        var nameField = new Icinga.Api.RESTFilterComboBox({
            targetField: target.toUpperCase()+'_CUSTOMVARIABLE_NAME',
            target: target,
            width: 300,
            name: 'name',
            fieldLabel: Ext.util.Format.capitalize(target)+_(' customvariable'),
            allowBlank: false,
            listeners: {
                select: function(v,record) {
                    var value = record.get(v.displayField);
                    valueField.filter(v.displayField,value,true);
                    valueField.setDisabled(false);
                    valueField.reset();
                    valueField.getStore().removeAll();
                }
            }
        });

        var valueField = new Icinga.Api.RESTFilterComboBox({
            targetField: target.toUpperCase()+'_CUSTOMVARIABLE_VALUE',
            target: 'host',
            name: 'value',
            fieldLabel: _(Ext.util.Format.capitalize(target)+' customvariable value'),
            width: 300,
            disabled: true

        });

        new Ext.Window({
            title: _('Add '+target+' customvariable'),
            width: 500,
            height: 180,
            layout: 'fit',
            modal: true,
            items: [{
                xtype: 'form',
                padding: 5,
                border:false,
                items: [
                    nameField,
                    valueField
                ]
            }],
            buttons: [{
                text: _('Add customvariable'),
                iconCls: 'icinga-icon-add',
                handler: function(b) {
                    var record = Ext.data.Record.create([
                        {name:'id'}, {name: 'name'}, {name:'value'}, {name:'target'}
                    ]);
                    var form = b.ownerCt.ownerCt.findByType('form')[0].getForm();
                    if(form.isValid()) {
                        userCustomvarPrincipalStore.add(
                            new record(
                                Ext.apply(form.getValues(),{target: target})
                            )
                        );
                        b.ownerCt.ownerCt.close();      
                    }

                }
            }, {
                text: _('Cancel'),
                iconCls: 'icinga-icon-cancel',
                handler: function(c) {
                    c.ownerCt.ownerCt.close();
                }
            }]
        }).show(document.body);
    }
   
    var customVariableView = new Ext.Panel({
        title: _('Customvariable'),
        iconCls: 'icinga-icon-bricks',
        layout: 'fit',
        tbar: [{
            text: _('Add customvariable restriction'),
            iconCls: 'icinga-icon-add',
            menu: [{
                text: _('Host customvariable'),
                handler: function(c) {
                    showCVWindowForTarget(c,'host');
                },
                iconCls: 'icinga-icon-host',
                scope: this
            },{
                text: _('Service customvariable'),
                handler: function(c) {
                    showCVWindowForTarget(c,'service')
                },
                iconCls: 'icinga-icon-service',
                scope: this
            }]
            
        },{
            text: _('Remove selected'),
            iconCls: 'icinga-icon-cancel',
            handler: function(c) {
                var grid = c.ownerCt.ownerCt.findByType('grid')[0];
                userCustomvarPrincipalStore.remove(grid.getSelectionModel().getSelections());
            }
        }],
        items: [{
            xtype: 'grid',
            multiSelect: true,
            store: userCustomvarPrincipalStore,
            viewConfig: {
                forceFit: true
            },
            columns: [{
                header: ' ',
                width:20,
                dataIndex: 'target',
                renderer: function(v) {
                    return '<div class="icon-16 icinga-icon-'+v+'"></div>';
                }
            },{
                header: _('Variable'),
                dataIndex: 'name'
            }, {
                header: _('Value'),
                dataIndex: 'value'
            },{
                header: _('Affects'),
                dataIndex: 'target'
            }]
        }],
        selectValues: function(principals) {
            var record = Ext.data.Record.create([
                {name: 'value'},{name: 'name'},{name: 'target'}
            ]);
            userCustomvarPrincipalStore.removeAll();
            Ext.iterate(principals, function(p) {
                if(p.target.target_name == 'IcingaHostCustomVariablePair' ||
                    p.target.target_name == 'IcingaServiceCustomVariablePair') {
                    var entry = new record({
                        target: p.target.target_name == 'IcingaHostCustomVariablePair' ?
                            'host' : 'service'
                    });
                    Ext.iterate(p.values,function(value) {
                        switch(value.tv_key) {
                            case 'cv_name':
                                entry.set('name',value.tv_val);
                                break;
                            case 'cv_value':
                                entry.set('value',value.tv_val);
                                break;
                        }
                    },true);
                    userCustomvarPrincipalStore.add(entry);
                }
            },this);
        }
    });
    
    var credentialSelectBox = new Ext.grid.CheckboxSelectionModel({
        width: 20,
        checkOnly: true,
        listeners: {
            selectionchange: function(_this) {
                userCredentialStore.selectedValues = _this.getSelections();
            }
        }
    });
    var credentialView = new Ext.Panel({
        title: _('Credentials'),
        layout:'fit',
        iconCls: 'icinga-icon-key',
        tbar: [_('Define credentials and access rights to this user here')],
        items: [{
            xtype: 'grid',
            store: userCredentialStore,
            viewConfig: {
                forceFit: true
            },
            sm: credentialSelectBox,

            columns: [ 
                credentialSelectBox
            ,{
                header: _('Credential'),
                dataIndex: 'target_name',
                width: 100
            },{
                header: _('Description'),
                dataIndex: 'target_description',
                width: 300
            }]
        }],
        updateView: function() {
            if(userCredentialStore.selectedValues)
                credentialSelectBox.selectRecords(userCredentialStore.selectedValues);
        },
        selectValues: function(principals) {
            credentialSelectBox.clearSelections();
            userCredentialStore.selectedValues = [];
            Ext.iterate(principals, function(p) {
                if(p.target.target_type != 'credential') 
                    return true;
                userCredentialStore.selectedValues.push(userCredentialStore.getById(p.target.target_id));
            },true);
            this.updateView();
        } 
    })
    
    
    var roleView = new Ext.Panel({
        title: 'Roles',
        iconCls: 'icinga-icon-group',
        items:[{
            xtype: 'listview',
            store: userRoleStore,
            multiSelect: true,
            columns: [{
                header: _('Role'),
                dataIndex: 'name'
            },{
                header: _('Description'),
                dataIndex: 'description'
            },new (Ext.extend(Ext.list.BooleanColumn,{
                trueText: '<div style="width:16px;height:16px;margin-left:25px" class="icinga-icon-accept"></div>',
                falseText: '<div style="width:16px;height:16px;margin-left:25px" class="icinga-icon-cancel"></div>'
            }))({                
                header: _('Active'),
                dataIndex:'active',
                width: 0.1
            })]
        }],
        tbar: [{
            text: _('Add role'),
            iconCls: 'icinga-icon-add',
            handler: function(c) {
                var panel = c.ownerCt.ownerCt;
                panel.showRoleSelectionDialog()
            },
            scope:this
        },{
            text: _('Remove selected'),
            iconCls: 'icinga-icon-cancel',
            handler: function(c) {
                var panel = c.ownerCt.ownerCt;
                var list = panel.findByType('listview')[0];
                userRoleStore.remove(list.getSelectedRecords());
                
            },
            scope:this
        }],
        showRoleSelectionDialog: function() {
            var groupsStore = new Ext.data.JsonStore({
                url: cfg.roleProviderURI,
                autoLoad:true,
                autoDestroy:true,
                root: 'roles',
                fields: [{
                    name: 'id'
                },{
                    name: 'name'
                },{
                    name: 'description'
                },{
                    name: 'active'
                },{
                    name: 'daisabled_icon',
                    mapping:'active',
                    convert: function(v) {
                         return '<div style="width:16px;height:16px;margin-left:25px" class="'+(v==1? 'icinga-icon-cancel' : 'icinga-icon-accept')+'"></div>';
                    }
                }]
            });
            var grid = new Ext.grid.GridPanel({ 
                
                bbar:new Ext.PagingToolbar({
                    pageSize: 25,
                    store: groupsStore,
                    displayInfo: true,
                    displayMsg: _('Displaying roles')+' {0} - {1} '+_('of')+' {2}',
                    emptyMsg: _('No roles to display')
                }),
                store: groupsStore,
                viewConfig: {
                    forceFit:true
                },
                columns: [{
                    header: _('Id'),
                    width: 20,
                    dataIndex: 'id'
                },{
                    header: _('Name'),
                    dataIndex: 'name'
                },{
                    header: _('Description'),
                    dataIndex: 'description'
                },{
                    header: _('Status'),
                    width: 50,
                    dataIndex: 'disabled_icon'
                }]
            });
            
            (new Ext.Window({
                title: _('Select roles'),
                modal:true,
                layout:'fit',
                iconCls: 'icinga-icon-group',
                height: Ext.getBody().getHeight()*0.5,
                width: Ext.getBody().getWidth()*0.5,
                items: [grid],
                buttons: [{
                  text: _('Add selected'),
                  iconCls: 'icinga-icon-add',
                  handler: function(c) {
                      var selected = grid.getSelectionModel().getSelections();
                      Ext.iterate(selected, function(item) {
                        if(userRoleStore.getById(item.get('id')))
                            return true;
                        userRoleStore.add(item);
                        return true;
                      },this)
                      c.ownerCt.ownerCt.close();
                  }
                },{
                  text: _('Cancel'),
                  iconCls: 'icinga-icon-cancel',
                  handler: function(c) {
                      c.ownerCt.ownerCt.close();
                  }
                }]
            })).show(Ext.getBody())
        }
    })
    
    var userRestrictionFlagsView = (function() {
        var items = [];
        for(var i=0;i<userFlags.length;i++) {
            var flag = userFlags[i];
            items.push(new Ext.form.Checkbox({
                xtype: 'checkbox',
                boxLabel: flag.text,
                id: flag.id,
                name: flag.principal
            }));
        }
        
        var panel = new Ext.Panel({
            layout: 'fit',
            tbar: new Ext.Toolbar({
                items: [{
                    xtype: 'tbtext',
                   text: _('You can define additional restrictions for this user here')
                }]
            }),
            title: 'Other restrictions',
            iconCls: 'icinga-icon-cancel',
            padding: 10,
            items: {
                xtype: 'container',
                layout: 'form',
                
                border: false,
                items: items
            }
            
        });
        panel.selectedValue = [];
        panel.selectValues = function(principals) {
            var checkboxes = panel.findByType('checkbox');
            Ext.iterate(checkboxes, function(checkbox) {
                checkbox.reset();
                Ext.iterate(principals, function(p) {
                    if(p.target.target_name == checkbox.getName())
                        checkbox.setValue(true);
                })
            },this);
        }
        
        return panel;
    })();
      
    AppKit.Admin.UserEditForm.bindUser = function(id,url) {
        if(id != 'new') {
            userStore.proxy.setUrl(url+"/id="+id);
            userStore.load();
        } else {
            userStore.newUser();
        }
    }
    
    AppKit.Admin.UserEditForm.saveUser = function(url,success,fail) {
        userStore.proxy.setUrl(url+"/create");
        var params = {}
        
        var i=0;
        userRoleStore.each(function(role) {
            params["userroles["+(i++)+"]"] = role.get("id");
        });
        var i=0;
        userHostgroupPrincipalStore.each(function(p) {
            params["principal_target["+i+"][name][]"] = "IcingaHostgroup";
            params["principal_value["+i+"][hostgroup][]"] = p.get("hostgroup"); 
            params["principal_target["+i+"][set][]"] = 1;
            i++;
        });
        userServicegroupPrincipalStore.each(function(p) {
            params["principal_target["+i+"][name][]"] = "IcingaServicegroup";
            params["principal_value["+i+"][serviegroup][]"] = p.get("servicegroup"); 
            params["principal_target["+i+"][set][]"] = 1;
            i++;
        });
        userCustomvarPrincipalStore.each(function(p) {
            if(p.get("target") == "host")
                params["principal_target["+i+"][name][]"] = "IcingaHostCustomVariablePair";
            else if(p.get("target") == "service")
                params["principal_target["+i+"][name][]"] = "IcingaServiceCustomVariablePair";
            else 
                return;
            params["principal_target["+i+"][set][]"] = 1;
            params["principal_value["+i+"][cv_name][]"] = p.get("name");
            params["principal_value["+i+"][cv_value][]"] = p.get("value");
            i++;
        });
        
        Ext.iterate(userCredentialStore.selectedValues,function(p) {
            params["principal_target["+i+"][set][]"] = 1;
            params["principal_target["+i+"][name][]"] = p.get("target_name");
            i++;
        });
        Ext.iterate(userFlags,function(flag) {
            if(!Ext.getCmp(flag.id).getValue())
                return true;
            params["principal_target["+i+"][set][]"] = 1;
            params["principal_target["+i+"][name][]"] = flag.principal;
            i++;
        });
        
        var paramMap = {
            id: 'form_user_id',
            user_name: 'form_user_name',
            user_firstname: 'form_user_firstname',
            user_lastname: 'form_user_lastname',
            user_email: 'form_user_email',
            user_disabled: 'form_user_disabled',
            user_authsrc: 'form_user_authsrc',
            user_authkey: 'form_user_authkey',
            password: 'form_user_password',
            password_validate: 'form_user_password_confirmed'
        }
        for(var id in paramMap) {
            var cmp = Ext.getCmp(paramMap[id]);
            if(cmp.isValid()) 
                if(cmp.getValue()) // don't write empty fields 
                    params[id] = cmp.getValue()
                else continue;
            else return fail(arguments);
        }
        userStore.on("load",function() {
            success(arguments);
        },this,{single:true});
        userStore.on("exception",function() {
            fail(arguments);
        },this,{single:true});
        if(params.user_disabled)
            params.user_disabled = 1;
        userStore.load({params: params});
    }
    
    return [
    {
        xtype: 'hidden',
        name: 'user_id',
        id: 'form_user_id'
    },{
        xtype:'fieldset',
        title: _('General information'),
        defaults: {
            allowBlank: false
        },
        items: [{
            xtype:'textfield',
            fieldLabel: _('User name'),
            name: 'user_name',
            id: 'form_user_name',
            anchor: '95%',
            minLength: 3,
            maxLength: 127
        },{
            xtype:'container',
            layout: 'column',
            anchor: '100%',
            items: [{
                xtype:'container',
                layout:'form',

                items: {
                    fieldLabel: _('Name'),
                    name: 'user_firstname',			
                    id: 'form_user_firstname',
                    xtype:'textfield',
                    anchor: '95%',
                    allowBlank: false,
                    minLength: 3,
                    maxLength: 40
                },
                columnWidth:0.5
            },{
                xtype:'container',
                layout: 'form',
                labelWidth:65,
                items: {
                    xtype:'textfield',
                    name: 'user_lastname',	
                    id: 'form_user_lastname',
                    fieldLabel: _('Surname'),
                    anchor: '90%',
                    allowBlank: false,
                    minLength: 3,
                    maxLength: 40
                },
                columnWidth:0.5
            }]
        },{
            xtype:'textfield',
            fieldLabel: _('Email'),	
            name: 'user_email',
            id: 'form_user_email',
            anchor: '75%',
            vtype: 'email',
            maxLength : 254
        }, {
            xtype: 'checkbox',
            name: 'user_disabled',
            id: 'form_user_disabled',
            fieldLabel: _('Disabled')
        }, {
            xtype: 'combo',
            fieldLabel: _('Auth via'),
            typeAhead: true,
            name: 'user_authsrc',
            id: 'form_user_authsrc',
            triggerAction: 'all',
            mode:'local',
            store: new Ext.data.ArrayStore({
                id:0,
                fields: ['user_authkey'],
                data:authTypes
            }),
            listeners: {
                change: function(cmp) {
                    var authMethod = cmp.getValue();
                    if(authMethod == 'internal' || authMethod == 'auth_key') {
                        setInternalFieldsEnabled(true);
                        return true;
                    }
                    setInternalFieldsEnabled(false);
                }
            },
            valueField: 'user_authkey',
            displayField: 'user_authkey'
        }]
    },{
        xtype:'spacer',
        height:25
    },{
        xtype: 'fieldset',
        title: _('Change Password'),
        id:'password_fieldset',
        items: [{
            xtype:'textfield',
            fieldLabel: _('Password'),
            id: 'form_user_password',
            name: 'user_password',
            validator: function(value) {
                var auth = Ext.getCmp('form_user_authsrc');
                if(auth != 'internal' && auth != 'auth_key')
                    return true;

                if(Ext.getCmp('form_user_id').getValue() == 'new' && !value) 
                    return _("Please provide a password for this user");
                return true;
            },
            inputType:'password',
            minLength: 6,
            maxLength: 20,
            width: '200'
        },{
            xtype:'textfield',
            fieldLabel: _('Confirm password'),
            name: 'user_password_confirmed',
            id: 'form_user_password_confirmed',
            inputType:'password',
            validator: function(value)  {
                var cmp_value = Ext.getCmp('form_user_password').getValue();
                if(value != cmp_value && cmp_value != "") 
                    return _("The confirmed password doesn't match");
                return true;		
            },
            width: '200'
        }, {
            xtype: 'compositefield',
            items: [{
                fieldLabel: _('Authkey for Api (optional)'),
                id: 'form_user_authkey',
                name: 'user_authkey',
                readOnly:true,
                minLength: 8,
                maxLength: 40,
                text: getApiKey(),
                width: '200',
                xtype:'textfield',
                regex: /[A-Za-z0-9]*/
            },{
                xtype:'button',
                iconCls:'icinga-icon-arrow-refresh',
                qtip: 'Create new api key',

                handler: function() {
                    Ext.getCmp('form_user_authkey').setValue(getApiKey());
                }
            }]
        }]
    },{
        xtype: 'fieldset',
        title: _('Meta information'),
        items: [{
            xtype:'displayfield',
            fieldLabel: _('Created'),
            name: 'user_created',
            id: 'form_user_created',
            preventMark: true,
            allowBlank: true,
            anchor: '95%'				
        },{
            xtype:'displayfield',
            fieldLabel: _('Modified'),
            name: 'user_modified',
            id: 'form_user_modified',
            preventMark: true,
            allowBlank: true,
            anchor: '95%'
        }]
    },{
        xtype: 'tabpanel',
        activeTab: 0,
        height:300,
        enableTabScroll: true,
        items: [
            credentialView,
            roleView,
            hostgroupPrincipalsView,
            servicegroupPrincipalsView,
            customVariableView, 
            userRestrictionFlagsView
            
        ],
        listeners: {
            tabchange: function(_this,panel) {
                if(panel.updateView)
                    panel.updateView();
            }  
        },
        minHeight:200,
        autScroll:true,
        height:400
    }]
   
}

})();