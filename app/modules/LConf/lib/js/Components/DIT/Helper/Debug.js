Ext.ns("LConf.Helper").Debug = (function() {
    var LOGLEVEL = {
        DEBUG:  false,
        INFO:   true,
        ERROR:  true
    }
    
    var log = function(tag,args) {
        
        if(LOGLEVEL[tag] == true)
            AppKit.log.apply(AppKit.log,args);
    }

    return {
        d: function() {log("DEBUG",arguments);},
        i: function() {log("INFO",arguments);},
        e: function() {log("ERROR",arguments);},
        log: log,
        enableLogLevel: function(level) {
            LOGLEVEL[level] = true;
        },
        disableLogLevel: function(level) {
            LOGLEVEL[level] = false;
        }
    }
})();

