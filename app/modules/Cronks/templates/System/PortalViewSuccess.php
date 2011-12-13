<script type="text/javascript">
Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {
	
	var portalView = new Icinga.Cronks.System.PortalView({
		defaultColumns : this.getParameter('columns', 1),
		stateful : true,
		stateId : this.cmpid
	});
	
	this.getParent().removeAll();
	
	if (Ext.isDefined(this.state)) {
		portalView.applyState(this.state);
	}
	
	this.add(portalView);
	
	this.doLayout();
	
});
</script>
