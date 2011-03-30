<?php
    $username = $tm->_('Guest');
    $auth = false;

    if ($us->isAuthenticated()) {
        $username = $us->getNsmUser()->givenName();
        $auth = true;
		$pref = $us->getPreferences();
    }
	else {
		$pref = new stdClass();
	}


?>

<script type="text/javascript">
Ext.onReady(function() {
	 
	<?php if ($auth === true) { ?>
	AppKit.onReady(function() {
		AppKit.setPreferences(<?php echo json_encode($pref); ?>);
		
	});
	<?php } ?>
	
	// Default ajax timeout
	Ext.Ajax.timeout = Number(<?php echo AgaviConfig::get('modules.appkit.ajax.timeout', 120000); ?>);
	
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
                        iconCls: 'icinga-icon-user-edit',
                        text: _('Preferences'),
                        handler: function() {
                        	// View nicely in window :-P
							AppKit.util.doPreferences('<?php echo $ro->gen("my.preferences"); ?>');
                        }
                    }, {
                        tooltip: _('Logout'),
                        iconCls: 'icinga-icon-user-go',
                        width: 'auto',
                        handler: function() {
							AppKit.util.doLogout('<?php echo $ro->gen("appkit.logout", array('logout' => 1)); ?>');
                        }
                    }]
                }
            },
            iconCls: 'icinga-icon-user',
            <?php else: ?>
            iconCls: 'icinga-icon-user-delete',
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
				columnWidth: 1
			}, {
				id: 'menu-logo',
				width: 60,
				height: 30,
				border: false,
				cls: 'icinga-link',
				items: {
					width: 61,
					border: false,
					autoEl: 'div',
					frame: false,
		            cls: 'menu-logo-icon',
					style: 'background-image: url('+AppKit.c.path + '/images/icinga/idot-small.png);background-repeat:no-repeat;width: 27px;text-align:center; height: 30px; margin-left: 15px;margin-top:0px; display: block;'
	    		}
			}]
		}, null, 'north');
	
		Ext.Ajax.on("beforerequest",function() {
			try {
				var icon = Ext.DomQuery.selectNode('.menu-logo-icon');
				if(!icon)
					return true;
				Ext.get(icon).setStyle('background-image','url('+AppKit.c.path+'/images/ajax/icinga-throbber.gif)');
			} catch(e) {
				// ignore any errors
			}
		});
		Ext.Ajax.on("requestcomplete",function() {
			try {
				var icon = Ext.DomQuery.selectNode('.menu-logo-icon');
				if(!icon)
					return true;
				Ext.get(icon).setStyle('background-image','url('+AppKit.c.path+'/images/icinga/idot-small.png)');
			} catch(e) {
				// ignore any errors
			}
		});


		earry.items.get(1).on('render', function(c) {
			c.getEl().on('click', function() {
				AppKit.changeLocation('http://www.icinga.org');
			});
		});
	
	})();
	
});
</script>
