$(function() {
	/**
	 * Open all links in a new window
	 */
    $("a[href*='http://']:not([href*='"+location.hostname+"'])").click( function() {
    	window.open(this.href);
    	return false;
   	});
	/**
	 * Convert internal links into commands
	 */
    $("a[href*='http://"+location.hostname+"']").click( function() {
    	//window.open(this.href);
    	$hashValue = this.href;
		// replace "/" by "&" in order to handle /varname=value/ and &varname=value&
		$hashValueWithAmperstand = $hashValue.replace(new RegExp("/", "g"),"&");
//		$hashValueWithAmperstand = $hashValueWithAmperstand.replace(new RegExp("?", "g"),"&");

		// split and extract key/value pairs
		var $vars_array = $hashValueWithAmperstand.split("&");
		for ($i = 0; $i < $vars_array.length; $i++)
		{
			//alert("setFlashVarsForSilexPage Start loop "+$vars_array[$i]);
			var $varsPair_array = $vars_array[$i].split("=");
			if ($varsPair_array && $varsPair_array[0]!=undefined && $varsPair_array[1]!=undefined){
				switch($varsPair_array[0]){
					case "p":
					case "?p":
						parent.openPost($varsPair_array[1]);
						return false;
					case "page":
					case "?page":
						parent.openPage($varsPair_array[1]);
						return false;
					case "cat":
						parent.openCategory($varsPair_array[1]);
						return false;
				}
			}
		}
    	return false;
   	});
});