ALTER TABLE NSM_USER MODIFY user_email VARCHAR(254);
CREATE UNIQUE INDEX user_email_unique on NSM_USER(user_email);