<script language="text/javascript">
Ext.onReady(function() {
	
	var url = '<?php echo $ro->gen("appkit.admin.tasks") ?>';
	
	var ar = function(params, success) {
		try {
			var mask = new Ext.LoadMask(Ext.getBody(), {msg: _("Saving")});
			mask.show();
			Ext.Ajax.request({
				url: url,
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
			}]
		})]
	});
	
	if (Ext.getCmp('admin_tasks_window')) {
		Ext.getCmp('admin_tasks_window').add(form);
		Ext.getCmp('admin_tasks_window').doLayout();
	}
});
</script>