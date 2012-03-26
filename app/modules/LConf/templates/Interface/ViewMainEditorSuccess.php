<?php
	$icons = AgaviConfig::get("modules.lconf.icons",array());
    $wizards =  AgaviConfig::get("modules.lconf.customDialogs",array());
?>

<script type='text/javascript'>

Ext.Msg.minWidth = 500;
Ext.onReady(function() {
    var urls = {
        ping:               '<?php echo $ro->gen("modules.lconf.ping"); ?>',
        login:              '<?php echo $ro->gen("modules.appkit.login"); ?>',
        simplesearch:       '<?php echo $ro->gen("modules.lconf.data.simplesearch"); ?>',
        directoryprovider:  '<?php echo $ro->gen("modules.lconf.data.directoryprovider"); ?>',
        properties:         '<?php echo $ro->gen("modules.lconf.data.propertyprovider"); ?>',
        filterlisting :     '<?php echo $ro->gen("modules.lconf.data.filterlisting"); ?>',
        modifyfilter :      '<?php echo $ro->gen("modules.lconf.data.modifyfilter"); ?>',
        modifynode :        '<?php echo $ro->gen("modules.lconf.data.modifynode"); ?>',
        modifyproperty :    '<?php echo $ro->gen("modules.lconf.data.modifyproperty"); ?>',
        searchreplace :     '<?php echo $ro->gen("modules.lconf.data.searchreplace"); ?>',
        connectionlisting : '<?php echo $ro->gen("modules.lconf.data.connectionlisting"); ?>',
        connect :           '<?php echo $ro->gen("modules.lconf.data.connect"); ?>',
        principals :        '<?php echo $ro->gen("modules.lconf.data.principals"); ?>',
        ldapmetaprovider :  '<?php echo $ro->gen("modules.lconf.data.ldapmetaprovider"); ?>',
        exportConfig:       '<?php echo $ro->gen("modules.lconf.export"); ?>',
        checkCommand:       '<?php echo $ro->gen("modules.lconf.testCheck"); ?>'
    };

    var icons =  <?php echo json_encode($icons); ?>;
    var presets = <?php echo $t['lconf_presets'] ?>;
    var wizards = <?php echo json_encode($wizards); ?>;
    /**
	 * Batch for displaying a specific connection/node on startup
	 */
	var connId = '<?php if(isset($t["start_connection"])) echo $t["start_connection"]?>'; 
	var dn = '<?php if(isset($t["start_dn"])) echo $t["start_dn"]?>';

    
    /**
     * Background check to detect session timeouts
     **/
	var killCheck = false;
    Ext.TaskMgr.start({
		run: function() {
			if(killCheck)
				return false;
			Ext.Ajax.request({
				url: urls.ping,
				failure: function(r) {
					if(r.status == 403) {
						Ext.Msg.confirm(_("Session expired"),
                            _("Your login session expired. <br/>By clicking 'yes' you will be redirected to the login page, press 'no' in order to stay in LConf. <span style='color:red'>You won't be able to perform any actions</span>"),
                            function(btn) {
                                if(btn == 'yes')
                                    AppKit.changeLocation(urls.loginURL);
					
                            }
                        )
						killCheck = true;		
					}	
				}
			});
            return true;
		},
		interval: 30000
	});

    (function() {
        /**
         * Register editor types
         */
        var _lconf = LConf.Editors;
        var _register = _lconf.EditorFieldManager.registerEditorField;
        _register("default",Ext.form.TextField);

        // register editor factories
        <?php
            foreach(AgaviConfig::get("modules.lconf.propertyPresets") as $type=>$preset)  {
                echo "
                _register('".$type."',LConf.Editors.".ucfirst($preset["factory"])."Factory.create('".@$preset["parameter"]."',urls));";
                foreach($preset as $subType=>$subPreset) {
                    if($subType != "factory" && $subType != "parameter")
                        echo "
                    _register('".$subType.".".$type."',LConf.Editors.".ucfirst($subPreset["factory"])."Factory.create('".@$subPreset["parameter"]."',urls));";
                }
            }
        ?>
        _lconf.EditorFieldManager.setupURLs(urls);
        
    })();
    LConf.Helper.Debug.d("Setting up viewport",
        "[urls]",urls,
        "[connId]",connId,
        "[dn]",dn,
        "[icons]",icons,
        "[wizards]",wizards,
        "[presets]",presets
    );


    new LConf.View.Viewport({
        urls: urls,
        connId: connId,
        dn: dn,
        icons: icons,
        presets: presets,
        wizards: wizards,
        parentCmp: AppKit.Layout.getCenter()
    });
    AppKit.Layout.doLayout();
})



</script>

