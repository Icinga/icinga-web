<script type="text/javascript">
Cronk.util.initEnvironment("<?php echo $parentid = $rd->getParameter('parentid'); ?>", function() {

	var searchHandler = Cronk.util.SearchHandler({
		minChars: "<?php echo AgaviConfig::get('modules.cronks.search.numberMinimumLetters', 2); ?>",
		proxyUrl: "<?php echo $ro->gen('modules.cronks.objectsearch.json')?>"
	});

	var myTextField = new Ext.form.TextField({
		title: 'Search',
		name: 'q',
		enableKeyEvents: true,
		width: 180
	});
	
	searchHandler.setTextField(myTextField);
	
	var oSearch = new Ext.Panel({
		layout: 'hbox',
		layoutConfig: {
		    align : 'middle',
		    pack  : 'start'
		},
		
		id: 'objectsearch',
		
		flex: 1,
		labelWidth: 0,
		border: false,
		
		defaults: { padding: 4 },
		
		items: [{
			html: {
				'tag': 'img',
				'src': AppKit.util.Dom.imageUrl('icons.magnifier')
			}
		}, myTextField, {
			html: {
				'tag': 'img',
				'src': AppKit.util.Dom.imageUrl('icons.cross'),
				'style': 'cursor: pointer;'
			},
			listeners: {
				render: function(p) {
					p.getEl().on('click', searchHandler.resetSearchbox, searchHandler);
				}
			}
		}],
		height: 50
	});
	
	this.add(oSearch);
	
	this.doLayout();
	
});
</script>
