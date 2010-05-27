<?php
    $username = $tm->_('Guest');
    $auth = false;
    if ($us->isAuthenticated()) {
        $username = $us->getNsmUser()->givenName();
        $auth = true;
    }
?>
<?
	
?>
<script type="text/javascript">
Ext.onReady(function() {
	var UserMenu = (function() {
	    var pub = {};
	    var _LA = AppKit.util.Layout;
	    
	    var auth = '<?php echo ($auth) ? 'true' : 'false'; ?>';
		var json = null;
		
		try {
			json=_LA._decodeMenuData((<?php echo $t['json_menu_data']; ?>));
		}
		catch(e) {}

		var AppKitNavBar = new Ext.Toolbar({
			id: 'menu-navigation',
			items: json['items'] || {},
			defaults: { border: false },
			style: 'border: none;',
			height: 30
		});

		AppKitNavBar.add([{
			xtype: 'tbfill'
		}, {
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
                        text: _('Preferences'),
                        handler: function() {
							AppKit.changeLocation('<?echo $ro->gen("my.preferences"); ?>');
                        }
                    }, {
                        tooltip: _('Logout'),
                        iconCls: 'silk-user-go',
                        width: 'auto',
                        handler: function() {
							AppKit.util.doLogout('<?echo $ro->gen("appkit.logout", array('logout' => 1)); ?>');
                        }
                    }]
                }
            },
            iconCls: 'silk-user',
            <?php else: ?>
            iconCls: 'silk-user-delete',
            <?php endif; ?>
            text: '<?php echo $username; ?>'
        }]);

		var earry = _LA.addTo({
			layout: 'column',
			id: 'menu',
			border: false,
			defaults: { style: { borderLeft: '1px #d0d0d0 solid' }, border: false, height: 30 },
			items: [{
				tbar: AppKitNavBar,
				columnWidth: 1,
			}, {
				id: 'menu-logo',
				width: 60,
				height: 30,
				border: false,
				cls: 'icinga-link',
				items: {
			        autoEl: {
			            tag: 'img',
			            style: 'width: 25px; height: 25px; margin: 2px auto; display: block;',
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
		
		earry.items.get(1).on('render', function(c) {
			c.getEl().on('click', function() {
				AppKit.changeLocation('http://www.icinga.org');
			});
		});
	
	})();
	
});
</script>