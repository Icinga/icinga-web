<?php

interface DQLViewExtender {
    public function extend(IcingaDoctrine_Query $query,array $params);
}
?>
