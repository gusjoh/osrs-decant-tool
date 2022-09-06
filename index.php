<?php  

require_once __DIR__.'/definitions.php';

$configParams = json_decode(file_get_contents(CONFIGURATION), true);

//This way of acquiring fresh data ought not to be used as it allows end user to spam API requests if there's some configuration error 
if ($configParams['RefreshDataOnPageLoad'] == true){
	$refreshDelayInSeconds = $configParams['DataRefreshRate'];
	$lastDataRefreshPlusDelay = $refreshDelayInSeconds + filemtime(PRICES_ALL_LATEST);
	if($lastDataRefreshPlusDelay < time()){		
		require_once GET_DATA_FROM_API;
	}
	
}

require_once CALCULATE_POTION_PRICES;
require_once PRINT_POTION_COMPARISON_TABLE;


?>