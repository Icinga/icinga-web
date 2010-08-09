<?php
	$webpath = $t['web_path'];
	$ns = AppKitModuleUtil::DEFAULT_NAMESPACE;
?>
<?php // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ ?>
<?php if ($rq->hasAttribute('app.meta_tags', $ns)): ?>
<?php
	$meta_tags = $rq->getAttribute('app.meta_tags', $ns);
	if (count($meta_tags) == 1 && isset($meta_tags[0])):
	$meta_tags = $meta_tags[0];
?>
<?php foreach ($meta_tags as $module): ?>
<?php foreach ($module as $mname=>$mvalue): ?>
	<meta name="<?php echo $mname; ?>" content="<?php echo $mvalue; ?>" />
<?php endforeach; ?>
<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>


<?php // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ ?>
<?php if ($rq->hasAttribute('app.css_files', $ns)): ?>
		<style type="text/css">
<?php foreach ($rq->getAttribute('app.css_files', $ns) as $file): ?>
			@import url("<?php echo $webpath. $file; ?>");
<?php endforeach; ?>
		</style>
<?php endif; ?>
<?php if (is_array($t['css_raw']) && count($t['css_raw'])) { ?>
		<!-- RAW css script -->
		<style type="text/css">
		<!-- /* <![CDATA[ */
<?php foreach ($t['css_raw'] as $css_name => $css_raw) { ?>
			/* start css <?php echo $css_name; ?> */
		
			<?php echo $css_raw; ?>
		
			/* stop css <?php echo $css_name; ?> */
<?php } ?>
		/* ]]> */ -->
		</style>
<?php } ?>


<?php // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ ?>
<?php if (isset($t['js_files_includes']) && is_array($t['js_files_includes'])): ?>
	<?php foreach ($t['js_files_includes'] as $js_file) { ?>
	<script type="text/javascript" src="<?php echo $js_file; ?>"></script>
	<?php } ?>
<?php endif; ?>
<?php if (is_array($t['js_raw']) && count($t['js_raw'])) { ?>
		<!-- RAW js code -->
		<script type="text/javascript">
		//<!-- // <![CDATA[
<?php foreach ($t['js_raw'] as $js_name => $js_raw) { ?>

			// start JS data: <?php echo $js_name; ?>

			<?php echo $js_raw; ?>

			// stop JS data: <?php echo $js_name; ?>

<?php } ?>
		// ]]> -->
		</script>
<?php } ?>


<?php // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ ?>