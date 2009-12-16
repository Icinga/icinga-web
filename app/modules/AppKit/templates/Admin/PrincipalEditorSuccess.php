<?php 
	$eid = AppKitRandomUtil::genSimpleId(10, 'principaledit-');
	
	// Principal admin model from the view
	$pa =& $t['pa'];
?>
<div id="<?php echo $eid; ?>"></div>
<script type="text/javascript">

(function() {

	var PrincnipalEdit = function() {

		var panel = undefined;
		
		var eid = '<?php echo $eid?>'; 

		var targets = <?php echo json_encode($pa->getTargetArray()); ?>;
		
		
		var pub = {
			getMenuItems : function() {
				var menu = [];
				Ext.iterate(targets, function(k, v) {
					menu.push({
						text: v.name,
						iconCls: 'silk-key',
						handler: function(b,e) {
						PrincnipalEdit.addHandler(v.name);
						}
					});
				});
				return menu;
			},

			buildPanel : function() {
				panel = new Ext.Panel({
					renderTo: eid,
					layout: 'form',
					width: 400,
					bodyStyle: 'padding: 2px 2px 2px 2px',
					tbar: [{
						text: '<?php echo $tm->_("add"); ?>',
						iconCls: 'silk-add',
						menu: this.getMenuItems(),
					}],

					defaults: {
						border: false
					},

					items: [{ height: 1 }]
				});
				
				panel.doLayout();

				return true;
			},

			addHandler : function(name) {

				var aItems = [{
					'xtype': 'label',
					'text': name
				}];

				var fields = targets[name].fields;
				
				Ext.iterate(fields, function(k, v) {
					aItems.push({
						xtype: 'textfield',
						fieldLabel: k,
					});
				});
				
				var np = new Ext.Panel({
					layout: 'hbox',
					items: [{
						xtype: 'button',
						iconCls: 'silk-cross',
						handler: function(b, e) {
							var p = b.findParentByType('panel');
							if (p) {
								p.hide();
							}
						}
					}, {
						layout: 'form',
						width: 380,
						labelWidth: 100,
						bodyStyle: 'padding: 2px 2px 2px 2px',
						border: false,
						items: aItems
					}]
				});

				panel.add(np);
				panel.doLayout();
				np.doLayout();
			}
		}

		return pub;
		
	}();
	
	PrincnipalEdit.buildPanel();
})();

</script>