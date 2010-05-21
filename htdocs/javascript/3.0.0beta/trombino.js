var tol_busy = false;
var tol_late = false;

$(document).ready(function(){
	$('#tol_searcher input').keyup(function() {
		search();
	});
});


function search()
{
	if (tol_busy) {
		tol_late = true;
	} else {
		tol_busy = true;

	    request('tol/ajax', $('#tol_searcher form').formToJSON(), {"success":
	    	function(json) {

	    		// On retire les résultats ayant disparus
	    		$.each($("#tol_results > li"), function(index, value) {
	    			if (!key_exists($(value).attr("uid"), json.results))
	    				$(value).remove();
	    		});

	    		// On ajoute les éventuels nouveaux résultats
		    	for (var uid in json.results)
		    	{
		    		if ($("#tol_results > li[uid='" + uid + "']").length == 0)
		    			$('#tol_results').append(json.results[uid]);
		    	}

		    	tol_busy = false;
		    	if (tol_late) {
		    		tol_late = false;
		    		search();
		    	}
	    	}});
	}
}