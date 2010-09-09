<!DOCTYPE html>
<html>
	<head>
		<title><?php 
			if(isset($t['title'])) {
				echo htmlspecialchars($t['title']). ' - '
				. AgaviConfig::get('core.app_name');
			}
			else {
				echo AgaviConfig::get('core.app_name');
			}
		?></title>
		
		<meta charset="UTF-8">
		
		<?php echo $slots['head']; ?>
		
		<?php echo $slots['head_start']; ?>
		
	</head>
	<body>
		<noscript>
			<div style="margin:auto;margin-top:10%;width:500px;text-align:center;padding:5px;-webkit-border-radius:5px;-moz-border-radius:5px;border:1px solid black;background-color:#dedede">
				<h1>Oops...JavaScript support is disabled!</h1>
				You have to activate JavaScript in order to use Icinga-web.
			</div>
		</noscript>
		<div id="content" class="x-hidden">
			<?php  (isset($title)) ? '<h1>'. $title. '</h1>' : null ?>
			<?php echo $inner; ?>
		</div>
	</body>
</html>