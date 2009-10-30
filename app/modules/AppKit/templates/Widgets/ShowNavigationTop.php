<?php if ($t['container_iterator'] instanceof RecursiveIteratorIterator) { ?>
<?php 
	$iterator = $t['container_iterator'];
	$check_depth = 0;
	$check = false;
	$open = 0;
?>

<div id="menuTopTarget"></div>

<script type="text/javascript">
<!-- // <![CDATA[
Ext.onReady(function() {

<?php

$d = '[';

foreach ($iterator as $name=>$navItem) {

	$check = false;
	if ($check_depth <> $iterator->getDepth()) $check = true;

	if ($iterator->getDepth() < $check_depth) {

		for ($i=$check_depth;$i>$iterator->getDepth();$i--) {
			$open--;
			$d .= ']}},';
		} 

	}

	$d .= '{';
	$d .= 'text: "'. $navItem->getCaption(). '",';
	
	if ($navItem->getRoute() !== null) {
		$d .= 'href: "'. $ro->gen( $navItem->getRoute() ). '",';
	
	} else {
		// UH?
	}

	if ($navItem->getContainer()->hasChildren()) {
		$open++;
		$d .= 'menu: { items: [';
	} else {
		$d .= '},';
	}
	
	$check_depth = $iterator->getDepth();
	
	}

	for ($i=$open; $i>0; $i--) {
		$d .= ']}},';
	}

}

$d .= ']';

?>

var xh = '';

<?php if ($us->isAuthenticated()) { ?>
xh += '<?php echo $tm->_('User')?>:&#160;<?php echo $us->getNsmUser()->givenName(); ?>'
xh += '| <a href="<?php echo $ro->gen('appkit.logout'); ?>">Logout</a>'
<?php } else { ?>
xh += '<?php echo $tm->_('User')?>:&#160;<?php echo $tm->_('Guest')?>'
<?php } ?>

	var p = new Ext.Panel({
		applyTo: 'menuTopTarget',
		layout: 'column',
		border: false,
		
		defaults: {
			border: false
		}
	});

	p.add({
		xtype: 'toolbar',
		defaults: {
			listeners: {
				mouseover: function(e) {
					this.showMenu();
				}
			}
		},
		columnWidth: .8,
		cls: 'x-icinga-top-toolbar',
		items: <?php echo $d?>
	});

	p.add({
		xtype: 'panel',
		columnWidth: .2,
		html: xh,
		baseCls: 'x-icinga-top-right',
		height: 31
	});

	p.doLayout();
});
// ]]> -->
</script>