<?php

/**
 * Validator that takes a string and exports it to an array by splitting it
 * by a specified char.
 *
 * @author jmosshammer <jannis.mosshammer@netways.de>
 *
 */
class AppKitSplitValidator extends AgaviValidator {

    protected function validate() {
        $context = $this->getContext();
        $argument = $this->getArgument();
        $data = $this->getData($argument);
        $splitChar = $this->getParameter("split",";");
        $splitted = explode($splitChar,$data);
        $this->export($splitted);
        return true;
    }

}