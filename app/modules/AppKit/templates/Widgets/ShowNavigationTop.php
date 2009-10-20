<?php if ($t['container_iterator'] instanceof RecursiveIteratorIterator) { ?>
<?php 
	$iterator = $t['container_iterator'];
	$check_depth = 0;
	$check = false;
	$open = 0;
?>

<script type="text/javascript">
<!-- // <![CDATA[
YAHOO.util.Event.onContentReady("yahooTopMenu", function () { 
	var oMenu = new YAHOO.widget.MenuBar("yahooTopMenu", {
		autosubmenudisplay: true, 
		hidedelay: 750, 
		lazyload: true,
		effect: {
			effect: YAHOO.widget.ContainerEffect.FADE,
			duration: 0.25
		}
	});
	
	oMenu.render();

});
// ]]> -->
</script>

<div id="topBar">
<div id="yahooTopMenu" class="yuimenubar yuimenubarnav">
<div class="bd"> 
<ul class="first-of-type">

<?php foreach ($iterator as $name=>$navItem) { ?>
<?php 
	$check = false;
	if ($check_depth <> $iterator->getDepth()) $check = true;
?>
<?php if ($iterator->getDepth() < $check_depth) { ?>
<?php
	for ($i=$check_depth;$i>$iterator->getDepth();$i--) {
		$open--;
		echo '</ul></div></div></li>';
	} 
?>
<?php } ?>
<li class="yuimenuitem">
<?php if ($navItem->getRoute() !== null) { ?>
<?php echo AppKitHtmlHelper::Obj()->LinkToRoute($navItem->getRoute(), $navItem->getCaption() ? $navItem->getCaption() : (string)$navItem, $navItem->getRouteArgs(), array('class' => 'yuimenuitemlabel')) ?>
<?php } else { ?>
<a name="<?php echo $navItem->getName(); ?>"><?php echo $navItem->getCaption(); ?></a>
<?php } ?>

<?php if ($navItem->getContainer()->hasChildren()) { ?>
<?php $open++; ?>

	<div id="item.<?php echo $navItem->getName(); ?>" class="yuimenubarnav"><div class="bd"><ul>
<?php } else { ?>

</li>
<?php } ?>
<?php $check_depth = $iterator->getDepth(); ?>
<?php } ?>

<?php for ($i=$open; $i>0; $i--) {?>
	</ul></div></div>
	</li>
<?php } ?>
</ul>
</div>
</div>
<?php } ?>
<a href="http://www.icinga.org/" target="_blank"><div id="icinga-logo-top"></div></a>
<!-- <div id="rss-top"><?php echo AppKitHtmlHelper::Obj()->Image('icons.rss'); ?></div> -->
<!-- <div id="links-top">User icinga | Logout | Help</div> -->
<div id="links-top">User icinga | Logout</div>
</div>