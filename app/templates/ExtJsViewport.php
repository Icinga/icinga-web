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
	</head>
	<body>
		<div id="content">
			<?php  (isset($title)) ? '<h1>'. $title. '</h1>' : null ?>
			<?php echo $inner; ?>
		</div>
	</body>
</html>