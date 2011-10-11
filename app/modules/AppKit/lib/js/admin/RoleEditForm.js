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
    var field = Ext.getCmp('form_role_disabled');
    var passfield = Ext.getCmp('password_fieldset');

    field.setVisible(bool);
    passfield.setVisible(bool);
    field.setDisabled(!bool);
    passfield.setDisabled(!bool);

}



AppKit.Admin.RoleEditForm = function(cfg) {
    var authTypes = [];
    Ext.iterate(cfg.authTypes, function(type) {
        authTypes.push([type]);
    });
    
    var roleFlags = [{
        icon: 'icinga-icon-role-delete',
        principal: 'IcingaCommandRo',
        id: 'flag-command-only',
        text: _('Disable commands for this role')
    }, {
        icon: 'icinga-icon-group', 
        principal: 'IcingaContactgroup',
        id: 'flag-contacts-only',
        text: _('Only show items that contain a contact with this name '+
            ' in their contactgroup definitions'
        )
    }];
    var roleRoleStore = new Ext.data.JsonStore({
        idProperty: 'id',
        fields: ['name','active','description','id']
    });
 
    var roleCredentialStore = new Ext.data.JsonStore({
        idProperty: 'target_id',
        fields: ['target_id','target_name','target_description'],
        data: cfg.availablePrincipals
    });
 
    var roleHostgroupPrincipalStore = new Ext.data.JsonStore({
        idProperty: 'hostgroup',
        fields: ['hostgroup']
    });
    var roleServicegroupPrincipalStore = new Ext.data.JsonStore({
        idProperty: 'servicegroup',
        fields: ['servicegroup']
    });
    
    var roleCustomvarPrincipalStore = new Ext.data.JsonStore({
        fields: ['id','name','value','target']
    });
    
    var roleStore = new Ext.data.JsonStore({
        root: 'role',
        idProperty: 'id',
        url: 'none',
        fields: [
            'id',
            'name',
            'description',
            {name: 'disabled', type:'boolean'},
            'modified',
            'created',
            'roles',
            'principals'
        ],
        newRole: function() {
            Ext.iterate(this.fields.keys,function(key) {
                var field = Ext.getCmp("form_role_"+key);
                if(!field)
                    return;
                field.setValue("");
            },this);
            Ext.getCmp("form_role_id").setValue('new');
            roleRoleStore.removeAll()
            credentialView.selectValues([]);
            hostgroupPrincipalsView.selectValues([]);
            servicegroupPrincipalsView.selectValues([]);
            customVariableView.selectValues([]);
            roleRestrictionFlagsView.selectValues([]);
        },
        listeners:{
            load: function(store,records, options) {
                var record = records[0];
                if(!record)
                    return;

                Ext.iterate(record.fields.keys,function(key) {
                    var field = Ext.getCmp("form_role_"+key);
                    if(!field)
                        return;
                    field.setValue(record.get(key));
                },this);
                
                roleRoleStore.loadData(record.get('roles'))
                var principals = record.get('principals');
                credentialView.selectValues(principals);
                hostgroupPrincipalsView.selectValues(principals);
                servicegroupPrincipalsView.selectValues(principals);
                customVariableView.selectValues(principals);
                roleRestrictionFlagsView.selectValues(principals);
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
                roleHostgroupPrincipalStore.add(new emptyRecord({hostgroup: 'new restriction'}),true);
            }
        },{
            text: _('Remove selected'),
            iconCls: 'icinga-icon-cancel',
            handler: function(c) {
                var panel = c.ownerCt.ownerCt;
                var list = panel.findByType('editorgrid')[0];
                roleHostgroupPrincipalStore.remove(list.getSelectionModel().getSelections());
                
            },
            scope:this
        }],
        items: [{
            xtype: 'editorgrid',
            autoScroll:true,
            sm: new Ext.grid.RowSelectionModel({singleSelect:false}),
            store: roleHostgroupPrincipalStore,
            emptyText: _('No hostgroup restrictions set for this role'),
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
            roleHostgroupPrincipalStore.removeAll();
            Ext.iterate(principals, function(p) {
                if(p.target.target_name == 'IcingaHostgroup')
                    Ext.iterate(p.values,function(v) {
                        roleHostgroupPrincipalStore.add(new record({hostgroup: v.tv_val}));
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
                roleServicegroupPrincipalStore.add(new emptyRecord({servicegroup: 'new restriction'}),true);
            }
        },{
            text: _('Remove selected'),
            iconCls: 'icinga-icon-cancel',
            handler: function(c) {
                var panel = c.ownerCt.ownerCt;
                var list = panel.findByType('editorgrid')[0];
                roleServicegroupPrincipalStore.remove(list.getSelectionModel().getSelections());
            },
            scope:this
        }],
   
        items: [{
            xtype: 'editorgrid',
            autoScroll:true,
            store: roleServicegroupPrincipalStore,
            sm: new Ext.grid.RowSelectionModel({singleSelect:false}),
            emptyText: _('No servicegroup restrictions set for this role'),
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
            roleServicegroupPrincipalStore.removeAll();
            Ext.iterate(principals, function(p) {
                if(p.target.target_name == 'IcingaServicegroup')
                    Ext.iterate(p.values,function(v) {
                        roleServicegroupPrincipalStore.add(new record({servicegroup: v.tv_val}));
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
                        roleCustomvarPrincipalStore.add(
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
                roleCustomvarPrincipalStore.remove(grid.getSelectionModel().getSelections());
            }
        }],
        items: [{
            xtype: 'grid',
            multiSelect: true,
            store: roleCustomvarPrincipalStore,
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
            roleCustomvarPrincipalStore.removeAll();
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
                    roleCustomvarPrincipalStore.add(entry);
                }
            },this);
        }
    });
    
    var credentialSelectBox = new Ext.grid.CheckboxSelectionModel({
        width: 20,
        checkOnly: true,
        listeners: {
            selectionchange: function(_this) {
                roleCredentialStore.selectedValues = _this.getSelections();
            }
        }
    });
    var credentialView = new Ext.Panel({
        title: _('Credentials'),
        layout:'fit',
        iconCls: 'icinga-icon-key',
        tbar: [_('Define credentials and access rights to this role here')],
        items: [{
            xtype: 'grid',
            store: roleCredentialStore,
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
            if(roleCredentialStore.selectedValues)
                credentialSelectBox.selectRecords(roleCredentialStore.selectedValues);
        },
        selectValues: function(principals) {
            credentialSelectBox.clearSelections();
            roleCredentialStore.selectedValues = [];
            Ext.iterate(principals, function(p) {
                if(p.target.target_type != 'credential') 
                    return true;
                roleCredentialStore.selectedValues.push(roleCredentialStore.getById(p.target.target_id));
            },true);
            this.updateView();
        } 
    })
    
    
    var roleView = new Ext.Panel({
        title: 'Roles',
        iconCls: 'icinga-icon-group',
        items:[{
            xtype: 'listview',
            store: roleRoleStore,
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
                roleRoleStore.remove(list.getSelectedRecords());
                
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
                        if(roleRoleStore.getById(item.get('id')))
                            return true;
                        roleRoleStore.add(item);
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
    
    var roleRestrictionFlagsView = (function() {
        var items = [];
        for(var i=0;i<roleFlags.length;i++) {
            var flag = roleFlags[i];
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
                   text: _('You can define additional restrictions for this role here')
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
      
    AppKit.Admin.RoleEditForm.bindRole = function(id,url) {
        if(id != 'new') {
            roleStore.proxy.setUrl(url+"/id="+id);
            roleStore.load();
        } else {
            roleStore.newRole();
        }
    }
    
    AppKit.Admin.RoleEditForm.saveRole = function(url,success,fail) {
        roleStore.proxy.setUrl(url+"/create");
        var params = {}
        
        var i=0;
        roleRoleStore.each(function(role) {
            params["roleroles["+(i++)+"]"] = role.get("id");
        });
        var i=0;
        roleHostgroupPrincipalStore.each(function(p) {
            params["principal_target["+i+"][name][]"] = "IcingaHostgroup";
            params["principal_value["+i+"][hostgroup][]"] = p.get("hostgroup"); 
            params["principal_target["+i+"][set][]"] = 1;
            i++;
        });
        roleServicegroupPrincipalStore.each(function(p) {
            params["principal_target["+i+"][name][]"] = "IcingaServicegroup";
            params["principal_value["+i+"][serviegroup][]"] = p.get("servicegroup"); 
            params["principal_target["+i+"][set][]"] = 1;
            i++;
        });
        roleCustomvarPrincipalStore.each(function(p) {
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
        
        Ext.iterate(roleCredentialStore.selectedValues,function(p) {
            params["principal_target["+i+"][set][]"] = 1;
            params["principal_target["+i+"][name][]"] = p.get("target_name");
            i++;
        });
        Ext.iterate(roleFlags,function(flag) {
            if(!Ext.getCmp(flag.id).getValue())
                return true;
            params["principal_target["+i+"][set][]"] = 1;
            params["principal_target["+i+"][name][]"] = flag.principal;
            i++;
        });
        
        var paramMap = {
            id: 'form_role_id',
            role_name: 'form_role_name',
            role_firstname: 'form_role_firstname',
            role_lastname: 'form_role_lastname',
            role_email: 'form_role_email',
            role_disabled: 'form_role_disabled',
            role_authsrc: 'form_role_authsrc',
            role_authkey: 'form_role_authkey',
            password: 'form_role_password',
            password_validate: 'form_role_password_confirmed'
        }
        for(var id in paramMap) {
            var cmp = Ext.getCmp(paramMap[id]);
            if(cmp.isValid()) 
                if(cmp.getValue()) // don't write empty fields 
                    params[id] = cmp.getValue()
                else continue;
            else return fail(arguments);
        }
        roleStore.on("load",function() {
            success(arguments);
        },this,{single:true});
        roleStore.on("exception",function() {
            fail(arguments);
        },this,{single:true});
        if(params.role_disabled)
            params.role_disabled = 1;
        roleStore.load({params: params});
    }
    
    return [
        {
            xtype: 'hidden',
            name: 'role_id',
            id: 'role_id'
        },{
            xtype:'fieldset',
            title: _('General information'),
            defaults: {
                allowBlank: false
            },
            items: [{
                xtype:'textfield',
                fieldLabel: _('Group name'),
                name: 'role_name',
                id: 'role_name',
                anchor: '95%',
                minLength: 3,
                maxLength: 18
            },{
                xtype:'textfield',
                fieldLabel: _('Description'),	
                name: 'role_description',
                id: 'role_description',
                anchor: '95%'

            }, {
                xtype: 'checkbox',
                name: 'role_disabled',
                id: 'role_disabled',
                fieldLabel: _('Disabled')
            }]
        },{
            xtype:'spacer',
            height:25
        },{
            xtype: 'fieldset',
            title: _('Meta information'),
            items: [{
                xtype:'displayfield',
                fieldLabel: _('Created'),
                name: 'role_created',
                id: 'role_created',
                preventMark: true,
                allowBlank: true,
                anchor: '95%'				
            },{
                xtype:'displayfield',
                fieldLabel: _('Modified'),
                name: 'role_modified',
                id: 'role_modified',
                preventMark: true,
                allowBlank: true,
                anchor: '95%'
            }]
        },{
            xtype:'fieldset',
            title: _('Permissions'),
            items: [{
                xtype:'panel',
                layout:'auto',
                autoHeight:true,
                collapsible: true,
                collapsed: false,
                title: _('Roles'),
                anchor: '95%',
                padding:4,
                labelWidth:400,
                qtip: _("Click to edit role"),
                id: 'groupRoles',
                items: []
            }, {
                xtype:'panel',
                layout: 'fit',
                title: _('Principals'),		
                anchor: '95%',
                collapsible:true,
                collapsed:true,
                id:'principalsPanel',

              //  items: AppKit.principalEditor.instance
            }]
        }
    ]
}

})();