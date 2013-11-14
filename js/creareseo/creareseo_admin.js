document.observe("dom:loaded", function() {
	$('creareseo-check').observe('click', function(event) {
		new Ajax.Request(BASE_URL+'creareseo/test/', {
		  method:'get',
		  onSuccess: function(transport) {
		    var response = transport.responseText || "no response text";
		    $('creareseo-check-results').insert(response);
		  },
		  onFailure: function() { alert('Something went wrong...'); }
		});
		
	});
});