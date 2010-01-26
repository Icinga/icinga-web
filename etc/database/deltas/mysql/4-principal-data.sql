-- //

INSERT INTO `nsm_principal` VALUES 
	(1,1,NULL,'user',0),
	(2,NULL,3,'role',0),
	(3,NULL,4,'role',0),
	(4,NULL,5,'role',0);

INSERT INTO `nsm_target` VALUES 
	(1,'IcingaHostgroup','Limit data access to specific hostgroups','IcingaDataHostgroupPrincipalTarget','icinga'),
	(2,'IcingaServicegroup','Limit data access to specific servicegroups','IcingaDataServicegroupPrincipalTarget','icinga'),
	(3,'IcingaHostCustomVariablePair','Limit data access to specific custom variables','IcingaDataHostCustomVariablePrincipalTarget','icinga'),
	(4,'IcingaServiceCustomVariablePair','Limit data access to specific custom variables','IcingaDataServiceCustomVariablePrincipalTarget','icinga'),
	(5,'IcingaContactgroup','Limit data access to users contact group membership','IcingaDataContactgroupPrincipalTarget','icinga'),
	(6,'IcingaCommandRo','Limit access to commands','IcingaDataCommandRoPrincipalTarget','icinga');

-- //@UNDO

-- //
