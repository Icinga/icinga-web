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
                var umenu = Ext.getCmp('menu-user');
                var logo = Ext.getCmp('menu-logo');
                var amenu = null;
                
                
                
                umenu.add({
                	border: false,
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
                });
                
                logo.add({
                    border: false,
                    width: 25,
                    height: 25,
                    autoEl: {
                        tag: 'img',
                        src: AppKit.c.path + '/images/icinga/idot-small.png'
                    }
                });
                
                logo.el.on('click', function() {
                    AppKit.changeLocation('http://www.icinga.org');
                });
                
                AppKit.Layout.getNorth().doLayout();
            })();
});
</script>
