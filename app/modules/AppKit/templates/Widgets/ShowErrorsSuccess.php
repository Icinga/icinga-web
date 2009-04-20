<?php 
	$items = $t['message_items'];
	if (is_array($items) && count($items) > 0) {
	$i=0;
?>
<div class="messageQueueFrame">
<?php foreach ($items as $item) { ?>
<?php 
	$i++;
	$classes = array();
	$classes[] = 'messageItem';
	
	if ($item->getType() & AppKitMessageQueueItem::ERROR) {
		$classes[] = 'stateError';
	}
	
	if ($item->getType() & AppKitMessageQueueItem::INFO) {
		$classes[] = 'stateInfo';
	}
	
	if ($item->getType() & AppKitMessageQueueItem::LOG) {
		$classes[] = 'stateLog';
	}
?>
	<div class="<?php echo implode(' ', $classes); ?>">
		<div class="messageText">
			<div class="messageTextContainer"><?php echo $item; ?></div>
		</div>
	</div>
<?php } ?>
</div>
<?php } ?>