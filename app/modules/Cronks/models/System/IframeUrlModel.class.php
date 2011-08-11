<?php

/**
 * URLs in iframes handles here
 * @author mhein
 *
 */
class Cronks_System_IframeUrlModel extends CronksBaseModel {

    private $baseURl = null;

    private $user = null;

    private $pass = null;

    private $params = array();

    /**
     * @var AgaviRequestDataHolder
     */
    private $rd = null;

    public function setBaseUrl($baseUrl) {
        $this->baseURl = $baseUrl;
    }

    public function setUserPassword($user, $password) {
        $this->user = $user;
        $this->pass = $password;
    }

    public function setParamMapArray(array $paramMap) {
        $this->params = $paramMap + $this->params;
    }

    public function setRequestDataHolder(AgaviRequestDataHolder $rd) {
        $this->rd = $rd;
    }

    private function glueTogether() {

        $u = (string)$this->baseURl;

        if (count($this->params)) {

            $params = array();

            foreach($this->params as $target=>$source) {
                $m = array();

                if (preg_match('/^_(\w+)\[([^\]]+)\]$/', $source, $m)) {
                    $source = $this->rd->get(strtolower($m[1]), $m[2]);
                }

                if ($source) {
                    $params[] = sprintf('%s=%s', $target, urlencode($source));
                }
            }

            if (strpos($u, '?') !== false) {
                $u .= '&'. implode('&', $params);
            } else {
                $u .= '?'. implode('&', $params);
            }

        }

        if ($this->user && $this->pass) {
            $u = str_replace('://', sprintf('://%s:%s@', $this->user, $this->pass), $u);
        }

        return $u;
    }

    public function __toString() {
        return $this->glueTogether();
    }
}