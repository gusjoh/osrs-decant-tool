<?php 
	
	//Directories
	define('BACKEND_LOGIC', __DIR__.'/backend_logic/');
	define('DYNAMIC_DATA_DIRECTORY', __DIR__.'/data_dynamic/');
	define('STATIC_DATA_DIRECTORY', __DIR__.'/data_static/');
	//PHP scripts
	define('GET_DATA_FROM_API', BACKEND_LOGIC.'getDataFromAPI.php');
	define('CALCULATE_POTION_PRICES', BACKEND_LOGIC.'calculatePotionPrices.php');
	define('PRINT_POTION_COMPARISON_TABLE', BACKEND_LOGIC.'printPotionComparisonTable.php');
	//Data files - dynamic
	define('PRICES_ALL_24H', DYNAMIC_DATA_DIRECTORY.'prices_API_all_24h.json');
	define('PRICES_ALL_6H', DYNAMIC_DATA_DIRECTORY.'prices_API_all_6h.json');
	define('PRICES_ALL_1H', DYNAMIC_DATA_DIRECTORY.'prices_API_all_1h.json');
	define('PRICES_ALL_LATEST', DYNAMIC_DATA_DIRECTORY.'prices_API_all_latest.json');
	define('PRICES_POTIONS_24H', DYNAMIC_DATA_DIRECTORY.'prices_potions_24h.json');
	define('PRICES_POTIONS_6H', DYNAMIC_DATA_DIRECTORY.'prices_potions_6h.json');
	define('PRICES_POTIONS_1H', DYNAMIC_DATA_DIRECTORY.'prices_potions_1h.json');
	//Data files - static or root
	define('CONFIGURATION', __DIR__.'/config.json');
	define('ALL_POTIONS_WITH_IDS', STATIC_DATA_DIRECTORY.'allPotionsWithIds.json');
	define('POTIONS_NAME_LIST', STATIC_DATA_DIRECTORY.'potionNameList.json');
	define('ALL_ITEMS', STATIC_DATA_DIRECTORY.'allItems.json');
	

?>