document.observe("dom:loaded", function() {
	$('creareseo-check').observe('click', function(event) {
		alert('test');
	});
});