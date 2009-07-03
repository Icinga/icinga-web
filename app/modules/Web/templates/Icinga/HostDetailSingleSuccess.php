<?php 
	$host =& $t['host'];
?>

<p>
<?php echo AppKitHtmlHelper::Obj()->LinkToRoute('icinga.hostDetail', $tm->_('Click here to go back')); ?>
</p>

<?php if ($host) { ?>

<script type="text/javascript">
<!-- // <![CDATA[
	YAHOO.util.Event.onContentReady("host_tabs", function () { 
		var groupTabs = new YAHOO.widget.TabView('host_tabs');
	});
// ]]> -->
</script>

<div id="host_tabs" class="yui-navset">

<ul class="yui-nav">
	<li class="selected"><a href="#host_data"><em>Basic</em></a></li>
	<li><a href="#host_comments"><em>Comments</em></a></li>
	<li><a href="#host_commands"><em>Commands</em></a></li>
</ul>


<div class="yui-content">
	<div id="host_data">
	
	<h4>Basic device data</h4>
	
	<table class="icinga-attribute-table">
	
	<tr>
		<td class="key"><?php echo $tm->_('Host status'); ?>:</td>
		<td class="val">
			<?php echo IcingaHostStateInfo::Create($host->host_current_state)->getCurrentStateAsHtml(); ?>
			<p>(<?php echo $tm->_('for');?>&#160;<?php echo AppKitDateUtil::durationToString( AppKitDateUtil::dateToDuration( $host->host_last_state_change ) ); ?>)</p>
		</td>
	</tr>
	
	<tr>
		<td class="key"><?php echo $tm->_('Status information'); ?>:</td>
		<td class="val"><?php echo $host->host_output; ?></td>
	</tr>
	
	<tr>
		<td class="key"><?php echo $tm->_('Performance data'); ?>:</td>
		<td class="val"><?php echo $host->host_perfdata ? $host->host_perfdata : $tm->_('(null)'); ?></td>
	</tr>
	
	<tr>
		<td class="key"><?php echo $tm->_('Current attempt'); ?>:</td>
		<td class="val">
			<?php echo $host->host_current_check_attempt; ?>/<?php echo $host->host_max_check_attempts; ?>
			(<?php echo $tm->_( IcingaConstantResolver::stateType($host->host_state_type) ); ?> <?php echo $tm->_('state'); ?>)
		</td>
	</tr>
	
	<tr>
		<td class="key"><?php echo $tm->_('Last check time'); ?>:</td>
		<td class="val"><?php echo $tm->_d( $host->host_last_check ); ?></td>
	</tr>
	
	<tr>
		<td class="key"><?php echo $tm->_('Check type'); ?>:</td>
		<td class="val"><?php echo $tm->_( IcingaConstantResolver::activeCheckType($host->host_is_active) ); ?></td>
	</tr>
	
	<tr>
		<td class="key"><?php echo $tm->_('Check latency / duration'); ?>:</td>
		<td class="val">
			<?php echo $host->host_avg_latency; ?> / <?php echo $host->host_avg_execution_time; ?> 
			<?php echo $tm->_('seconds'); ?>
		</td>
	</tr>
	
	<tr>
		<td class="key"><?php echo $tm->_('Next scheduled active check'); ?>:</td>
		<td class="val"><?php echo $tm->_d( $host->host_next_check ); ?></td>
	</tr>
	
	<tr>
		<td class="key"><?php echo $tm->_('Last state change'); ?>:</td>
		<td class="val"><?php echo $tm->_d( $host->host_last_state_change ); ?></td>
	</tr>
	
	<tr>
		<td class="key"><?php echo $tm->_('Last notification'); ?>:</td>
		<td class="val"><?php echo $tm->_d( $host->host_last_notification ); ?></td>
	</tr>
	
	<tr>
		<td class="key"><?php echo $tm->_('Is the host flapping'); ?>:</td>
		<td class="val"><?php echo $tm->_( IcingaConstantResolver::booleanNames( $host->host_is_flapping ) ); ?></td>
	</tr>
	
	<tr>
		<td class="key"><?php echo $tm->_('In scheduled downtime'); ?>:</td>
		<td class="val"><?php echo $tm->_( IcingaConstantResolver::booleanNames( $host->host_scheduled_downtime_depth ) ); ?></td>
	</tr>
	
	<tr>
		<td class="key"><?php echo $tm->_('Last update'); ?>:</td>
		<td class="val">
			<?php echo $tm->_d( $host->host_status_update_time );?>
			(<?php echo AppKitDateUtil::durationToString( AppKitDateUtil::dateToDuration( $host->host_status_update_time ) ) ?>)
		</td>
	</tr>
	
	</table>
	</div>
	
	<div id="host_comments">
		Comments
	</div>
	
	<div id="host_commands">
		Commands
	</div>
	
</div>
</div>

<?php } else { ?>

<?php echo $tm->_('Sorry, no details for host <strong>%1$s</strong> found!', null, null, array($rd->getParameter('hostname'))); ?>

<?php } ?>