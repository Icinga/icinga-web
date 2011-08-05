<?php
	$message = $t['message'];
	$username = isset($t['username']) ? $t['username'] : '';
?>
<script pe="text/javascript">
Ext.onReady(function() {
	var bAuthenticated = false;
	
	<?php if ($us->isAuthenticated() == true) { ?>
	bAuthenticated = true;
	<?php } ?>
	
	var oLogin = function() {
		
		var pub;
		
		var oButton = new Ext.Button({
			text: '<?php echo $tm->_("Login"); ?>',
			id: 'login_button',
			handler: function(b, e) {
				pub.disableForm();
				pub.doSubmit();
			}
		});
		
		var oFormPanel = new Ext.form.FormPanel({
			labelWidth: 100,
			defaultType: 'textfield',
			bodyStyle: { padding: '5px 5px', marginTop: '10px' },
			
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
				allowBlank: true
			}],
			
			listeners: {
				afterrender: function(p) {
					pub.resetForm(true);
					oFormPanel.getForm().findField('username').setValue('<?php echo $username; ?>');
				}
			},
			
			keys: [{
				key: Ext.EventObject.ENTER,
				scope: pub,
				stopEvent: true,
				fn: function() {
					pub.doSubmit()
				}
			}],
			
			buttons: [oButton]
		});

		var oBox = new Ext.Panel({
			id: 'login-dialog',
			baseCls: 'x-box',
			frame: true,
			defaults: { border: false },
			items: [ { bodyCfg: { tag: 'h1', html: _('Login') } }, oFormPanel ]
		});

		var oContainer = new Ext.Panel({
			width: 400,
			style: { margin: '120px auto', padding: '10px 0 0 0' },
			items: oBox,
			border: false,
			id: 'login-container'
		});

		var messageTip = null;

		<?php if ($message==true): ?>

		messageTip = new Ext.ToolTip({
			id: 'login-message-tooltip',
			target: 'login-dialog',
			anchor: 'left',
			title: '<?php echo (isset($t['message_title'])) ? $t['message_title'] : null; ?>',
			autoHide: false,
			closable: true,
			contentEl: 'login-message-container',
			showDelay: 500,
			autoShow: true
		});

		oFormPanel.addButton({
			iconCls: 'icinga-icon-help',
			tooltip: _('Click here to view instructions'),
			handler: function(button, event) {
				var m = oLogin.getMessage();
				if (m.isVisible()) {
					m.hide();
				}
				else {
					m.show();
				}
			}
		});

		<?php endif; ?>


		var oFormAction = new Ext.form.Action.Submit(oFormPanel.getForm(), {
			clientValidation: true,
			url: '<?php echo $ro->gen("modules.appkit.login.provider"); ?>',
			
			params: {
				dologin: 1
			},
			
			failure: function(f, a) {
				
				if (a.failureType != Ext.form.Action.CLIENT_INVALID) {
					var c = {
						waitTime: 5
					};
					
					AppKit.notifyMessage('<?php echo $tm->_("Login failed"); ?>', '<?php echo $tm->_("Please verify your input and try again!"); ?>', null, c);
				}
				
				/* oBox.highlight("cc0000", {
				    attr: 'background-color',
				    easing: 'easeOutStrong',
				    duration: 2
				}); */
				
				if (oBox) {
					var ox = oBox.getEl();
					var orgX = ox.getLeft();
					ox.sequenceFx();
					
					for(var i=0; i<1; i++) {
						ox.shift({x: ox.getLeft()-20, duration: .02, easing: 'bounceBoth'})
						.shift({x: ox.getLeft()+40, duration: .02 , easing: 'bounceBoth'})
						.shift({x: ox.getLeft()-20, duration: .02, easing: 'bounceBoth'})
						.pause(.03);
					}
					
					ox.shift({ x: orgX, duration: .02, easing: 'bounceBoth', callback: pub.enableForm, scope: pub });
				}
				
				pub.resetForm();
				
			},
			
			success: function(f, a) {
				pub.disableForm(true);
				AppKit.changeLocation.defer(1, null, ['<?php echo $ro->gen("index_page"); ?>']);
			}
		});
		
		pub = {

			getMessage : function() {
				return messageTip;
			},

			hasMessage : function() {
				if (!Ext.isEmpty(messageTip) && Ext.isObject(messageTip)) {
					return true;
				}
				return false;
			},

			getPanel : function() {
				return oContainer;
			},
			
			getForm : function() {
				return oFormPanel.getForm();
			},
			
			getAction : function() {
				return oFormAction;
			},
			
			doSubmit : function() {
				this.getForm().doAction(this.getAction());
			},
			
			resetForm : function(full) {
				if (full != undefined) {
					this.getForm().reset();
				}
				else {
					this.getForm().findField('password').setValue("");
				}
				
				this.getForm().findField('username').focus('', 10);
			},
			
			enableForm : function() {
				this.getForm().findField('username').enable();
				this.getForm().findField('password').enable();
				oButton.enable();
			},
			
			disableForm : function(full) {
				if (full != undefined) {
					this.getForm().findField('username').disable();
					this.getForm().findField('password').disable();
				}
				
				oButton.disable();
			}
		};
		
		return pub;
	}();

	AppKit.util.Layout.addTo({
		items: oLogin.getPanel()
	});

	AppKit.util.Layout.doLayout();

	<?php if (isset($t['message_expand_first']) && $t['message_expand_first'] == true): ?>
	if (oLogin.hasMessage()) {
		oLogin.getMessage().show();
	}
	<?php endif; ?>
});
</script>
<?php if ($message==true): ?>
<div class="x-hidden" id="login-message-container">
<?php echo $t['message_text']; ?>
</div>
<?php endif; ?>