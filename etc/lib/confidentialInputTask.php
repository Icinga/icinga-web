<?php
/**
 * Input task that disables output
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class confidentialInputTask extends InputTask {
	
	public function main() {
		system("stty -echo");
		parent::main();
		system("stty echo");

		// Adding an \n to have a line line breaks
		// for multiple inputs
		echo chr(10);
	}
	
	public function __destruct() {
		system("stty echo");
	}
}