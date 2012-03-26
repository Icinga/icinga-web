<div class="lconf-about">
	<div class="logo"></div>
	<?php 
		echo $tm->_('<h2>LConf management tool for icinga.</h2>').'<br/>';
		echo $tm->_('<b>Version ').AgaviConfig::get('modules.lconf.version')."</b><br/>";
	?>
	Copyright &copy; Netways 2012<br/>
	<?php 
		echo $tm->_('<p>LConf for Icinga is licensed under the GNU General Public License and is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING THE WARRANTY OF DESIGN, MERCHANTABILITY, AND FITNESS FOR A PARTICULAR PURPOSE.<br/>
				All other trademarks are the property of their respective owners.</p> 
				<br/>');
	?>
	<?php 
		echo $tm->_('For issues, questions or feedback, please visit ').'<a href="http://www.netways.org">www.netways.org</a><br/>';
	?>	
</div>