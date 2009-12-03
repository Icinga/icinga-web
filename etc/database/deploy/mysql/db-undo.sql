

-- //DELETE FROM changelog WHERE change_number = 4 AND delta_set = 'Main';
-- Fragment ends: 4 --


-- //DELETE FROM changelog WHERE change_number = 3 AND delta_set = 'Main';
-- Fragment ends: 3 --


	TRUNCATE TABLE nsm_user_role;
	TRUNCATE TABLE nsm_role;
	TRUNCATE TABLE nsm_user;

-- //
DELETE FROM changelog WHERE change_number = 2 AND delta_set = 'Main';
-- Fragment ends: 2 --


-- //DELETE FROM changelog WHERE change_number = 1 AND delta_set = 'Main';
-- Fragment ends: 1 --
