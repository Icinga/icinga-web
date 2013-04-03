<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}
    session_destroy();
    $message = $t['message'];
    $username = isset($t['username']) ? $t['username'] : '';
    $app_string = AgaviConfig::get('org.icinga.version.release');
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
                allowBlank: true
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
                    var old_username = '<?php echo $username; ?>';
                    if(old_username)
                        oFormPanel.getForm().findField('username').setValue(old_username);
                    
                    Ext.getCmp('menu').destroy();
                    
                    // Disable some borders
                    Ext.getCmp('viewport-center').getEl().addClass('login-page');
                    
                    var ele = Ext.DomHelper.append(Ext.getBody(), {
                        tag : 'div',
                        style : 'position: absolute;'
                        + ' top: 20px; left: 100px;'
                        + ' height: 170px;'
                        + ' width : 500px;'
                        + String.format(' background-image: url(\'{0}/images/icinga/icinga-logo-big.png\');', AppKit.util.Config.get('path'))
                        + ' background-color: #fff',
                        html : '&nbsp;'
                    });
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
            title : String.format(_('Login ({0})'), '<?php echo $app_string; ?>'),
            width : 400,
            frame : true,
            border : true,
            defaults: { border: false },
            items: [ oFormPanel ]
        });

        var oContainer = new Ext.Panel({
            width: 420,
            height : 170,
            style: {
                margin: '200px 140px', 
                padding: '10px 0 0 0'
            },
            items: oBox,
            border: true,
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
                oFormPanel.getForm().findField('username').allowBlank = false;
                oFormPanel.getForm().findField('password').allowBlank = false;
                
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
