<script type="text/javascript">

/*
 * We want to use the global search with our Cronks object search window
 * Just registering the handler ...
 */
(function() {
    var s = Icinga.Cronks.search.SearchHandler;
    s.setProxyUrl("<?php echo $ro->gen('modules.cronks.objectsearch.json')?>");
    s.setMinimumChars(<?php echo (int)AgaviConfig::get('modules.cronks.search.numberMinimumLetters', 2); ?>);
    s.register();
})();

Cronk.util.initEnvironment('viewport-center', function() {
    
    var portal = new Icinga.Cronks.System.CronkPortal({
       loadingMask : parseInt(<?php echo (int)AgaviConfig::get('modules.cronks.portal.loadmasktimeout', '1000'); ?>),
       customCronkCredential: <?php echo json_encode((boolean)$us->hasCredential('icinga.cronk.custom')); ?>
    });
    
    AppKit.util.Layout.addTo(portal);
    
    if(<?php echo $rd->getParameter("isURLView") ? 1 : 0 ?>) {
        Ext.getCmp('cronk-tabs').setURLTab(<?php echo $rd->getParameter('URLData');?>);
    }
    
    AppKit.util.Layout.doLayout();
        
}, { run: true, extready: true });

</script>