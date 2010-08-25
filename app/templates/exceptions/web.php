<?php
/**
 * Exception template that distinguishes between HTML and plaintext output and already defines some
 * default exceptions
 * 
 */

$output = 'json';

$fixTip = "";
$fixTips = array(
	"PDO_CONN_ERR" => array(
		"Exception" =>  "PDO Connection Error",
		"Message" => "
			Couldn't connect to the icinga-web database.<br/>
			<ul>
				<li>Have entered valid credentials in %icinga_web%/app/config/databases.xml ?</li>
				<li>Is the mysql server accessible (Can you access it in from the command line) ?</li>
			</ul>
	"),
	"PDO_DRIVER_ERR" => array(
		"Exception" => "Couldn't locate driver named",
		"Message" => "
			You seem to be missing a backend driver for database.
			<ul>
				<li>Have you installed the appropiate driver package for your db (for mysql this could be php-mysql or php5-mysql)</li>
				<li>Did you restart your webserver afterwards</li>
				<li>Check your php config if the pdo_%your_db%.so is correctly loaded and available</li>
			</ul>
		"
	),
	"PDO_DRIVER_ERR" => array(
		"Exception" => "Couldn't locate driver named",
		"Message" => "
			You seem to be missing a backend driver for database.
			<ul>
				<li>Have you installed the appropiate driver package for your db (for mysql this could be php-mysql or php5-mysql)</li>
				<li>Did you restart your webserver afterwards</li>
				<li>Check your php config if the pdo_%your_db%.so is correctly loaded and available</li>
			</ul>
		"
	),
);

// errors with custom messages
$PDO_CONN_ERR = "PDO Connection Error";
$PDO_DRIVER_ERR = "Couldn't locate driver named";

if(AgaviContext::getInstance()->getController()) {
	$output =	AgaviContext::getInstance()->getController()->getOutputType()->getName();
} else {
	// Internal exception of the bootstrap
	// check if we can give a tip for fixing the error
	foreach($fixTips as $tip) {
		if(substr($e->getMessage(),0,strlen($tip["Exception"])) == $tip["Exception"]) {
			$e->fixTip = $tip["Message"];
			$output = 'html';
			break;
		}
	}
}

switch($output) {
	case 'html':
		printPrettyMessage($e);
		break;
	default:
		printPlainMessage($e);
		break;
}

function printPlainMessage(Exception $e) {
	if (!headers_sent()) {
		header('HTTP/1.1 500 Internal Server Error');
		header('Content-type: text/plain');
	}

	echo "-> 500 internal server error!\n\n";

	printf("=== Error ===\nUncaught exception %s thrown!\n\n", get_class($e));

	printf("=== Message ===\n%s\n\n", $e->getMessage());

	printf("=== Stacktrace ===\n%s", $e->getTraceAsString());
}


function printPrettyMessage(Exception $e) {
	if (!headers_sent()) {
		header('HTTP/1.1 500 Internal Server Error');
		header('Content-type: text/html');
		?>
		<html>
			<head>
				<link type="text/css" href="<?php echo AgaviConfig::get('org.icinga.appkit.web_path')."/styles/exception.css" ?>" rel="stylesheet"/>
			</head>
			<body>
				<div class='icinga_exceptionBox'>
					<div class='exception_header'><?php echo "A critical exception occured!" ?></div>
					<div class='exception_message'>

						<div class="exception_title"><?php echo "Uncaught ".get_class($e)." thrown:"?></div>
						<div class="exception_text">
							<?php if(isset($e->fixTip)) echo $e->fixTip."<br/>" ?>
							<?php echo $e->getMessage() ?>
						</div>
						<div class="exception_stacktrace"><b>Stacktrace:</b><br/><?php echo nl2br($e->getTraceAsString()) ?></div>
					</div>
				</div>
			</body>
		</html>
		<?php
	}
}
?>
