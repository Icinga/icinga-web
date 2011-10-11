
Ext.ns("AppKit.Admin");
AppKit.Admin.RoleManager = function(cfg) {
    // private static
    var roleProviderURI = cfg.roleProviderURI;
    console.log("2");
    var containerCmp = null;
    var roleGridCmp = null;
    var roleFormCmp = null;
    
    
    
    var roleList = new Ext.data.JsonStore({
		autoDestroy: true,
		storeId: 'roleListStore',
		totalProperty: 'totalCount',
		root: 'roles',
		idProperty: 'id',

        url: roleProviderURI,
		remoteSort: true,

		baseParams: {
			hideDisabled: false
		},
        proxy: new Ext.data.HttpProxy({
            api: {
                read: {method: 'GET', url: roleProviderURI}
            }
        }),
  		fields: [
			{name: 'role_id', type:'int'},
			'role_name',
			'role_description',
			{name: 'role_disabled', type:'boolean'},
			{name: 'role_disabled_icon',type:'boolean' , mapping:'role_disabled', convert: function(v) {
				return '<div style="width:16px;height:16px;margin-left:25px" class="'+(v==1? 'icinga-icon-cancel' : 'icinga-icon-accept')+'"></div>';
			}},
			{name: 'role_created'},
			{name: 'role_modified'},
			{name: 'role_parent'}
		]
	})
 
    var initRoleGridComponent = function() {
       roleGridCmp = new Ext.grid.GridPanel({
            title: _('Available roles'), 
            stateful: false,
            sm: new Ext.grid.RowSelectionModel(),
            iconCls: 'icinga-icon-group',
            
            deleteSelected: function() {
                Ext.Msg.confirm(_("Delete role"),_("Do you really want to delete these roles?"),function(btn) {
                    if(btn != "yes")
                        return false;
                    var selModel = this.getSelectionModel();
                    var selected = selModel.getSelections();
                    var ids = [];

                    Ext.each(selected,function(record) {
                        ids.push(record.get("id"));
                    },this);
                    var uri = roleProviderURI+"/ids="+ids.join(",");                        
                    Ext.Ajax.request({
                        url: uri,
                        method: 'DELETE',
                        success: function() {
                            this.getStore().reload();
                        },
                        scope:this,
                        params: ids

                    });
                },this)
            },
            viewConfig : {
                scrollOffset:30,
                forceFit:true
            },
            tbar: {
                items: [{
                    xtype: 'button',
                    iconCls: 'icinga-icon-arrow-refresh',
                    scope:this,
                    text: 'Refresh',
                    handler: function() {
                        roleGridCmp.getBottomToolbar().doRefresh();
                    }
                        
                },{
                    xtype: 'button',
                    iconCls: 'icinga-icon-cancel',
                    text: _('Remove selected'),
                    handler: function(ev,btn) {
                        roleGridCmp.deleteSelected();
                    },
                    scope: this
                },' ',{
                    xtype: 'button',
                    iconCls: 'icinga-icon-add',
                    text: _('Add new role'),
                    handler: function() {
                        AppKit.Admin.RoleEditForm.bindRole('new', roleProviderURI);
                        Ext.getCmp('roleEditor').setDisabled(false);
                        Ext.getCmp('btn-save-group').setText(_('Create role'));
                        Ext.getCmp('btn-save-group').setIconClass('icinga-icon-group-add');
                        Ext.getCmp('progressbar-field').setValue();
                    }

                },'->',{
                    xtype:'button',
                    enableToggle:true,
                    text: _('Hide disabled'),
                    id:'hide_disabled',
                    name: 'disabled',
                    listeners: {
                        toggle: function(btn,checked) {
                            roleGridCmp.getStore().setBaseParam('hideDisabled',checked);
                            return true;
                        }
                    }
                }]
                
            },
            bbar: new Ext.PagingToolbar({
                pageSize: 25,
                store: roleList,
                displayInfo: true,
                displayMsg: _('Displaying roles')+' {0} - {1} '+_('of')+' {2}',
                emptyMsg: _('No roles to display')
            }), 

            store : roleList,

            listeners: {
                rowclick: function(grid,index,_e) {
                    var id = grid.getStore().getAt(index).get("id");
                    Ext.getCmp('roleEditor').setDisabled(false);
                    Ext.getCmp('btn-save-group').setText(_('Save'));
                    Ext.getCmp('btn-save-group').setIconClass('icinga-icon-disk');
                    Ext.getCmp('progressbar-field').setValue();
                    AppKit.Admin.RoleEditForm.bindRole(id, roleProviderURI);									
                }

            },


            colModel: new Ext.grid.ColumnModel({
                defaults: {
                    width:120,
                    sortable:true
                },
                columns: [
                    {id:'id', header: 'ID', width:75,  dataIndex: 'id'},
                    {header: _('rolename'), dataIndex: 'name'},
                    {header: _('lastname'), dataIndex: 'lastname'},
                    {header: _('firstname'), dataIndex: 'firstname'},
                    {header: _('email'),dataIndex: 'email'},
                    {header: _('active'), dataIndex: 'disabled_icon',width:75}
                ]
            })

        });
    }
    
    var initContainerComponent = function() {
        if(roleGridCmp == null)
            throw "Role grid component not correctly initialized, aborting container creation";
        
        containerCmp = new Ext.Container({
            layout: 'fit',
            
            items: new Ext.Panel({
                layout: 'border',
                border:false,
                defaults: {
                    margins: {top: 10, left: 10, bottom: 0}
                },
                items: [{
                    region:'center',
                    xtype:'panel',
                    layout:'fit',
                    id:'roleListPanel',
                    
                    items: roleGridCmp,
                    autoScroll:true,
                    listeners: {
                        render: function() {
                            roleList.load({params: {start:0,limit:25}})
                        }
                    }
                },{
                    region: 'east',
                    xtype: 'panel',
                    padding: 5,
                    disabled:true,
                    split:true,
                    id: 'roleEditor',
                    autoScroll:true,
                    title: _('Edit role'),
                    items: roleFormCmp,
                    buttons: [
                    {   
                        xtype: 'displayfield',
                        id:'progressbar-field',
                        width:200
                    },{
                        iconCls: 'icinga-icon-disk',
                        id: 'btn-save-group',
                        text: _('Save'),
                        handler:  function(b) {
                            b.setIconClass('icinga-icon-throbber');
                            b.setText(_("Saving role"));
                            b.setDisabled(true);
                            AppKit.Admin.RoleEditForm.saveRole(
                                roleProviderURI,
                                function() {
                                    Ext.getCmp('progressbar-field').setValue(
                                        "<span style='color:green;margin:4px;'>"+_("Role saved successfully")+"</span>"
                                    );
                                    b.setIconClass('icinga-icon-disk');
                                    b.setText(_("Save"));
                                    b.setDisabled(false);
                                    roleList.load({params: {start:0,limit:25}})
                                },
                                function() {
                                    Ext.getCmp('progressbar-field').setValue(
                                        "<span style='color:red;margin:4px;'>"+_("Couldn't save role, review your settings")+"</span>"
                                    );
                                    b.setIconClass('icinga-icon-disk');
                                    b.setText(_("Retry"));
                                    b.setDisabled(false);
                                }
                            );
                            
                        }
                    }],
                    width: '30%'
                }]	
            })
        });
        
    }
    
    var initRoleFormComponent = function() {
       
        roleFormCmp = new Ext.form.FormPanel({
            border: false,
            items: AppKit.Admin.RoleEditForm(cfg)
        })
    }
    
    this.construct = function() {
        initRoleGridComponent();
        initRoleFormComponent();
        initContainerComponent();
        
     //   AppKit.util.Layout.getCenter().add(containerCmp);
     //   AppKit.util.Layout.doLayout();
        
    }
    
    this.construct();
}