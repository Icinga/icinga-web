<?php 
	$htmlid = AppKitRandomUtil::genSimpleId(10, 'login-box-');
	$containerid = AppKitHtmlHelper::concatHtmlId($htmlid, 'container');
?>
<div style="width:400px; margin: 150px auto 150px auto; padding: 20px;" id="<?php echo $containerid; ?>">
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
	var sContainerId = '<?php echo $containerid; ?>';
	
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
		
		var oContainer = Ext.get(sContainerId);
		
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
					var ox = oContainer.getLeft();
					oContainer.sequenceFx();
					
					for(var i=0; i<1; i++) {
						oContainer.shift({x: oContainer.getLeft()-20, duration: .02, easing: 'bounceBoth'})
						.shift({x: oContainer.getLeft()+40, duration: .02 , easing: 'bounceBoth'})
						.shift({x: oContainer.getLeft()-20, duration: .02, easing: 'bounceBoth'})
						.pause(.03);
					}
					
					oContainer.shift({ x: ox, duration: .02, easing: 'bounceBoth', callback: pub.enableForm, scope: pub });
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
				
				AppKit.Ext.changeLocation.defer(10, null, ['<?php echo $ro->gen("index_page"); ?>']);
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
	
	oLogin.getPanel().render(sId);

})();
</script>