<?php

class AppKitUriValidator extends AgaviValidator {

    protected function validate() {
        $data = $this->getData($this->getArgument());

        if (strpos($data, '/') !== 0) {
            $this->throwError('beginning');
            return false;
        }

        if (preg_match('/\/$/', $data)) {
            $this->throwError('end');
            return false;
        }

        return true;
    }

}

?>