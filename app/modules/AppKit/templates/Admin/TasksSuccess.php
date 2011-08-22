<script language="text/javascript">
Ext.onReady(function() {
	var tasksUrl = '<?php echo $ro->gen("modules.appkit.admin.tasks") ?>';
	var icingaControl = (function() {
		var viewAccess = false; //<?php echo AgaviContext::getInstance()->getUser()->hasCredentials("icinga.control.admin") ? 'true' : 'false'; ?>;	
		var icingaStatUrl = '<?php echo $ro->gen("api.icingaStatus") ?>';	
 		if(!viewAccess) 
			return {dontShow: true, xtype:'label', text: _('Not allowed')} 
		
		var statusRenderer = function(value, metaData, record, rowIndex, colIndex, store) {
			switch(value) {
				case 0:
					return '<div class="icinga-status icinga-status-up">'+_('Running')+'</div>';
					break;
				case 1:
					return '<div class="icinga-status icinga-status-down">'+_('Stopped')+'</div>';
					break;	
				default:
					return '<div class="icinga-status icinga-status-unknown">'+_('Unknown')+'</div>';
					break;	
			}
		};
		
		var errorHandler = function (value, metaData, record, rowIndex, colIndex, store) {
			var tMsg;
			switch(value) {
				case 'SSH_NA_Err':
					tMsg =  _("You're icinga-web server doesn't have ssh2 enabled, couldn't check results");
					break;
				case 'AuthErr':
					tMsg = _("Authorization failed for this instance");
					break;
				case 'CommandErr':
					tMsg = _("Couldn't submit check command - check your access.xml");
					break;
				case 'IAuthErr':
					tMsg = _("Invalid authorization type defined in access.xml");
					break;
				case 'Unknown':
					tMsg = _("Unknown error - please check your logs");
					break;
				default:
					return '';
			}
			var id = Ext.id('errNode');
			var el = '<div id="'+id+'" class="icon-32 icinga-icon-exclamation-red"></div>';
			(new Ext.util.DelayedTask(	
				function() {
					new Ext.ToolTip({
						target : id,
						html: tMsg
					})
				}
			)).delay(200);
			return el;
		
		};
		var addRestartButton = function(instance,container) {
			(new Ext.Button({
				iconCls: 'icinga-icon-arrow-refresh',
				applyTo:container,
				style: 'float:left',
				tooltip: _('(Re-)Start icinga process'),
				handler: submitIcingaCommand.createCallback('restart',instance)
			})).show();
		}
		var addCancelButton = function(instance,container) {
			(new Ext.Button({
				iconCls: 'icinga-icon-cancel',
				applyTo:container,
				style: 'float:left',
				tooltip: _('Stop icinga process'),
				handler: submitIcingaCommand.createCallback('shutdown',instance)
			})).show();
		}
		var submitIcingaCommand = function(type,instance) {
			var confMessage = "";
			switch(type) {
				case 'restart':
					confMessage = _("Please confirm restarting icinga");
					break;
				case 'shutdown':
					confMessage = _("Please confirm shutting down icinga");
					break;
				default:
					return false;
			}
			Ext.Msg.confirm(_("Confirm"),confMessage,function(btn, text){
				if (btn != 'yes')
					return false;
       			sendCommand({action:type,instance:instance},icingaInstancesGrid.getStore().reload.createDelegate(icingaInstancesGrid.getStore()));
			})
		}
	
		var sendCommand = function(params, success) {
			try {
				var mask = new Ext.LoadMask(Ext.getBody(), {msg: _("Please wait...")});
				mask.show();
				Ext.Ajax.request({
					url: icingaStatUrl,
					params: params,
					callback: function() {
						mask.hide();
					},
					success: function(resp) {
	
						if (Ext.isFunction(success)) {
							success.call();
						}
					}
		
				});
			} catch(e) {
				mask.hide();
				AppKit.log(e);
			}
		};
		var btnsToRender = [];
		var parseBtnsTask = new Ext.util.DelayedTask(
			function() {
				for(var i=0;i<btnsToRender.length;i++) {
					var btn = btnsToRender[i];
					if(btn.restart)
						addRestartButton(btn.instance,btn.id);
					if(btn.stop)
						addCancelButton(btn.instance,btn.id);
				}
				btnsToRender = [];	
			}
		);	
		var btnAddHandler = function(value,metaData,record,rowIndex,colIndex) {
			var id =Ext.id('btnGrp');		
			if(value == 0)  
				btnsToRender.push({id: id,instance: record.get('instance'), row: rowIndex, col:colIndex, restart: true, stop: true})	
			if(value == 1) 	
				btnsToRender.push({id: id,instance: record.get('instance'), row: rowIndex, col:colIndex,restart: true, stop: false})	
			
			parseBtnsTask.delay(100);
			return '<div style="float:left" id="'+id+'" />';
		};

		var icingaInstancesGrid = new Ext.grid.GridPanel({
			store: new Ext.data.JsonStore({
				autoDestroy: true,
				url: icingaStatUrl,
				root: 'instances',
				storeId: 'appkit.tasks.icingaInstancesStore',
				idProperty: 'instance',
				autoLoad:true,
				fields: ['instance','status','error','actions']
			}),
			colModel: new Ext.grid.ColumnModel({
				columns: [
					{id: 'status',header: _('Status'), dataIndex:'status', width: 100, renderer:statusRenderer},
					{id: 'error', menuDisabled:true, width:32,padding:0, sortable: true, dataIndex: 'error', renderer:errorHandler },
					{id: 'instance', header: _('Instance'), width:100, sortable: true, dataIndex: 'instance' },
					{id: 'actions', menuDisabled:true, width:100,padding:0, sortable: true, dataIndex: 'status', renderer:btnAddHandler }	
				]
			}),
			width: 500,
			height:200,
			autoFit:true,
			scrollable: true,
			autoScroll:true,
			frame:true,
			tbar: [{
				xtype: 'button', 
				text: _('Refresh'), 
				iconCls: 'icinga-icon-arrow-refresh',
				handler: function() {
					var store = Ext.StoreMgr.get('appkit.tasks.icingaInstancesStore');
					store.reload();
				},
				scope: this
			}]
		});

		return icingaInstancesGrid;
	})()

	var ar = function(params, success) {
		try {
			var mask = new Ext.LoadMask(Ext.getBody(), {msg: _("Saving")});
			mask.show();
			Ext.Ajax.request({
				url: tasksUrl,
				params: params,
				callback: function() {
					mask.hide();
				},
				success: function() {
					if (Ext.isFunction(success)) {
						success.call();
					}
				}
	
			});
		} catch(e) {
			mask.hide();
			AppKit.log(e);
		}
	};
	
	var form = new Ext.Panel({
		autoScroll:true,
		layout: 'fit',
		
		bodyStyle: 'padding: 10px 10px;',
		
		defaults: {
			border: false
		},
		
		items: [new Ext.form.FormPanel({
			items: [{
				xtype: 'fieldset',
				title: _('Clear cache'),
				items: [{
					xtype: 'label',
					text: _('Clear the agavi configuration cache to apply new xml configuration.')
				}, {
					xtype: 'button',
					iconCls: 'icinga-icon-database-delete',
					text: _('Clear'),
					handler: function() {
						ar({task: 'purgeCache'}, function() {
							Ext.Msg.show({
								title: _('Success'),
								msg: _('In order to complete you have to reload the interface. Are you sure?'),
								icon: Ext.MessageBox.QUESTION,
								buttons: Ext.Msg.YESNO,
								fn: function(a) {
									if (a=='yes') {
										AppKit.changeLocation(AppKit.c.path);
									}
								}
							});
						})
					}
				}]
			},{
				xtype: 'fieldset',
				hidden: icingaControl.dontShow,
				title: _('Icinga status'),
				items: [{
					xtype: 'label',
					text: _('Show status of accessible icinga instances')
				},
					icingaControl
				]
			}]
		})]
	});
	
	if (Ext.getCmp('admin_tasks_window')) {
		Ext.getCmp('admin_tasks_window').add(form);
		Ext.getCmp('admin_tasks_window').doLayout();
	}
});
</script>
