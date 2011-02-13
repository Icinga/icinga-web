CREATE TABLE cronk (cronk_id NUMBER(10), cronk_uid VARCHAR2(45), cronk_name VARCHAR2(45), cronk_description VARCHAR2(100), cronk_xml CLOB, cronk_user_id NUMBER(10), cronk_created DATE NOT NULL, cronk_modified DATE NOT NULL, PRIMARY KEY(cronk_id), CONSTRAINT cronk_uid_UNIQUE UNIQUE (cronk_uid))
/
CREATE TABLE cronk_category (cc_id NUMBER(10), cc_uid VARCHAR2(45) NOT NULL, cc_name VARCHAR2(45), cc_visible NUMBER(3) DEFAULT 0, cc_position NUMBER(10) DEFAULT 0, cc_created DATE NOT NULL, cc_modified DATE NOT NULL, PRIMARY KEY(cc_id), CONSTRAINT cc_uid_UNIQUE UNIQUE (cc_uid))
/
CREATE TABLE cronk_category_cronk (ccc_cc_id NUMBER(10), ccc_cronk_id NUMBER(10), PRIMARY KEY(ccc_cc_id, ccc_cronk_id))
/
CREATE TABLE cronk_principal_cronk (cpc_principal_id NUMBER(10), cpc_cronk_id NUMBER(10), PRIMARY KEY(cpc_principal_id, cpc_cronk_id))
/
CREATE SEQUENCE CRONK_seq START WITH 1 INCREMENT BY 1 NOCACHE
/
CREATE SEQUENCE CRONK_CATEGORY_seq START WITH 1 INCREMENT BY 1 NOCACHE
/
DECLARE
  constraints_Count NUMBER;
BEGIN
  SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count FROM USER_CONSTRAINTS WHERE TABLE_NAME = 'CRONK' AND CONSTRAINT_TYPE = 'P';
  IF constraints_Count = 0 THEN
    EXECUTE IMMEDIATE 'ALTER TABLE CRONK ADD CONSTRAINT CRONK_AI_PK_idx PRIMARY KEY (cronk_id)';
  END IF;
END;
/
ALTER TABLE cronk ADD CONSTRAINT ccnu FOREIGN KEY (cronk_user_id) REFERENCES nsm_user(user_id) NOT DEFERRABLE INITIALLY IMMEDIATE
/
DECLARE
  constraints_Count NUMBER;
BEGIN
  SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count FROM USER_CONSTRAINTS WHERE TABLE_NAME = 'CRONK_CATEGORY' AND CONSTRAINT_TYPE = 'P';
  IF constraints_Count = 0 THEN
    EXECUTE IMMEDIATE 'ALTER TABLE CRONK_CATEGORY ADD CONSTRAINT CRONK_CATEGORY_AI_PK_idx PRIMARY KEY (cc_id)';
  END IF;
END;
/
ALTER TABLE cronk_category_cronk ADD CONSTRAINT cccc_6 FOREIGN KEY (ccc_cronk_id) REFERENCES cronk(cronk_id) NOT DEFERRABLE INITIALLY IMMEDIATE
/
ALTER TABLE cronk_category_cronk ADD CONSTRAINT cccc_5 FOREIGN KEY (ccc_cc_id) REFERENCES cronk_category(cc_id) NOT DEFERRABLE INITIALLY IMMEDIATE
/
ALTER TABLE cronk_principal_cronk ADD CONSTRAINT ccnp FOREIGN KEY (cpc_principal_id) REFERENCES nsm_principal(principal_id) NOT DEFERRABLE INITIALLY IMMEDIATE
/
ALTER TABLE cronk_principal_cronk ADD CONSTRAINT cccc_7 FOREIGN KEY (cpc_cronk_id) REFERENCES cronk(cronk_id) NOT DEFERRABLE INITIALLY IMMEDIATE
/
DECLARE
  constraints_Count NUMBER;
CREATE TRIGGER CRONK_AI_PK
   BEFORE INSERT
   ON CRONK
   FOR EACH ROW
DECLARE
   last_Sequence NUMBER;
   last_InsertID NUMBER;
BEGIN
   IF (:NEW.cronk_id IS NULL OR :NEW.cronk_id = 0) THEN
      SELECT CRONK_seq.NEXTVAL INTO :NEW.cronk_id FROM DUAL;
   ELSE
      SELECT NVL(Last_Number, 0) INTO last_Sequence
        FROM User_Sequences
       WHERE UPPER(Sequence_Name) = UPPER('CRONK_seq');
      SELECT :NEW.cronk_id INTO last_InsertID FROM DUAL;
      WHILE (last_InsertID > last_Sequence) LOOP
         SELECT CRONK_seq.NEXTVAL INTO last_Sequence FROM DUAL;
      END LOOP;
   END IF;
END;
/
CREATE TRIGGER CRONK_CATEGORY_AI_PK
   BEFORE INSERT
   ON CRONK_CATEGORY
   FOR EACH ROW
DECLARE
   last_Sequence NUMBER;
   last_InsertID NUMBER;
BEGIN
   IF (:NEW.cc_id IS NULL OR :NEW.cc_id = 0) THEN
      SELECT CRONK_CATEGORY_seq.NEXTVAL INTO :NEW.cc_id FROM DUAL;
   ELSE
      SELECT NVL(Last_Number, 0) INTO last_Sequence
        FROM User_Sequences
       WHERE UPPER(Sequence_Name) = UPPER('CRONK_CATEGORY_seq');
      SELECT :NEW.cc_id INTO last_InsertID FROM DUAL;
      WHILE (last_InsertID > last_Sequence) LOOP
         SELECT CRONK_CATEGORY_seq.NEXTVAL INTO last_Sequence FROM DUAL;
      END LOOP;
   END IF;
END;
