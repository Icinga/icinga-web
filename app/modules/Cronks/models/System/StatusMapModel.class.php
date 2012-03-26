<?php
/**
 * Data provider for the status map
 *
 * @author Jannis Moßhammer <jannis.mosshammer@netways.de>
 */
class Cronks_System_StatusMapModel extends CronksBaseModel {


    private $connectionName = null;
    private $tm = false;

    private $scriptTemplate = '<a href="#" onclick="return CronkTrigger({objectId:%s,objectName:\'%s\',objectType:\'host\'});">%s</a>';
    private $tableRowTemplate = '<tr><td>%s</td><td>%s</td></tr>';
    private $tableTemplate = '<table>%s</table>';

    /**
     * (non-PHPdoc)
     * @see lib/agavi/src/model/AgaviModel#initialize($context, $parameters)
     */
    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        $this->tm = $this->getContext()->getTranslationManager();
       
        $this->connectionName = isset($parameters["connection"]) ? $parameters["connection"] : "icinga";

    }

    /**
     * fetches hosts ans re-structures them to support processing of parent-child relationships
     * @return	array									hosts in parent-child relation
     */
    public function getParentChildStructure() {

        $hosts = array();
        $hostReferences = array();

        $idPrefix = sha1(microtime()) . '-';

        $hostView = $this->getContext()->getModel("Views.ApiDQLView","Api",array(
            "view" => "TARGET_STATUSMAP_HOST",
            "connection" => $this->connectionName
        ));
        // fetch all hosts
        $apiResHosts = $hostView->getResult();
     
        $hostWithParents = array();

        $root = array(
              'id'		=> $idPrefix . '-1',
              'name'    => 'Icinga',
              'data'	=> array(
                  'object_id' => 0,
                  'status'	=> '-1',
                  'relation'	=> 'Icinga Monitoring Process',
              ),
              'children'	=> array(),
          );

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
                    'object_id' => $row['HOST_OBJECT_ID']
                ),
                'children'	=> array(),
                'parent' => $row['HOST_PARENT_OBJECT_ID']
            );
            if($row['HOST_PARENT_OBJECT_ID'] != -1) {
                $hostWithParents[$objectId] = $idPrefix.$row['HOST_PARENT_OBJECT_ID'];
            }
        }
        
        // connect childs to parents
        foreach($hostWithParents as $objectId=>$parentId) {
            if(isset($hosts[$parentId]))
               $hosts[$parentId]["children"][] = $hosts[$objectId];
            else // if parent node is missing, connect to top level
                $hosts[$objectId]["parent"] = -1;
        }
        // connect top level nodes to root
        foreach($hosts as $obj) {
            if($obj["parent"] == -1)
                $root["children"][] = $obj;
        }

        return $root;

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
}
