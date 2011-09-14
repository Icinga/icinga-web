<?php
	$webpath = $t['web_path'];
	$type = $rd->getParameter('type', 'javascript');
	$imports = isset($t['imports']) && is_array($t['imports']) ? $t['imports'] : array();
	$includes = isset($t['includes']) && is_array($t['includes']) ? $t['includes'] : array();
	
	switch($type) {
		case 'javascript':
			foreach($includes as $_ => $include) {
				echo
<<<INCLUDE
<script type="text/javascript" src="$include"></script>
INCLUDE
;
			}
				
			break;
		case 'css':
			echo '<style type="text/css">';
			foreach($imports as $_ => $import) {
				$import = $webpath . $import;
				echo
<<<IMPORT
@import url("$import");
IMPORT
;
			}
			echo '</style>';
			
			foreach($includes as $_ => $include) {
				echo
<<<INCLUDE
<link href="$include" rel="stylesheet" type="text/css">
INCLUDE
;						
			}
				
			break;
	}
?>
