ALTER TABLE nsm_user ALTER COLUMN user_email TYPE varchar(254);
CREATE UNIQUE INDEX user_email_unique ON nsm_user (user_email);