<?php 
	$iterator = $t['container_iterator'];
	$check_depth = 0;
	$open = 0;
	$display_max = 0;
?>

<?php if ($iterator instanceof RecursiveIteratorIterator && count($iterator)) { ?>
<div id="navigationLeft">
<ul>
<?php foreach ($iterator as $name=>$navItem) { ?>
<?php 
	if ($navItem->isActive()) {
		$display_max = $iterator->getDepth()+1; 
	}
	elseif ($iterator->getDepth() < 1) {
		$display_max = 0;
	}
?>
<?php if ($display_max>0) { ?>
<?php // echo '<li>------------> IT '. $iterator->getDepth(). ' <-> CHECK '. $check_depth. '</li>'; ?>
<?php if ($open > 0 && $iterator->getDepth() < $check_depth) { ?>
<?php
	for ($i=$check_depth;$open > 0 && $i>$iterator->getDepth();$i--) {
		$open--;
		echo '</ul></li>';
	} 
?>
<?php } ?>
<li class="<?php echo $navItem->isActive() ? 'act' : null?>">
<?php
	$attributes = array('class' => 'link');
	if ($navItem->isActive()) $attributes['class'] .= ' act';
	
	if ($navItem->getRoute() !== null) {
		echo AppKitHtmlHelper::Obj()->LinkToRoute($navItem->getRoute(), $navItem->getCaption(), $navItem->getRouteArgs(), $attributes);
	}
	else {
		echo '<a name="'. $navItem->getName(). '">'. $navItem->getCaption(). '</a>';	
	}
?>
<?php if ($navItem->getContainer()->hasChildren()) {?>
<?php $open++; ?>
	<ul>
<?php } else { ?>
</li>
<?php } ?>
<?php } ?>
<?php
	$check_depth = $iterator->getDepth(); 
	}
?>
<?php for ($i=$open; $i>0; $i--) {?>
	</ul></li>
<?php } ?>
</ul>
</div>
<?php } ?>