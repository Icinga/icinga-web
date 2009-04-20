/**
 * What a preloader :-) Just to reduce the massive script tags
 * within the header ....
 */
function JSLoader() {
	
	// private one ?!
	var files = {};
	
	this.addFile = function(file) {
		files[file] = file;
		return true;
	};
	
	this.bulkInclude = function() {
		
		var head = document.getElementsByTagName("head")[0];
		
		for(var file in files) {
			var script =  document.createElement("script");
			script.setAttribute("type", "text/javascript");
			script.setAttribute("src", file);
			head.appendChild(script);
		}
	};
	
}