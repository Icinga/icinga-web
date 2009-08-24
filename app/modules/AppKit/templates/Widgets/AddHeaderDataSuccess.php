<!-- Additional header data -->
<?php if (is_array($t['meta_tags']) && count($t['meta_tags'])) { ?>
		<!--  META tags -->
<?php foreach ($t['meta_tags'] as $tag_name=>$tag_val) { ?>
		<meta name="<?php echo $tag_name; ?>" content="<?php echo $tag_val; ?>" />
<?php } } ?>

<?php if (is_array($t['css_files']) && count($t['css_files'])) { ?>
		<!-- CSS file imports -->
		<style type="text/css">
		<!-- /* <![CDATA[ */
<?php foreach ($t['css_files'] as $css_file) { ?>
			@import url("<?php echo $css_file; ?>");
<?php } ?>
		/* ]]> */ -->
		</style>
<?php } ?>

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


<?php if (is_array($t['js_files']) && count($t['js_files'])) { ?>
		<!-- JS included files -->
	<?php foreach ($t['js_files'] as $js_file) { ?>
		<script type="text/javascript" src="<?php echo $js_file; ?>">
		<!-- // <![CDATA[
		// NO INLINE SCRIPT
		// ]]> -->
		</script>
	<?php } ?>
<?php } ?>

<?php if (is_array($t['js_raw']) && count($t['js_raw'])) { ?>
		<!-- RAW js code -->
		<script type="text/javascript">
		<!-- // <![CDATA[
<?php foreach ($t['js_raw'] as $js_name => $js_raw) { ?>

			// start JS data: <?php echo $js_name; ?>

			<?php echo $js_raw; ?>

			// stop JS data: <?php echo $js_name; ?>

<?php } ?>
		// ]]> -->
		</script>
<?php } ?>
