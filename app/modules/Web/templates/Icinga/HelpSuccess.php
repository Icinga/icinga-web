<script type="text/javascript">
	Ext.onReady(function () {
		var lTitle = _("We're Icinga");
		AppKit.util.Dom.makeImage('icinga-image-home', 'icinga.icinga-logo', { alt: lTitle , style: 'width: 200px' });
		AppKit.util.Dom.makeImage('icinga-image-default', 'icinga.idot-small',	{ alt: lTitle });
		AppKit.util.Dom.makeImage('icinga-image-dev', 'icinga.idot-small', { alt: lTitle });
		AppKit.util.Dom.makeImage('icinga-image-docs', 'icinga.idot-small', { alt: lTitle });
	});
</script>
<div style="width: 200px; margin: 0 auto;">
	<a id="icinga-image-home" href="http://www.icinga.org/"></a>
</div>

<h2 style="text-align: center;">
	Version <?php echo AgaviConfig::get('org.icinga.version.release'); ?>
</h2>

<h3 style="text-align: center;">
	<?php echo $tm->_d(AgaviConfig::get('org.icinga.version.releasedate')); ?>
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
	<a id="icinga-image-default" title="<?php echo $tm->_('Icinga'); ?>" href="http://www.icinga.org/"></a>
	<a id="icinga-image-dev" title="<?php echo $tm->_('Dev'); ?>" href="http://dev.icinga.org/"></a>
	<a id="icinga-image-docs" title="<?php echo $tm->_('Docs'); ?>" href="http://docs.icinga.org/"></a> 
</p>