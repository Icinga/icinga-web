<?php
	$template = $rd->getParameter('template');
	$render = $rd->getParameter('render', 'MAIN');
	$cmpid = $rd->getParameter('cmpid');
?>
<script type="text/javascript">
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {
		var CE = this;
		
		var p = (function() {
			var pub = {};
			var panel = null;
			var pc = null;
			var template_name = '<?php echo $template; ?>';
			var url = "<?php echo $ro->gen('cronks.staticContent.content', array('template' => $template, 'render' => $render, 'cmpid' => $cmpid)); ?>";
			
			url = url.replace(/&amp;/g, '&');
			
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
								
								text: String.format(_('Loading TO "{0}" ...'), template_name),
								
								/*
								 * @todo: make timeout configurable
								 */
								timeout: 600 // Very long for too much data
							},
							
							// Building the toolbar
							tbar: {
								items: [{
									text: _('Refresh'),
									iconCls: 'icinga-icon-arrow-refresh',
									tooltip: _('Refresh the data in the grid'),
									handler: function(oBtn, e) { panel.getUpdater().refresh(); }
								}, {
									text: _('Settings'),
									iconCls: 'icinga-icon-cog',
									toolTip: _('Tactical overview settings'),
									menu: {
										items: [{
											text: _('Auto refresh'),
											checked: false,
											checkHandler: function(checkItem, checked) {
												if (checked == true) {
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