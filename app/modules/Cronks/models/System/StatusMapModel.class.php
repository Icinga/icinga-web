<?php
/**
 * @author Christian Doebler <christian.doebler@netways.de>
 */
class Cronks_System_StatusMapModel extends CronksBaseModel {

    private $api = false;
    private $tm = false;

    private $hostResultColumns = array(
                                     'HOST_OBJECT_ID', 'HOST_NAME', 'HOST_ADDRESS', 'HOST_ALIAS', 'HOST_DISPLAY_NAME', 'HOST_CURRENT_STATE','HOST_IS_PENDING', 'HOST_OUTPUT',
                                     'HOST_PERFDATA', 'HOST_CURRENT_CHECK_ATTEMPT', 'HOST_MAX_CHECK_ATTEMPTS', 'HOST_LAST_CHECK', 'HOST_CHECK_TYPE',
                                     'HOST_LATENCY', 'HOST_EXECUTION_TIME', 'HOST_NEXT_CHECK', 'HOST_LAST_HARD_STATE_CHANGE', 'HOST_LAST_NOTIFICATION',
                                     'HOST_IS_FLAPPING', 'HOST_SCHEDULED_DOWNTIME_DEPTH', 'HOST_STATUS_UPDATE_TIME'
                                 );

    private $scriptTemplate = '<a href="#" onclick="return CronkTrigger({objectId:%s,objectName:\'%s\',objectType:\'host\'});">%s</a>';
    private $tableRowTemplate = '<tr><td>%s</td><td>%s</td></tr>';
    private $tableTemplate = '<table>%s</table>';

    /**
     * (non-PHPdoc)
     * @see lib/agavi/src/model/AgaviModel#initialize($context, $parameters)
     * @author	Christian Doebler <christian.doebler@netways.de>
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->tm = $this->getContext()->getTranslationManager();
        $this->api = $this->getContext()->getModel('Icinga.ApiContainer', 'Web');

    }

    /**
     * fetches hosts ans re-structures them to support processing of parent-child relationships
     * @return	array									hosts in parent-child relation
     * @author	Christian Doebler <christian.doebler@netways.de>
     */
    public function getParentChildStructure() {

        $hosts = array();
        $hostReferences = array();

        $idPrefix = sha1(microtime()) . '-';

        $apiResHosts = $this->api->getConnection()->createSearch();
        $apiResHosts->setResultType(IcingaApi::RESULT_ARRAY)
        ->setSearchTarget(IcingaApi::TARGET_HOST)
        ->setResultColumns($this->hostResultColumns);

        IcingaPrincipalTargetTool::applyApiSecurityPrincipals($apiResHosts);
        $apiResHosts = $apiResHosts
                       ->fetch()
                       ->getAll();

        $apiResHostParents = $this->api->getConnection()->createSearch();
        $apiResHostParents->setSearchTarget(IcingaApi::TARGET_HOST_PARENTS);

        IcingaPrincipalTargetTool::applyApiSecurityPrincipals($apiResHostParents);
        $apiResHostParents = $apiResHostParents->fetch();

        foreach($apiResHosts as $row) {
            if ($row['HOST_IS_PENDING'] == '1') {
                $row['HOST_CURRENT_STATE'] = "99";
            }

            $objectId = $idPrefix . $row['HOST_OBJECT_ID'];
            $hosts[$objectId] = array(
                                    'id'		=> $objectId,
                                    'name'		=> $row['HOST_NAME'],
                                    'data'		=> array(
                                        'status'	=> $row['HOST_CURRENT_STATE'],
                                        'relation'	=> $this->getHostDataTable($row),
                                    ),
                                    'children'	=> array(),
                                );
        }

        foreach($apiResHostParents as $row) {
            $childObjectId = $idPrefix . $row->HOST_CHILD_OBJECT_ID;
            $parentObjectId = $idPrefix . $row->HOST_PARENT_OBJECT_ID;

            if (!array_key_exists($childObjectId, $hostReferences)) {
                $hostReferences[$childObjectId] = $hosts[$childObjectId];
            }

            unset($hosts[$childObjectId]);

            if (array_key_exists($parentObjectId, $hosts)) {
                $hosts[$parentObjectId]['children'][$childObjectId] =& $hostReferences[$childObjectId];
            }

            elseif(array_key_exists($parentObjectId, $hostReferences)) {
                $hostReferences[$parentObjectId]['children'][$childObjectId] =& $hostReferences[$childObjectId];
            }
        }

        $hostsFlatStruct = $this->flattenStructure($hosts);

        if (count($hostsFlatStruct) == 1) {
            $hostsFlat = $hostsFlatStruct;
            $icingaProc = array(
                              'id'		=> $idPrefix . '-1',
                              'name'		=> 'Icinga',
                              'data'		=> array(
                                  'status'	=> '-1',
                                  'relation'	=> 'Icinga Monitoring Process',
                              ),
                              'children'	=> array(),
                          );
            array_push($hostsFlat[0]['children'], $icingaProc);
        } else {
            $hostsFlat = array(
                             'id'		=> $idPrefix . '-1',
                             'name'		=> 'Icinga',
                             'data'		=> array(
                                 'status'	=> '-1',
                                 'relation'	=> 'Icinga Monitoring Process',
                             ),
                             'children'	=> $hostsFlatStruct,
                         );
        }

        return $hostsFlat;

    }

    /**
     * wraps up additional host information in html table
     * @param	array		$hostData				information for a certain host
     * @return	string								host information as html table
     * @author	Christian Doebler <christian.doebler@netways.de>
     */
    private function getHostDataTable($hostData) {
        $hostTable = null;
        $hostObjectId = false;
        foreach($hostData as $key => $value) {
            if ($key == 'HOST_OBJECT_ID') {
                $hostObjectId = $value;
                continue;
            }

            switch ($key) {
                case 'HOST_CURRENT_STATE':
                    $value = IcingaHostStateInfo::Create($value)->getCurrentStateAsText();
                    break;

                case 'HOST_NAME':
                    $value = sprintf($this->scriptTemplate, $hostObjectId, $value, $value);
                    break;
            }

            $hostTable .= sprintf($this->tableRowTemplate, $this->tm->_($key), $value);
        }
        $hostTable = sprintf($this->tableTemplate, $hostTable);
        return $hostTable;
    }

    /**
     * brings the structure in a more usable format for json
     * @param	array		$hosts					host data
     * @return	array								cleaned host data
     * @author	Christian Doebler <christian.doebler@netways.de>
     */
    private function flattenStructure($hosts) {

        $hostsFlat = array();

        foreach($hosts as $hostId => $hostData) {
            $currentHost = array(
                               'id'		=> $hostData['id'],
                               'name'		=> $hostData['name'],
                               'data'		=> $hostData['data'],
                               'children'	=> array(),
                           );

            if (count($hostData['children'])) {
                $currentHost['children'] = $this->flattenStructure($hostData['children']);
            }

            array_push($hostsFlat, $currentHost);
        }

        return $hostsFlat;
    }

}

?>
