<?php

class AppKit_ServertimeAction extends AppKitBaseAction
{
	
	public function getDefaultViewName()
	{
		return 'Success';
	}
    
    public function isSimple() {
        return true;
    }
}

?>