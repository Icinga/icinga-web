<?php

/**
 * Interface for all search classes
 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
 */
interface IcingaApiSearchFilterInterface {
    public function getAllFilterColumns();
    public function createQueryStatement();
}
?>
