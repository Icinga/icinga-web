/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.state.SessionProvider = Ext.extend(Ext.state.CookieProvider, {
    readCookies : function() {
        if(this.state){
        	
//        	console.log("--- READ STATE START ---");
        	
//        	console.log(this.state);
        	
            for(var k in this.state){
                if(typeof this.state[k] == 'string'){
//                	console.log(this.state[k])
                    this.state[k] = this.decodeValue(this.state[k]);
                }
            }
            
//            console.log("--- READ STATE STOP ---");
            
        }
        
        
        return Ext.apply(this.state || {}, Ext.state.SessionProvider.superclass.readCookies.call(this));
    },

	encodeValue : function(v) {
		return Ext.util.JSON.encode(v);
	},
	
	decodeValue : function(v) {
		return Ext.util.JSON.decode(v);
	}

});