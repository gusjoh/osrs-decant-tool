<?php

require_once $_SERVER["DOCUMENT_ROOT"].'/definitions.php';

function populatePotionArrayIdsPricesQuantities($fileWithAllPrices){

	$allPotsWithIds = json_decode(file_get_contents(ALL_POTIONS_WITH_IDS), true);
	$priceOverTime = json_decode(file_get_contents($fileWithAllPrices), true);
	$latestPrice = json_decode(file_get_contents(PRICES_ALL_LATEST), true);
	
	foreach ($allPotsWithIds as $potCategory => &$potionsInCategory) {
		foreach ($potionsInCategory as $potionTitle => &$potionInformation) {
			$potionInformation["avgHighPrice"] = 0;
			$potionInformation["highPriceVolume"] = 0;
			$potionInformation["avgLowPrice"] = 0;
			$potionInformation["lowPriceVolume"] = 0;		
			//If there is data in price history - else item id is missing from price data
			if(array_key_exists($potionInformation["id"], $priceOverTime["data"])){
				$potionPriceArray = $priceOverTime["data"][$potionInformation["id"]];
				if ($potionPriceArray["avgHighPrice"] != NULL){
					$potionInformation["avgHighPrice"] = $potionPriceArray["avgHighPrice"];
				}
				if ($potionPriceArray["highPriceVolume"] != NULL){
					$potionInformation["highPriceVolume"] = $potionPriceArray["highPriceVolume"];
				}
				if ($potionPriceArray["avgLowPrice"] != NULL){
					$potionInformation["avgLowPrice"] = $potionPriceArray["avgLowPrice"];
				}
				if ($potionPriceArray["lowPriceVolume"] != NULL){
					$potionInformation["lowPriceVolume"] = $potionPriceArray["lowPriceVolume"];
				}
			}else{
				//Potion ID missing from price information
				#echo($potionInformation["id"]);
			}
			//Store potion size
			if(ctype_digit($potionTitle[strlen($potionTitle)-2])){
				$potionsInCategory[$potionTitle]["potionSize"] = intval($potionTitle[strlen($potionTitle)-2]);
			}


			$potionInformation["avgLowDosePrice"] = intval(round($potionInformation["avgLowPrice"]/$potionInformation["potionSize"]));
			$potionInformation["avgHighDosePrice"] = intval(round($potionInformation["avgHighPrice"]/$potionInformation["potionSize"]));
			//Not the same as daily volume listed on WIKI price page, should be presented to end user
			$potionInformation["totalVolume"] = $potionInformation["lowPriceVolume"]+$potionInformation["highPriceVolume"];
			$potionInformation["avgAvgDosePrice"] = 0;
			$potionInformation["avgAvgPrice"] = 0;

			if($potionInformation["totalVolume"] > 0){
				$potionInformation["avgAvgDosePrice"] = intval(round((($potionInformation["avgLowPrice"]*$potionInformation["lowPriceVolume"])+($potionInformation["avgHighPrice"]*$potionInformation["highPriceVolume"]))/$potionInformation["totalVolume"])/$potionInformation["potionSize"]);
				$potionInformation["avgAvgPrice"] = $potionInformation["avgAvgDosePrice"] * $potionInformation["potionSize"];
			}
			if(array_key_exists($potionInformation["id"], $latestPrice["data"])){
				#$potionInformation["latestHigh"] = $latestPrice[$potionInformation["id"]];
				#echo($latestPrice["data"][$potionInformation["id"]]["highTime"]);
				$potionInformation["latestHigh"] = $latestPrice["data"][$potionInformation["id"]]["high"];
				$potionInformation["latestHighDosePrice"] = intval(round($potionInformation["latestHigh"] / $potionInformation["potionSize"]));
				$potionInformation["latestLow"] = $latestPrice["data"][$potionInformation["id"]]["low"];
				$potionInformation["latestLowDosePrice"] = intval(round($potionInformation["latestLow"] / $potionInformation["potionSize"]));
				$potionInformation["latestHighTime"] = $latestPrice["data"][$potionInformation["id"]]["highTime"];
				$potionInformation["latestLowTime"] = $latestPrice["data"][$potionInformation["id"]]["lowTime"];
    			//https://stackoverflow.com/questions/8273804/convert-seconds-into-days-hours-minutes-and-seconds/19680778#19680778
				//https://stackoverflow.com/questions/1519228/get-interval-seconds-between-two-datetime-in-php
				$dtlatestHighTime = new \DateTime('@'. $potionInformation["latestHighTime"]);
				$dtlatestLowTime = new \DateTime('@'. $potionInformation["latestLowTime"]);
				$dtCurrent = new \DateTime('@' . time());
				#var_dump($dtCurrent->getTimestamp() - $dtlatestHighTime->getTimestamp());
				#var_dump($dtlatestHighTime->diff($dtCurrent)->format('%a days, %h hours, %i minutes and %s seconds'));
			
				$potionInformation["latestHighTimeElapsed"] = $dtCurrent->getTimestamp() - $dtlatestHighTime->getTimestamp();
				$potionInformation["latestLowTimeElapsed"] = $dtCurrent->getTimestamp() - $dtlatestLowTime->getTimestamp();
			}

		}
		unset($potionInformation);
	}
	unset($potionsInCategory);
	return $allPotsWithIds;
}

file_put_contents(PRICES_POTIONS_1H, json_encode(populatePotionArrayIdsPricesQuantities(PRICES_ALL_1H)));
file_put_contents(PRICES_POTIONS_6H, json_encode(populatePotionArrayIdsPricesQuantities(PRICES_ALL_6H)));
file_put_contents(PRICES_POTIONS_24H, json_encode(populatePotionArrayIdsPricesQuantities(PRICES_ALL_24H)));

?>