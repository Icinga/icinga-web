<?php

class AppKitDoctrinePagerLayout extends Doctrine_Pager_Layout {
	
	public function __construct($pager, $pagerRange, $urlMask) {
		
		parent::__construct($pager, $pagerRange, $urlMask);
		
		$this->setTemplate('<td class="page_links"><a class="pager_link" href="{%url}">{%page}</a></td>');
		$this->setSelectedTemplate('<td class="page_links"><a class="pager_link act" href="{%url}">{%page}</a></td>');
		$this->setSeparatorTemplate('');
		
	}
	
	public function display($options = array(), $return = false) {
        $pager = $this->getPager();
        $str = '';

        if ($pager->getPage() > 1) {
        
	        // First page
	        $this->addMaskReplacement('page', '&laquo;', true);
	        $options['page_number'] = $pager->getFirstPage();
	        $str .= $this->processPage($options);
	
	        // Previous page
	        $this->addMaskReplacement('page', '&lsaquo;', true);
	        $options['page_number'] = $pager->getPreviousPage();
	        $str .= $this->processPage($options);
        
        }
        
        // Pages listing
        $this->removeMaskReplacement('page');
        $str .= parent::display($options, true);

        if ($pager->getPage() < $pager->getLastPage()) {
        
	        // Next page
	        $this->addMaskReplacement('page', '&rsaquo;', true);
	        $options['page_number'] = $pager->getNextPage();
	        $str .= $this->processPage($options);
	
	        // Last page
	        $this->addMaskReplacement('page', '&raquo;', true);
	        $options['page_number'] = $pager->getLastPage();
	        $str .= $this->processPage($options);

        }
        
        // Adding a frame arround
        if ($str) {
        	$info = sprintf('<td class="pager_info">(page %d of %d, %d entries found)</td>', $pager->getPage(), $pager->getLastPage(), $pager->getNumResults());
        	$str = sprintf('<div class="pager_frame"><table class="pager_frame"><tr>%s%s</tr></table></div>', $str, $info);
        }
        
        // Possible wish to return value instead of print it on screen
        if ($return) {
            return $str;
        }

        echo $str;
    }
	
}

?>