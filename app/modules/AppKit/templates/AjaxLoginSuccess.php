<?php 
	$htmlid = AppKitRandomUtil::genSimpleId(10, 'login-box-');
	$containerid = AppKitHtmlHelper::concatHtmlId($htmlid, 'container');
?>
<script type="text/javascript" defer="defer">
Ext.onReady(function() {

	var bAuthenticated = false;
	var sId = '<?php echo $htmlid ?>';
	
	<?php if ($us->isAuthenticated() == true) { ?>
	bAuthenticated = true;
	<?php } ?>
	
	var oLogin = function() {
		
		var pub;
		
		var oButton = new Ext.Button({
			text: '<?php echo $tm->_("Try"); ?>',
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
				allowBlank: false
			}],
			
			listeners: {
				afterrender: function(p) {
					pub.resetForm(true);
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
		
		var oContainer = new Ext.Panel({
			width: 400,
			style: { margin: '120px auto', padding: '10px 0 0 0' },
			baseCls: 'x-box',
			frame: true,
			defaults: { border: false },
			items: [ { bodyCfg: { tag: 'h1', html: _('Login') } }, oFormPanel ]
		});
		
		var oFormAction = new Ext.form.Action.Submit(oFormPanel.getForm(), {
			clientValidation: true,
			url: '<?php echo $ro->gen("appkit.login.provider"); ?>',
			
			params: {
				dologin: 1
			},
			
			failure: function(f, a) {
				
				if (a.failureType != Ext.form.Action.CLIENT_INVALID) {
					var c = {
						waitTime: 5
					};
					
					AppKit.Ext.notifyMessage('<?php echo $tm->_("Login failed"); ?>', '<?php echo $tm->_("Please verify your input and try again!"); ?>', null, c);
				}
				
				/* oContainer.highlight("cc0000", {
				    attr: 'background-color',
				    easing: 'easeOutStrong',
				    duration: 2
				}); */
				
				if (oContainer) {
					var ox = oContainer.getEl();
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
				
				var p = new Ext.Panel({
					style: 'margin-top: 20px;',
					bodyCssClass: 'x-icinga-simplebox-green',
					html: '<?php echo $tm->_("Successfully logged in. You should be redirected immediately. If not please <a href=\"%s\">click here to change location by hand</a>.", null, null, array($ro->gen("index_page"))); ?>',
					unstyled: true,
					layout: 'fit',
					forceLayout: true
				});
				
				oFormPanel.add(p);
				oFormPanel.doLayout();
				
				AppKit.changeLocation.defer(10, null, ['<?php echo $ro->gen("index_page"); ?>']);
			}
		});
		
		pub = {
			
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
	
	AppKit.Layout.getCenter().add({
		items: oLogin.getPanel()
	});
	
	AppKit.Layout.doLayout();

});
</script>
