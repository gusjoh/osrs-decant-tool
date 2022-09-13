<?php

require_once __DIR__ . '/../definitions.php';

//Curl handlers
$curlHandler1 = curl_init("https://prices.runescape.wiki/api/v1/osrs/latest");
$curlHandler2 = curl_init("https://prices.runescape.wiki/api/v1/osrs/1h");
$curlHandler3 = curl_init("https://prices.runescape.wiki/api/v1/osrs/6h");
$curlHandler4 = curl_init("https://prices.runescape.wiki/api/v1/osrs/24h");

//Curl Options
$curlOptions = array(
	CURLOPT_HEADER => false,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FAILONERROR	=> true,
	CURLOPT_TIMEOUT => 5,
	CURLOPT_USERAGENT => "Potion_Decanting_Calculator"
);
curl_setopt_array($curlHandler1, $curlOptions);
curl_setopt_array($curlHandler2, $curlOptions);
curl_setopt_array($curlHandler3, $curlOptions);
curl_setopt_array($curlHandler4, $curlOptions);

$multiHandle = curl_multi_init();

curl_multi_add_handle($multiHandle, $curlHandler1);
curl_multi_add_handle($multiHandle, $curlHandler2);
curl_multi_add_handle($multiHandle, $curlHandler3);
curl_multi_add_handle($multiHandle, $curlHandler4);

//TODO: Implement exception handling on data collection
do {
	$status = curl_multi_exec($multiHandle, $active);
	if ($active) {
		curl_multi_select($multiHandle);
	}
} while ($active && $status == CURLM_OK);

curl_multi_remove_handle($multiHandle, $curlHandler1);
curl_multi_remove_handle($multiHandle, $curlHandler2);
curl_multi_remove_handle($multiHandle, $curlHandler3);
curl_multi_remove_handle($multiHandle, $curlHandler4);
curl_multi_close($multiHandle);

file_put_contents(PRICES_ALL_LATEST, curl_multi_getcontent($curlHandler1));
file_put_contents(PRICES_ALL_1H, curl_multi_getcontent($curlHandler2));
file_put_contents(PRICES_ALL_6H, curl_multi_getcontent($curlHandler3));
file_put_contents(PRICES_ALL_24H, curl_multi_getcontent($curlHandler4));
