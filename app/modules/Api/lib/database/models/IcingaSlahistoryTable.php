<?php

/**
 * IcingaConfigfiles
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class IcingaSlahistoryTable extends Doctrine_Table {
    
    
    /**
     * @param Api_SLA_SLAFilterModel $filter
     * @param type $prefix
     * @param type $date
     * @return type 
     */
    private static function buildWherePart(Doctrine_Connection $conn,Api_SLA_SLAFilterModel $filter) {
        $queryBuilder = AgaviContext::getInstance()->getModel("SLA.SLAQueryBuilder","Api");
        
        return $queryBuilder->getWherePart($conn,$filter);
    }
    
    /**
     * Returns the SLA Summary (@see IcingaSLASummary) for this filter
     * 
     * @param Doctrine_Connection $dbConnection
     * @param type $filter
     * @return Doctrine_Collection 
     */
    public static function getSummary(Doctrine_Connection $dbConnection =null, $filter = null) {
        if($dbConnection == null)
            $dbConnection = AgaviContext::getInstance()->getDatabaseConnection("icinga");
        
        $driver = $dbConnection->getDriverName();
        $stmt = null;
        switch(strtolower($driver)) {
            case 'pgsql':
                $stmt = self::getPgsqlSummaryQuery($dbConnection, $filter);
                break;
            case 'oracle':
            case 'icingaoracle':
                $stmt = self::getOracleSummaryQuery($dbConnection,$filter);
                break;
            case 'mysql':
                $stmt = self::getMySQLSummaryQuery($dbConnection,$filter);
                break;
            default:
                throw new AppKitDoctrineException("Invalid driver ".$driver);
        }
        $stmt->execute();
        $result = new Doctrine_Collection("IcingaSLASummary");
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $record = new IcingaSLASummary();
            foreach($row as $key=>$value) {
                $record->{strtolower($key)} = $value;
            }
            $result->add($record);
        }
        
        return $result;        
    }

    private static function getTimeRangeQuery(Doctrine_Connection $c, $filter) {
        $prefix = $c->getPrefix();
        $startTime = $filter->getStartTime();
        $endTime = $filter->getEndTime();
        $DATE_FORMAT;
        $DATE;
        $NOW;
        switch(strtolower($c->getDriverName())) {
            
            case 'pgsql':
                $DATE_FORMAT = "'YYYY-MM-DD HH24:MI:SS'";
                $DATE = "TO_DATE";
                $NOW = "NOW()";
                break;
            case 'oracle':
            case 'icingaOracle':
                $DATE_FORMAT = "'YYYY-MM-DD HH24:MI:SS'";
                $DATE = "TO_DATE";
                $NOW = "CURRENT_DATE";
                break;
            case 'mysql':
                $DATE_FORMAT = "'%Y-%m-%d %H:%i:%s'";
                $DATE = "STR_TO_DATE";
                $NOW = "now()";
                break;
                
        }
        if(!$startTime && !$endTime)
            return "SELECT * FROM ".$prefix."slahistory";
        $timeRange = "SELECT state, object_id, scheduled_downtime,acknowledgement_time";
        if(!$startTime)
            $timeRange .= ",start_time";
        else 
            $timeRange .= "
                ,CASE 
                   WHEN start_time <= $DATE('$startTime',$DATE_FORMAT) AND
                        COALESCE(acknowledgement_time, end_time, $NOW) >= $DATE('$startTime',$DATE_FORMAT) THEN $DATE('$startTime',$DATE_FORMAT)
                   ELSE start_time 
                END AS start_time";

        if(!$endTime)
            $timeRange .= ",COALESCE(end_time,$NOW) as end_time";
        else 
            $timeRange .= "
                ,CASE 
                   WHEN COALESCE(acknowledgement_time, end_time, $NOW) >= $DATE('$endTime',$DATE_FORMAT) AND
                        start_time <= $DATE('$endTime',$DATE_FORMAT) THEN $DATE('$endTime',$DATE_FORMAT)
                   ELSE COALESCE(end_time,$NOW); 
                END AS end_time";
        $query = $timeRange." FROM ".$prefix."slahistory ";
        if(!($endTime || $startTime)) 
            return $query;
        $query .= "WHERE ";
        if($endTime)
            $query .= "end_time <= $DATE('$endTime',$DATE_FORMAT) ";
        if($endTime && $startTime)
            $query .= " AND ";
        if($startTime)
            $query .= "start_time >= $DATE('$startTime',$DATE_FORMAT) ";
        return $query;
        
            
    }
    
    
    private static function getPgsqlSummaryQuery(Doctrine_Connection $c, $filter = null) {
        $dbh = $c->getDbh();
        $prefix = $c->getPrefix();
        $mainQuery = "SELECT 
                (state*(scheduled_downtime-1)*-1) as sla_state, 
                s.object_id, 
                obj.objecttype_id,
                scheduled_downtime, 
                SUM(date_part('epoch',
                    COALESCE(
                        acknowledgement_time-start_time,
                        end_time-start_time,
                        NOW()-start_time
                    )
                 )) AS duration FROM timeRange s INNER JOIN ".$prefix.
                "objects obj ON obj.object_id = s.object_id ";
        
        if($filter) {
            $filterParts = self::buildWherePart($c,$filter);
        
            $mainQuery .= $filterParts["wherePart"];
        }
        $mainQuery .= " GROUP BY 
                 (state*(scheduled_downtime-1)*-1), 
                 s.object_id, 
                 obj.objecttype_id,
                 scheduled_downtime";
        
        $timeRangeQuery = self::getTimeRangeQuery($c,$filter);
        $stmt = $dbh->prepare($query = "
            WITH 
                timeRange AS (".$timeRangeQuery."),
                slatable as (".$mainQuery." ),
            completeDuration AS (
                 SELECT object_id,
                    SUM(date_part('epoch',
                        COALESCE(
                            acknowledgement_time-start_time,
                            end_time-start_time,
                            NOW()-start_time
                        ))
                    ) AS complete 
                  FROM timeRange
                  GROUP BY object_id
            ) 
            SELECT 
                s.object_id AS OBJECT_ID, 
                s.sla_state AS SLA_STATE, 
                s.objecttype_id AS OBJECTTYPE_ID,
                SUM(s.duration/c.complete*100) as PERCENTAGE
            FROM slatable s 
            INNER JOIN 
                completeDuration c 
                ON 
                    c.object_id = s.object_id          
            GROUP BY s.object_id, s.sla_state, s.objecttype_id
        ");
        foreach($filterParts["params"] as $param=>$value)
            $stmt->bindValue($param,$value);
        return $stmt;
        
    }
    
    private static function getOracleSummaryQuery(Doctrine_Connection $c, $filter = null) {    
        $dbh = $c->getDbh();
        $prefix = $c->getPrefix();
        $mainQuery = "SELECT 
                (state*(scheduled_downtime-1)*-1) as sla_state, 
                s.object_id,
                obj.objecttype_id,
                scheduled_downtime, 
                SUM(
                    COALESCE(
                        acknowledgement_time-start_time,
                        end_time-start_time,
                        CURRENT_DATE-start_time
                    )
                 )*86400 AS duration FROM timeRange s INNER JOIN ".$prefix.
                "objects obj ON obj.id = s.object_id ";
        
        if($filter) {
            $filterParts = self::buildWherePart($c,$filter);
            $mainQuery .= $filterParts["wherePart"];
        }
        $mainQuery .= " GROUP BY 
                 (state*(scheduled_downtime-1)*-1), 
                 s.object_id, 
                 obj.objecttype_id,
                 scheduled_downtime";
        
        $timeRangeQuery = self::getTimeRangeQuery($c,$filter);
        $stmt = $dbh->prepareBase($query = "WITH 
                timeRange AS (".$timeRangeQuery."),
                slatable as (".$mainQuery." ),
            completeDuration AS (
                 SELECT object_id,
                    SUM(
                        COALESCE(
                            acknowledgement_time-start_time,
                            end_time-start_time,
                            CURRENT_DATE-start_time
                        )
                    )*86400 AS complete 
                  FROM timeRange
                  GROUP BY object_id
            ) 
            SELECT 
                s.object_id AS OBJECT_ID, 
                s.sla_state AS SLA_STATE,
                s.objecttype_id AS OBJECTTYPE_ID,
                SUM(s.duration/c.complete*100) as PERCENTAGE
            FROM slatable s 
            INNER JOIN 
                completeDuration c 
                ON 
                    c.object_id = s.object_id          
            GROUP BY s.object_id, s.sla_state, s.objecttype_id
        ");
      
        
        foreach($filterParts["params"] as $param=>$value)
            $stmt->bindValue($param,$value);
        return $stmt;
    }
    
    private static function getMySQLSummaryQuery(Doctrine_Connection $c, $filter = null) {   
        $dbh = $c->getDbh();
        $prefix = $c->getPrefix();
        // MYSQL has no with clause, so we need two queries
        $mainQuery = "(SELECT 
                s.object_id,
                state*(scheduled_downtime-1)*-1 as sla_state,
                obj.objecttype_id,
                SUM(
                    COALESCE(
                        UNIX_TIMESTAMP(acknowledgement_time)-UNIX_TIMESTAMP(start_time),
                        UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(start_time),
                        UNIX_TIMESTAMP(now())-UNIX_TIMESTAMP(start_time)
                    )
                ) as duration          
             FROM  timerange  s
             INNER JOIN ".$prefix."objects obj ON obj.object_id = s.object_id ";
        
        if($filter) {
            $filterParts = self::buildWherePart($c,$filter);
            $mainQuery .= $filterParts["wherePart"];
        }
        
        $mainQuery .= "
            
             GROUP BY 
                 state*(scheduled_downtime-1)*-1,
                 object_id,
                 objecttype_id
             ) slahistory_main";
        
        $result = $dbh->query($query = "

            START TRANSACTION;
            DROP VIEW IF EXISTS timerange;
            CREATE ALGORITHM=MERGE VIEW timerange AS
                ".self::getTimeRangeQuery($c,$filter).";");

        if($result == false) {
            $err = $dbh->errorInfo();
            throw new PDOException($err[0],$err[1],$err[2]);
        }
         
       
        $stmt = $dbh->prepare($query = "
            
            SELECT 
                slahistory_main.sla_state AS SLA_STATE, 
                slahistory_main.object_id AS OBJECT_ID, 
                slahistory_main.objecttype_id AS OBJECTTYPE_ID,
                SUM(slahistory_main.duration/s.complete*100) AS PERCENTAGE
            FROM
                 $mainQuery 
                 INNER JOIN (
                     SELECT object_id,
                     SUM(
                         COALESCE(
                             UNIX_TIMESTAMP(acknowledgement_time)-UNIX_TIMESTAMP(start_time),
                             UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(start_time),
                             UNIX_TIMESTAMP(now())-UNIX_TIMESTAMP(start_time)
                         )         
                    ) as complete FROM timerange
                    GROUP BY 
                        object_id     
                 ) as s ON slahistory_main.object_id = s.object_id  
             GROUP BY 
                slahistory_main.object_id,
                slahistory_main.sla_state,
                slahistory_main.objecttype_id;
             DROP VIEW timerange;
         ");
       
        foreach($filterParts["params"] as $param=>$value)
            $stmt->bindValue($param,$value);
        
        return $stmt;
       
    }
}