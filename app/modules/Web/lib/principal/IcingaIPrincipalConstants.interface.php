<?php

/**
 * Possible security / credential / principal target
 * for enhance the standard icinga object<->client model
 * @author mhein
 *
 */
interface IcingaIPrincipalConstants {
    /**
     * Views based on hostgroup membership
     * @var string
     */
    const TYPE_HOSTGROUP = 'IcingaHostgroup';
    
    /**
     * Security layer for hosts based on customvars
     * @var string
     */
    const TYPE_CUSTOMVAR_HOST = 'IcingaHostCustomVariablePair';
    
    /**
     * Restrict service based views with customvars
     * @var string
     */
    const TYPE_CUSTOMVAR_SERVICE = 'IcingaServiceCustomVariablePair';
    
    /**
     * Restriction for services based on servicegroups
     * @var string
     */
    const TYPE_SERVICEGROUP = 'IcingaServicegroup';
    
    /**
     * Native Icinga/Nagios(tm) security model. Show belongings
     * based on contact/contactgroup membership
     * @var string
     */
    const TYPE_CONTACTGROUP = 'IcingaContactgroup';
}