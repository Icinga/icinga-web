<?php $template = $rd->getParameter('template'); ?>
<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {
		var CE = this;
		
		var p = (function() {
			var pub = {};
			var panel = null;
			var pc = null;
			var url = "<?php echo $ro->gen('cronks.staticContent.content', array('template' => $template)); ?>" 
			
			Ext.apply(pub, {
				
				init : function() {
					if (!panel) {
						
						panel = new Ext.Panel({
							border: false,
							autoScroll: true,
							id: CE.cmpid,
							
							// Options for the updater
							autoLoad: {
								url: url,
								scripts: true,
								method: 'get',
								scope: this
							},
							
							// Building the toolbar
							tbar: {
								items: [{
									text: _('Refresh'),
									iconCls: 'silk-arrow-refresh',
									tooltip: _('Refresh the data in the grid'),
									handler: function(oBtn, e) { panel.getUpdater().refresh(); }
								}, {
									text: _('Settings'),
									iconCls: 'silk-cog',
									toolTip: _('Tactical overview settings'),
									menu: {
										items: [{
											xtype: 'button',
											text: _('Auto refresh'),
											iconCls: 'silk-database-refresh',
											enableToggle: true,
											handler: function(oBtn, e) {
												if (oBtn.pressed == true) {
													this.trefresh = AppKit.getTr().start({
														run: function() {
															this.getUpdater().refresh();
														},
														interval: 120000,
														scope: panel
													});
												}
												else {
													AppKit.getTr().stop(this.trefresh);
													delete this.trefresh;
												}
											}
										}]
									}
								}]
							}
						});
						
						CE.add(panel);
						CE.doLayout();
						
						return true;						
					}
					
					return false;
				}
				
			});
			
			return pub;
		})();
	
		p.init();
});
</script>