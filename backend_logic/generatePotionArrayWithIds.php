<?php
require_once $_SERVER["DOCUMENT_ROOT"].'/definitions.php';
function generatePotionList(){
	$allItemsArray = json_decode(file_get_contents(ALL_ITEMS), true);
	$potionNamesArray = json_decode(file_get_contents(POTIONS_NAME_LIST), true);
	$potionList = array();
	foreach ($allItemsArray as $index => $item) {
		foreach ($potionNamesArray as $index => $name) {
			//Remove last three characters to enable literal matching
			if (strcmp($name, substr($item["name"], 0, -3)) == 0){
				$potionItem = array();
				$potionItem["name"] = $item["name"];
				$potionItem["name"] = $item["name"];
				$potionList[$name][$item["name"]] = array('id' => $item["id"]);
			}
		}	
	}
	return $potionList;
}
#var_dump(generatePotionList());
file_put_contents(ALL_POTIONS_WITH_IDS, json_encode(generatePotionList()));

?>