<?php 

interface IcingaIDoctrineQueryFilter {
    public function preQuery(Doctrine_Query_Abstract $query);
    public function postQuery(Doctrine_Query_Abstract $query);
}
