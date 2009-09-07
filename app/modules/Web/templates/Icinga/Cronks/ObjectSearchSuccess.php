<?php 
	$htmlid = $rd->getParameter('htmlid');
?>
<script type="text/javascript">
	var oid = '<?php echo $htmlid; ?>';
	var coParent = Ext.getCmp(oid);
	
	var oSearchHandler =  function() {
		var val;
		
		return {
			keyup : function(field, e) {
				val = field.getValue();
				if (val && val.length >= 3) {
					AppKit.Ext.Message('FieldVal', val);
				}
			}
		};
		
	}();
	
	var oSearch = new Ext.FormPanel({
		labelWidth: 80,
		frame: false,
		labelWidth: 1,
		border: false,
		defaultType: 'textfield',
		
		style: {
			padding: '2px 2px 2px 2px'
		},
		
		items: [{
			title: 'Search',
			xtype: 'fancytextfield',
			name: 'q',
			enableKeyEvents: true,
			
			listeners: {
				keyup: oSearchHandler.keyup
			}
		}],
	});
	
	coParent.add(oSearch);
	
	Ext.getCmp('north-frame').doLayout();
</script>