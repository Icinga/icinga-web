<?php 
	$img_icinga = (string)AppKitHtmlHelper::getInstance()->Image('icinga.icinga-logo', 'icinga', array('style' => 'width: 200px'));
	$img_idot = (string)AppKitHtmlHelper::getInstance()->Image('icinga.idot-small');
?>
<div style="width: 200px; margin: 0 auto;">
	<a href="http://www.icinga.org/">
		<?php echo $img_icinga; ?>
	</a>
</div>

<h2 style="text-align: center;">
	Version <?php echo AgaviConfig::get('org.icinga.appkit.version.release'); ?>
</h2>

<h3 style="text-align: center;">
	<?php echo $tm->_d(AgaviConfig::get('org.icinga.appkit.version.releasedate')); ?>
</h3>

<p style="text-align: center;" class="legal">
	Copyright &copy; 2009,2010 Icinga Development Team.<br /><br />
	Portions copyright by Nagios/Icinga community members - see the THANKS file for more information.
</p>

<p style="text-align: center;" class="legal">
	Icinga is licensed under the GNU General Public License and is provided
	AS IS 	with NO WARRANTY OF ANY KIND, INCLUDING THE WARRANTY OF DESIGN,
	MERCHANTABILITY, AND FITNESS FOR A PARTICULAR PURPOSE.<br /><br />
	All other trademarks are the property of their respective owners.
</p>

<p style="text-align: center; margin: 20px auto;">
	<a title="<?php echo $tm->_('Icinga'); ?>" href="http://www.icinga.org/"><?php echo $img_idot; ?></a>
	<a title="<?php echo $tm->_('Dev'); ?>" href="http://dev.icinga.org/"><?php echo $img_idot; ?></a>
	<a title="<?php echo $tm->_('Docs'); ?>" href="http://docs.icinga.org/"><?php echo $img_idot; ?></a> 
</p>