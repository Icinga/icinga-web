<?php 
	$parentid = $rd->getParameter('parentid');
?>
<script type="text/javascript">

(function() {
	
	var CreateGridProcessor = function (meta) {	
		
		var MetaGrid = new AppKit.Ext.grid.MetaGridCreator(meta);
		
		MetaGrid.setStoreUrl("<?php echo $ro->gen('icinga.cronks.viewProc.json', array('template' => $rd->getParameter('template'))); ?>");
		MetaGrid.setParameters(<?php echo json_encode($rd->getParameters()); ?>);
		
		var grid = MetaGrid.createGrid();
		
		// Magick includes (Grid filters)
		// <?php include(AppKitInlineIncluderUtil::getJsFile('js/IcingaGridFilterHandler.js')); ?>
		
		// Add the window to a toolbar button
		grid.on('render', function(g) {
			
			if (meta.template.option.mode == 'minimal') {
				this.topToolbar.hide();
			}
			else {
			
				var bFilters = false;
				
				Ext.iterate(MetaGrid.getFilterCfg(), function() {
					if (bFilters == false) bFilters = true;
				});
			
				if (bFilters == true) {
				
					IcingaGridFilterWindow.setGrid(grid);
					IcingaGridFilterWindow.setFilterCfg( MetaGrid.getFilterCfg() );
				
					// Distribute destroy events
					grid.on('destroy', function() {
						IcingaGridFilterWindow.destroyHandler();
					});
					
					grid.on('refresh', function() {
						IcingaGridFilterWindow.destroyHandler();	
					});
					
				
					this.topToolbar.add([
						'-', {
							text: '<?php echo $tm->_("Filter"); ?>',
							iconCls: 'silk-pencil',
							menu: { 
								items: [{ 
									text: '<?php echo $tm->_("Modify"); ?>', 
									iconCls: 'silk-application-form',
									handler: IcingaGridFilterWindow.startHandler,
									scope: this
								},{ 
									text: '<?php echo $tm->_("Remove"); ?>', 
									iconCls: 'silk-cancel',
									handler: function(b, e) {
										IcingaGridFilterWindow.removeFilters();
									},
									scope: this
								}]
							}
						}
					]);
				
				}
				
				// If the templates uses commands
				var Options = MetaGrid.getOptions();
				var bCommands = (Options['commands'] && Options['commands']['enabled'] == true) ? true : false;
				
				if (bCommands == true) {
					
					var tbEntry = this.topToolbar.add({
						text: '<?php echo $tm->_("Commands"); ?>',
						iconCls: 'silk-server-lightning',
						menu: {
							items: []
						}
					});
					
					// We need a new class
					AppKit.Ext.ScriptDynaLoader.loadScript({
						url: "<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.CommandHandler')) ?>",
						
						callback: function() {
							
							// An instance to work with
							var cHandler = new IcingaCommandHandler(meta);
							
							// The entry point to start
							cHandler.setToolbarEntry(tbEntry);
							
							// We need some selection from a grid panel
							cHandler.setGrid(grid);
							
							// Where we can get some info
							cHandler.setInfoUrl('<?php echo urldecode($ro->gen("icinga.cronks.commandProc.metaInfo", array("command" => "{0}"))); ?>');
							cHandler.setSendUrl('<?php echo urldecode($ro->gen("icinga.cronks.commandProc.send", array("command" => "{0}"))); ?>');
							
							// We need something to click on :D
							cHandler.enhanceToolbar();
							
						}
					});
					
				}
			
			}
		});
		
		//Insert the grid in the parent
		var cmp = Ext.getCmp("<?php echo $parentid; ?>");
		cmp.insert(0, grid);
		
		// Refresh the container layout
		Ext.getCmp('view-container').doLayout();
	}

	// First loading the meta info to configure the grid
	var oContainer = function() {
		
		Ext.Ajax.request({
			   url: "<?php echo $ro->gen('icinga.cronks.viewProc.json.metaInfo', array('template' => $rd->getParameter('template'))); ?>",
			   
			   success: function(response, opts) {
			   		
					var meta = Ext.decode(response.responseText);
					// Include needed javascript by the xml template
					if (meta.template.option.dynamicscript) {
						// Register the create grid event
						var f = CreateGridProcessor.createCallback(meta);
						AppKit.Ext.ScriptDynaLoader.on('bulkfinish', function() {
							f.call();
							run = true;
						}, this, { single : true });
						
						AppKit.Ext.ScriptDynaLoader.startBulkMode();
						
						Ext.iterate(meta.template.option.dynamicscript, function(v,k) {
							AppKit.Ext.ScriptDynaLoader.loadScript("<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => null)) ?>" + v);
						});
						
					}
					else {
						CreateGridProcessor(meta);
					}
			   },
			   
			   failure: function(response, opts) {

					AppKit.Ext.notifyMessage(
						"Ext.Ajax.request: request failed!",
						String.format("{0} ({1})", response.statusText, response.status)
					);
					
			   },
			   
			   scope : oContainer
		});
	}
	
	oContainer.call(this);
    
})();

</script>
