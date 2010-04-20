<!DOCTYPE html>
<html>
	<head>
		<title><?php 
			if(isset($t['title'])) {
				echo htmlspecialchars($t['title']). ' - '
				. AgaviConfig::get('core.app_name');
			}
		?></title>
		
		<meta charset="UTF-8">
		
		<?php echo $slots['head']; ?>
		
		<?php echo $slots['head_navigation']; ?>
		
		<?php echo $slots['head_start']; ?>
		
	</head>
	<body>
		<div id="content" class="x-hidden">
			<?php  (isset($title)) ? '<h1>'. $title. '</h1>' : null ?>
			<?php echo $inner; ?>
		</div>
	</body>
</html>