<?php 
	$service =& $t['service'];
?>
<p>
<?php echo AppKitHtmlHelper::Obj()->LinkToRoute('icinga.serviceDetail', $tm->_('Click here to go back')); ?>
</p>

<?php if ($service) { ?>

<script type="text/javascript">
<!-- // <![CDATA[
	YAHOO.util.Event.onContentReady("service_tabs", function () { 
		var serviceTabs = new YAHOO.widget.TabView('service_tabs');
	});
// ]]> -->
</script>

<div id=service_tabs class="yui-navset">

<ul class="yui-nav">
	<li class="selected"><a href="#service_data"><em>Basic</em></a></li>
	<li><a href="#service_comments"><em>Comments</em></a></li>
	<li><a href="#service_commands"><em>Commands</em></a></li>
</ul>

<div class="yui-content">
	<div id="service_data">
	
		<h4>Basic service data</h4>
	
		<table class="icinga-attribute-table">
	
			<tr>
				<td class="key"><?php echo $tm->_('Current status'); ?>:</td>
				<td class="val"><?php echo IcingaServiceStateInfo::Create($service->service_current_state)->getCurrentStateAsHtml(); ?></td>
			</tr>
			
			<tr>
				<td class="key"><?php echo $tm->_('Status information'); ?>:</td>
				<td class="val"><?php echo $service->service_output; ?></td>
			</tr>
			
			<tr>
				<td class="key"><?php echo $tm->_('Performance data'); ?>:</td>
				<td class="val"><?php echo $service->service_perfdata ? $service->service_perfdata : $tm->_('(null)'); ?></td>
			</tr>
			
			<tr>
				<td class="key"><?php echo $tm->_('Current attempt'); ?>:</td>
				<td class="val">
				<?php echo $service->service_current_check_attempt; ?>/<?php echo $service->service_max_check_attempts; ?>
				(<?php echo $tm->_( IcingaConstantResolver::stateType($service->service_state_type) ); ?> <?php echo $tm->_('state'); ?>)
				</td>
			</tr>
			
			<tr>
				<td class="key"><?php echo $tm->_('Last check time'); ?>:</td>
				<td class="val"><?php echo $tm->_d( $service->service_last_check ); ?></td>
			</tr>
			
			<tr>
				<td class="key"><?php echo $tm->_('Check type'); ?>:</td>
				<td class="val"><?php echo $tm->_( IcingaConstantResolver::activeCheckType($service->service_is_active) ); ?></td>
			</tr>
			
			<tr>
				<td class="key"><?php echo $tm->_('Check latency / duration'); ?>:</td>
				<td class="val">
				<?php echo $service->service_avg_latency; ?> / <?php echo $service->service_avg_execution_time; ?> 
				<?php echo $tm->_('seconds'); ?>
				</td>
			</tr>
			
			<tr>
				<td class="key"><?php echo $tm->_('Next scheduled check'); ?>:</td>
				<td class="val"><?php echo $tm->_d( $service->service_next_check ); ?></td>
			</tr>
			
			<tr>
				<td class="key"><?php echo $tm->_('Laste state change'); ?>:</td>
				<td class="val"><?php echo $tm->_d( $service->service_last_state_change ); ?></td>
			</tr>
			
			<tr>
				<td class="key"><?php echo $tm->_('Last notification'); ?>:</td>
				<td class="val"><?php echo $tm->_d( $service->service_last_notification ); ?></td>
			</tr>
			
			<tr>
				<td class="key"><?php echo $tm->_('Is this service flapping'); ?>:</td>
				<td class="val"><?php echo $tm->_( IcingaConstantResolver::booleanNames( $service->service_is_flapping ) ); ?></td>
			</tr>
			
			<tr>
				<td class="key"><?php echo $tm->_('In scheduled downtime'); ?>:</td>
				<td class="val"><?php echo $tm->_( IcingaConstantResolver::booleanNames( $service->service_scheduled_downtime_depth ) ); ?></td>
			</tr>
			
			<tr>
				<td class="key"><?php echo $tm->_('Last update'); ?>:</td>
				<td class="val">
					<?php echo $tm->_d( $service->service_status_update_time );?>
					(<?php echo AppKitDateUtil::durationToString( AppKitDateUtil::dateToDuration( $service->service_status_update_time ) ) ?>)
				</td>
			</tr>
	
		</table>
	</div>
	
	<div id="service_comments">
	
	</div>
	
	<div id="service_commands">
	
	</div>
	
</div>
</div>

<?php } ?>