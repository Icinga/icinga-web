INSERT INTO `nsm_user` VALUES
(1,0,'root','Root','Enoch','42bc5093863dce8c150387a5bb7e3061cf3ea67d2cf1779671e1b0f435e953a1','0c099ae4627b144f3a7eaa763ba43b10fd5d1caa8738a98f11bb973bebc52ccd','root@localhost.local',0,'2009-02-18 10:12:59','2009-02-18 10:12:59');

INSERT INTO `nsm_role` VALUES
(1,'grapher_user','Grapher usage privileges',0,'2009-02-17 10:17:10','0000-00-00 00:00:00'),(2,'grapher_admin','Grapher admin privileges',0,'2009-02-17 10:17:10','0000-00-00 00:00:00'),(3,'appkit_user','Appkit user',0,'2009-02-19 09:17:37','2009-02-19 09:17:37'),(4,'appkit_admin','AppKit admin',0,'2009-02-17 10:17:10','0000-00-00 00:00:00');

INSERT INTO `nsm_user_role` VALUES (1,4);
