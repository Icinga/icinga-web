<?php
/**
 * IDO Implementation of the api filter
 * @author Jannis Mosshammer <jannis.mosshammer@netways.de>
 */
class IcingaApiSearchFilterIdo extends IcingaApiSearchFilter {
    public function createQueryStatement() {
        $field = $this->getField();
        $value = $this->getValue();
        $match = $this->getMatch();

        if ($match == IcingaApiConstants::MATCH_LIKE || $match == IcingaApiConstants::MATCH_NOT_LIKE) {
            $value = str_replace("*","%",$value);
        }

        $statementSkeleton = $field." ".$match." '".$value."' ";

        return $statementSkeleton;
    }

    public function __toDQL(IcingaDoctrine_Query $q,$dqlOnly = false) {
        $field = $this->getField();
        $value = $this->getValue();
        $match = $this->getMatch();

        if ($match == IcingaApiConstants::MATCH_LIKE || $match == IcingaApiConstants::MATCH_NOT_LIKE) {
            $value = str_replace("*","%",$value);
        }
        $value = preg_replace("/'/","\'",$value);
        $field = preg_replace("/(.*?) +AS +.+ */i","$1",$field);

        $statementSkeleton = $field." ".$match." '".$value."' ";

        return $statementSkeleton;

    }
}


?>
