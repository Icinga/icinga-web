<?php
/**
 * IDO Implementation of filtergroup
 *
 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
 *
 */
class IcingaApiSearchFilterGroupIdo extends IcingaApiSearchFilterGroup {
    public function createQueryStatement() {
        $statement = "";
        foreach($this as $filter) {
            if ($statement) {
                $statement .= " ".$this->getType()." ";    // Add chain type (AND/OR)
            }

            $statement .= $filter->createQueryStatement();
        }

        return "(".$statement.")";
    }

    public function __toDQL(IcingaDoctrine_Query $q,$dqlOnly=false) {
        $dql = "";
        $content = "";
        $first = true;

        foreach($this as $filter) {
            $filterDQL = $filter->__toDQL($q,true);

            if ($filterDQL) {
                // glue the operator type in front of the filter if it's not the first filter
                $content .= (($first) ? ' ' : ' '.$this->getType())." ".$filterDQL;
                $first = false;
            }
        }

        if (!$content) {
            return "";
        }

        $dql = "(".$content.")";

        if ($dqlOnly) {
            return $dql;
        }

        if ($this->getType() == 'AND') {
            $q->andWhere($dql);
        } else {
            $q->orWhere($dql);
        }
    }
}

?>
