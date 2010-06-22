<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $rd->getParameter('parentid'); ?>", function() {

	var CE = this;

	var CreateGridProcessor = function (meta) {	

		// Add base url, so static js files can build routes, too
		meta.baseURL = '<?php echo $_SERVER['SERVER_NAME'].$ro->gen("icinga.home") ?>';
		
		var MetaGrid = new Cronk.grid.MetaGridCreator(meta);
		MetaGrid.setStateUid(CE.stateuid);
		
		MetaGrid.setStoreUrl("<?php echo $ro->gen('cronks.viewProc.json', array('template' => $rd->getParameter('template'))); ?>");
		MetaGrid.setParameters(<?php echo json_encode($rd->getParameters()); ?>);
		MetaGrid.setParameters({storeDisableAutoload: true});
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
					
					var fw = new IcingaGridFilterWindow();
					
					fw.setGrid(grid);
					fw.setFilterCfg( MetaGrid.getFilterCfg() );
				
					// Distribute destroy events
					grid.on('destroy', function() {
						fw.destroyHandler();
					});
					
					grid.on('refresh', function() {
						fw.destroyHandler();	
					});
					
				
					this.topToolbar.add([
						'-', {
							text: _("Filter"),
							iconCls: 'silk-pencil',
							menu: { 
								items: [{ 
									text: _("Modify"), 
									iconCls: 'silk-application-form',
									handler: fw.startHandler,
									scope: this
								},{ 
									text: _("Remove"), 
									iconCls: 'silk-cancel',
									handler: function(b, e) {
										fw.removeFilters();
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
				
				var bCommandRo = '<?php echo $us->getNsmUser()->hasTarget("IcingaCommandRo"); ?>';
				
				if (bCommands == true && !bCommandRo == 1) {
					
					var tbEntry = this.topToolbar.add({
						text: '<?php echo $tm->_("Commands"); ?>',
						iconCls: 'silk-server-lightning',
						menu: {
							items: []
						}
					});
					
					// We need a new class
					AppKit.ScriptDynaLoader.loadScript({
						url: "<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => 'Cronks.CommandHandler')) ?>",
						
						callback: function() {
							
							// An instance to work with
							var cHandler = new IcingaCommandHandler(meta);
							
							// The entry point to start
							cHandler.setToolbarEntry(tbEntry);
							
							// We need some selection from a grid panel
							cHandler.setGrid(grid);
							
							// Where we can get some info
							cHandler.setInfoUrl('<?php echo urldecode($ro->gen("cronks.commandProc.metaInfo", array("command" => "{0}"))); ?>');
							cHandler.setSendUrl('<?php echo urldecode($ro->gen("cronks.commandProc.send", array("command" => "{0}"))); ?>');
							
							// We need something to click on :D
							cHandler.enhanceToolbar();
							
						}
					});
					
				}
			
			}
		});
		
		Ext.onReady(function() {
			// Check if the store is loaded by whatever ...
			// If no load with defautl params!
			grid.on('render', function(g) {
				if (this.storeIsLoaded() == false) {
					this.initStore();
				}
			}, MetaGrid);
			
			// Add to parent component
			CE.add(grid);
			CE.doLayout();
			
		});
	}
	
	// First loading the meta info to configure the grid
	var oContainer = function() {
		
		var s = AppKit.util.getStore('viewproc_templates');
		
		var template = "<?php echo $rd->getParameter('template'); ?>";
		var initGrid = function() {
			var meta = s.get(template);
			if (meta.template.option.dynamicscript) {
				
				AppKit.ScriptDynaLoader.on('bulkfinish', CreateGridProcessor.createCallback(meta), this, { single : true });
				AppKit.ScriptDynaLoader.startBulkMode();
				
				Ext.iterate(meta.template.option.dynamicscript, function(v,k) {
					AppKit.ScriptDynaLoader.loadScript("<?php echo $ro->gen('appkit.ext.dynamicScriptSource', array('script' => null)) ?>" + v);
				});
				
			}
			else {
				CreateGridProcessor(meta);
			}
		}
		
		if (s.containsKey(template)) {
			initGrid();
		}
		else {
		
			Ext.Ajax.request({
				   url: "<?php echo $ro->gen('cronks.viewProc.json.metaInfo', array('template' => $rd->getParameter('template'))); ?>",
				   
				   success: function(response, opts) {
				   		s.add(template, Ext.decode(response.responseText));
				   		initGrid();
				   },
				   
				   failure: function(response, opts) {
						AppKit.notifyMessage(
							"Ext.Ajax.request: request failed!",
							String.format("{0} ({1})", response.statusText, response.status)
						);
				   },
				   scope : oContainer
			});
		}
	}
	
	oContainer.call(this);
    
});
</script>
