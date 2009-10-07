<?php 
	$htmlid = AppKitRandomUtil::genSimpleId(10);
?>
<div style="width:370px; margin: 150px auto 150px auto;">
    <div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>
    <div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc">
        <h3 style="margin-bottom:5px;"><?php echo $tm->_('Login'); ?></h3>
        <div id="<?php echo $htmlid; ?>"></div>
    </div></div></div>
    <div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>
</div>
<script type="text/javascript">
(function() {

	var bAuthenticated = false;
	var sId = '<?php echo $htmlid ?>';
	
	<?php if ($us->isAuthenticated() == true) { ?>
	bAuthenticated = true;
	<?php } ?>
	
	var oLogin = function() {
		
		var pub;
		
		var oFormPanel = new Ext.form.FormPanel({
			labelWidth: 100,
			defaultType: 'textfield',
			bodyStyle: 'padding: 5px;',
			
			defaults: {
				msgTarget: 'side'
			},
			
			items: [{
				fieldLabel: '<?php echo $tm->_("User"); ?>',
				name: 'username',
				id: 'username',
				allowBlank: false
			}, {
				fieldLabel: '<?php echo $tm->_("Password"); ?>',
				inputType: 'password',
				name: 'password',
				id: 'password',
				allowBlank: false
			}],
			
			listeners: {
				afterrender: function(p) {
					pub.resetForm();
				}
			},
			
			keys: [{
				key: Ext.EventObject.ENTER,
				scope: pub,
				fn: function() {
					pub.doSubmit()
				}
			}],
			
			buttons: [{
				text: '<?php echo $tm->_("Try"); ?>',
				id: 'login_button',
				handler: function(b, e) {
					pub.doSubmit();
				}
			}]
		});
		
		var oFormAction = new Ext.form.Action.Submit(oFormPanel.getForm(), {
			clientValidation: true,
			url: '<?php echo $ro->gen("appkit.login.provider"); ?>',
			waitMsg: '<?php echo $tm->_("Verifying credentials ..."); ?>',
			
			params: {
				dologin: 1
			},
			
			failure: function(f, a) {
				
				if (a.failureType != Ext.form.Action.CLIENT_INVALID) {
					var c = {
						waitTime: 5
					};
					
					AppKit.Ext.notifyMessage('<?php echo $tm->_("Login failed"); ?>', '<?php echo $tm->_("Please verify your input and try again!"); ?>', null, c);
					
					pub.resetForm();

				}
				
				f.getEl().highlight("f39a00", {
				    attr: 'background-color',
				    easing: 'easeIn',
				    duration: 1
				});
			},
			
			success: function(f, a) {
				AppKit.Ext.changeLocation('<?php echo $ro->gen("index_page"); ?>');
			}
		});
		
		pub = {
			
			getPanel : function() {
				return oFormPanel;
			},
			
			getForm : function() {
				return this.getPanel().getForm();
			},
			
			getAction : function() {
				return oFormAction;
			},
			
			doSubmit : function() {
				this.getForm().doAction(this.getAction());
			},
			
			resetForm : function() {
				this.getForm().reset();
				this.getForm().findField('username').focus('', 10);
			}
			
		};
		
		return pub;
	}();
	
	oLogin.getPanel().render(sId);

})();
</script>