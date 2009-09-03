/*
 * AppKit javascript start script
 */

/**
 * AppKit Ext helper
 */
AppKit.Ext = function() {
	var cMessage = null;
	
	/**
	 * Internal method to create popup bpxes
	 */
	function createPopupBox(title, text) {
		return ['<div class="ext-msg-message">',
                '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', title, '</h3>', text, '</div></div></div>',
                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
                '</div>'].join('');
	}
	
	/**
	 * Display javascript messages on the message stack 
	 */
	return {
		Message: function(title, format) {
			if (!cMessage) {
				cMessage = Ext.DomHelper.insertFirst(document.body, {id:'ext-msg-stack'}, true);
			}
			
			cMessage.alignTo(document, 'tr-tr', [-18, 0]);
			var string = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
			
			var ele = Ext.DomHelper.append(cMessage, {html: createPopupBox(title, string)}, true);
			ele.slideIn('t').pause(2).ghost("t", {remove:true});
		}
	}
}();

/*
 * Test data
 */
AppKit.Ext.bogusMarkup = [
'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam',
'nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,', 
'sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.', 
' Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor', 
'sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam',
'nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed',
'diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.',
'Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.'
].join(' ');

/**
 * ini extjs 
 */
Ext.onReady(function(){
	Ext.BLANK_IMAGE_URL = '/images/ajax/s.gif';
	
	Ext.QuickTips.init();
});