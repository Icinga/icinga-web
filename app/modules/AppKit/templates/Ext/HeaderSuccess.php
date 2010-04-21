<?php
    $username = $tm->_('Guest');
    $auth = false;
    if ($us->isAuthenticated()) {
        $username = $us->getNsmUser()->givenName();
        $auth = true;
    }
?>
<script type="text/javascript">
Ext.onReady(function() {
	var umenu = Ext.getCmp('menu-user');
	var UserMenu = (function() {
	    var pub = {};
	    var auth = '<?php echo ($auth) ? 'true' : 'false'; ?>';
		var json = null;
		
		try {
			json=AppKit.util.Layout._decodeMenuData((<?php echo $t['json_menu_data']; ?>));
		}
		catch(e) {}

		AppKit.util.Layout.addTo({
			layout: 'column',
			autoHeight: true,
			id: this.fuid_menu,
			border:false,
			items: [{
				tbar: new Ext.Toolbar({
					id: 'menu-bar',
					items: json['items'] || {}
				}),
				columnWidth: 1,
				border: false
			}, {
				id: 'menu-user',
				width: 150,
				border: false,
				items: {
			        tbar: new Ext.Toolbar({
			            id: 'menu-user-sub',
			            items: [{
			                <?php if ($auth === true): ?>
			                
			                menu: {
			                    items: {
			                        xtype: 'buttongroup',
			                        columns: 2,
			                        autoWidth: true,
			                        defaults: {
			                            scale: 'large',
			                            iconAlign: 'left',
			                            width: '100%'
			                            
			                        },
			                        items: [{
			                            tooltip: _('Preferences'),
			                            iconCls: 'silk-user-edit',
			                            text: _('Preferences')
			                        }, {
			                            tooltip: _('Logout'),
			                            iconCls: 'silk-user-go',
			                            width: 'auto'
			                        }]
			                    }
			                },
			                iconCls: 'silk-user',
			                <?php else: ?>
			                iconCls: 'silk-user-delete',
			                <?php endif; ?>
			                text: '<?php echo $username; ?>'
			            }]
			        })
				}
			}, {
				id: 'menu-logo',
				width: 25,
				border: false,
				items: {
			        border: false,
			        width: 25,
			        height: 25,
			        autoEl: {
			            tag: 'img',
			            src: AppKit.c.path + '/images/icinga/idot-small.png'
			        },
			        listeners: {
			        	click: function() {
			        		AppKit.notifyMessage('Picture', ' ... successfully clicked');
			        	}
			        }
	    		}
			}]
		}, null, 'north');
		
//	    Ext.getCmp('menu-logo').getEl().on('click', function() {
//	        AppKit.changeLocation('http://www.icinga.org');
//	    });
	
	})();
	
});
</script>