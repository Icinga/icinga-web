
ALTER TABLE `icinga`.`icinga_logentries` 
ADD INDEX `icinga_web_time` (`instance_id` ASC, `logentry_time` DESC) ;
