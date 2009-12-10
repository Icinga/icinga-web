<?php 
	$eid = AppKitRandomUtil::genSimpleId(10, 'principaledit-');
?>
<div id="<?php echo $eid; ?>"></div>
<script type="text/javascript">

(function() {

	var PrincnipalEdit = function() {

		var panel = undefined;
		
		var eid = '<?php echo $eid?>'; 

		var targets = {};
		<?php foreach (Doctrine::getTable('NsmTarget')->findAll() as $r) { ?>
		targets["<?php echo $r->target_name; ?>"] = {
			name: '<?php echo $r->target_name; ?>',
			description: '<?php echo $r->target_description; ?>',
			type: '<?php echo $r->target_type; ?>',
			fields: {
			<?php $c=false; ?>
			<?php foreach ($r->getTargetObject()->getFields() as $fname=>$fdesc) { ?>
				
				<?php echo ($c===true) ? ', ' : null; ?>'<?php echo $fname; ?>': '<?php echo $fdesc; ?>'
				<?php $c=true; ?>
			<?php } ?>
			}
		};
		<?php } ?>
		
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
					bodyStyle: 'padding: 4px',
					
					tbar: [{
						text: '<?php echo $tm->_("add"); ?>',
						iconCls: 'silk-add',
						menu: this.getMenuItems(),
					}]
				});
				
				panel.doLayout();

				return true;
			},

			addHandler : function(name) {
				panel.add({
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
						html: name,
					}]
				});
				
				panel.doLayout();
			}
		}

		return pub;
		
	}();
	
	PrincnipalEdit.buildPanel();
})();

</script>