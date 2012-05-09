<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


/**
 * Util class for working with doctrine records
 * @author mhein
 *
 */
class AppKitDoctrineUtil {

    /**
     * Updates an doctrine record
     * @param Doctrine_Record $record
     * @param array $argsArray
     * @param array $attribArray
     * @return boolean
     * @throws AppKitDoctrineUtilException
     * @author Marius Hein
     */
    public static function updateRecordsetFromArray(Doctrine_Record &$record, array $argsArray, array $attribArray) {
        foreach($attribArray as $attribute) {

            if (array_key_exists($attribute, $argsArray)) {
                if ($record->getTable()->hasColumn($attribute)) {
                    // Clean update
                    $record-> { $attribute } = $argsArray[$attribute];
                } else {
                    // Wrong attribute definition, throw something!
                    throw new AppKitDoctrineUtilException("Field $attribute is not available on ". get_class($record));
                }
            }
        }

        return true;
    }

    /**
     * Automatic 'disabled' field handling
     * @param Doctrine_Record $record
     * @param string $field
     * @throws AppKitDoctrineUtilException
     */
    public static function toggleRecordValue(Doctrine_Record &$record, $field=null) {
        // Try to autodetect the fieldname
        if ($field === null) {
            foreach($record->getTable()->getColumns() as $name=>$info) {
                if (preg_match('@_disabled$@', $name) && in_array($info['type'], array('boolean', 'integer')) == true) {
                    $field = $name;
                }
            }
        }

        if ($field && $record->getTable()->hasColumn($field)) {
            $record-> { $field } = !$record-> { $field };
        } else {
            throw new AppKitDoctrineUtilException("Field does not exist on the record (tableobject) ");
        }

    }

    /**
     * Shortcut for Doctrine_Table::findAll but with sorting and flagselection
     * @param string $component_name
     * @param string $where
     * @param string $orderby
     * @return Doctrine_Collection
     * @author Marius Hein
     */
    public static function fastTableCollection($component_name, $where=null, $orderby=null) {
        $query = Doctrine_Query::create()
                 ->from($component_name);

        if ($order) {
            $query->orderBy($orderby);
        }

        if ($where) {
            $query->andWhere($where);
        }

        return $query->execute();
    }
    
    /**
     * @return Doctrine_Connection
     */
    public static function getConnection() {
        static $connection = null;
        
        if ($connection === null) {
            $connection = Doctrine_Manager::getInstance()->getConnection(AppKitIConstants::DEFAULT_CONNECTION);
        }
        
        return $connection;
    }
    
    /**
     * @return Doctrine_Query
     */
    public static function createQuery() {
        return Doctrine_Query::create(self::getConnection());
    }

}

class AppKitDoctrineUtilException extends AppKitDoctrineException {}