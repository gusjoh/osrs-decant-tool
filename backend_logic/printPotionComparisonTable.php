<?php

require_once __DIR__.'/../definitions.php';

validateForm();

function validateForm(){
	$lowHighMode = 2;
	$minQuantity = 0;
	$hoursData = 1;
	
	if(isset($_GET["modeDecantOption"])){
		$tempLowHighMode = sanitizeNumericInput($_GET["modeDecantOption"]);
		if(is_numeric($tempLowHighMode)){
			switch (intval($tempLowHighMode)) {
				case 1:
				$lowHighMode = 1;
				break;
				case 11:
				$lowHighMode = 11;
				break;
				case 2:
				$lowHighMode = 2;
				break;
				case 3:
				$lowHighMode = 3;
				break;
				case 33:
				$lowHighMode = 33;
				break;
				default:
				//Faulty value
				$lowHighMode = 2;
				break;
			}
		}
	}
	if(isset($_GET["quantityDecantOption"])){
		$tempMinQuantity = sanitizeNumericInput($_GET["quantityDecantOption"]);
		if(is_numeric($tempMinQuantity)){
			if(0 <= (int)$tempMinQuantity && (int)$tempMinQuantity <= 1000000){
				$minQuantity = (int)$tempMinQuantity;
			}else{
				//Faulty value
			}
		}
	}
	if(isset($_GET["timeDecantOption"])){
		$tempHoursData = sanitizeNumericInput($_GET["timeDecantOption"]);
		if(is_numeric($tempHoursData)){
			switch ((int)$tempHoursData) {
				case '1':
				$hoursData = 1;
				break;
				case '6':
				$hoursData = 6;
				break;
				case '24':
				$hoursData = 24;
				break;
				default:
				//Faulty value
				$hoursData = 1;
				break;
			}
		}
	}
	printPotionComparisonTable($lowHighMode, $minQuantity, $hoursData);
}

function sanitizeNumericInput($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function printPotionComparisonTable($lowAvgHighMode, $minQuantity, $hoursData){
	
	$allPotsWithIdsPricesQuantities = null;
	switch ($hoursData) {
		case '1':
		$allPotsWithIdsPricesQuantities = json_decode(file_get_contents(PRICES_POTIONS_1H), true);
		break;
		case '6':
		$allPotsWithIdsPricesQuantities = json_decode(file_get_contents(PRICES_POTIONS_6H), true);
		break;		
		case '24':
		$allPotsWithIdsPricesQuantities = json_decode(file_get_contents(PRICES_POTIONS_24H), true);
		break;
		default:
		$allPotsWithIdsPricesQuantities = json_decode(file_get_contents(PRICES_POTIONS_1H), true);
		break;
	}
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Decant tool</title>
		<link rel="stylesheet" href="decantStyle.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	</head>
	<body class="bg-dark mt-5">
		<div class="container-xxl text-white">
			<form class="row g-3" method="GET">
				<div class="col-sm">
					<label for="modeDecantOption" class="form-label">Mode</label>
					<select id="modeDecantOption" name="modeDecantOption" class="form-select">
					<option value="1">Fast (latest)</option>
					<option value="11">Fast (average)</option>
					<option value ="2">Average (average)</option>
					<option value="3">Slow (latest)</option>
					<option value="33">Slow (average)</option>
					</select>
				</div>
				<div class="col-sm">
					<label for="quantityDecantOption" class="form-label">Min. Quantity</label>
					<input type="number" id="quantityDecantOption" name="quantityDecantOption" class="form-control" placeholder="Quantity" aria-label="Quantity" value=<?php echo('"'.$minQuantity.'"') ?> min="0" max="1000000">
				</div>
				<div class="col-sm">
					<label for="timeDecantOption" class="form-label">Time (hours)</label>
					<select id="timeDecantOption" name="timeDecantOption" class="form-select">
						<?php echo(getTimeToForm($hoursData))?>
					</select>
				</div>				
				<div class="col-12 d-flex justify-content-end">
					<button type="submit" class="btn btn-primary m-1">Submit</button>
					<a href="/" class="btn btn-danger m-1">Reset</a>
				</div>
			</form>
			<div class="row">
				<table class="table table-dark table-striped table-hover table-sm" id="potionComparisonTable">
					<thead>
						<tr>
							<th scope="col">Potion</th>
							<th scope="col">Margin % (Averaged)</th>
							<th scope="col">Margin GP (Averaged)</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$modalString = null;
						$modalIterationIndex = 1;				
						foreach ($allPotsWithIdsPricesQuantities as $potCatName => $potsCatArray) {
							$margins = getMargins($lowAvgHighMode, $potsCatArray, $minQuantity);
							echo('<tr data-bs-toggle="modal" data-bs-target="#potModal'.$modalIterationIndex.'">	
								<th scope="row">'.$potCatName.'</th>	
								<td>'.$margins[0].'</td>	
								<td>'.$margins[1].'</td>
								</tr>');
							$modalString .= '
							<div class="modal fade" id="potModal'.$modalIterationIndex.'" tabindex="-1" aria-labelledby="modalLabel'.$modalIterationIndex.'" aria-hidden="true">
							<div class="modal-dialog modal-xl">
							<div class="modal-content bg-dark">
							<div class="modal-header text-white">
							<h5 class="modal-title" id="modalLabel'.$modalIterationIndex.'">'.$potCatName.'</h5>
							<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body text-white">
							<div class="row d-none d-lg-flex fw-bold">
							<div class="col-6 col-lg">Potion</div>
							<div class="col-6 col-lg">Last instasell</div>
							<div class="col-6 col-lg">Last instabuy</div>
							<div class="col-6 col-lg">Averaged cost</div>
							<div class="col-6 col-lg">Total quantity</div>
							</div>														
							';
							foreach ($potsCatArray as $potion => $potInfo) {
								if($minQuantity <= $potInfo["totalVolume"]){
									//If untiltered
									$modalString .='
									<div class="row d-lg-flex">
									';
								}else{
									//Filtered
									$modalString .='
									<div class="row d-lg-flex text-decoration-line-through">
									';

								}
								$modalString .= '<div class="col-12 col-lg"><a class="link-light" target="_blank" href="https://prices.runescape.wiki/osrs/item/'.$potInfo["id"].'">'.$potion.'</a></div>
								<div class="col-6 d-lg-none">Last instasell:</div>
								<div class="col-6 col-lg text-end text-lg-start">'.$potInfo["latestLowDosePrice"].' ('.$potInfo["latestLow"].') / '.getTimeElapsedSinceLastTrade($potInfo["latestLowTimeElapsed"]).'</div>
								<div class="col-6 d-lg-none">Last instabuy:</div>
								<div class="col-6 col-lg text-end text-lg-start">'.$potInfo["latestHighDosePrice"].' ('.$potInfo["latestHigh"].') / '.getTimeElapsedSinceLastTrade($potInfo["latestHighTimeElapsed"]).'</div>
								<div class="col-6 d-lg-none">Averaged cost:</div>
								<div class="col-6 col-lg text-end text-lg-start">'.$potInfo["avgAvgDosePrice"].' ('.$potInfo["avgAvgPrice"].')</div>
								<div class="col-6 d-lg-none">Total volume:</div>
								<div class="col-6 col-lg text-end text-lg-start">'.$potInfo["totalVolume"].'</div>
								</div>';

							}
							$modalString .='
							</div>
							<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							</div>
							</div>
							</div>
							</div>
							';
							$modalIterationIndex++;
						}

						?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		echo($modalString);
		?>
		<script src="decantTool.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	</body>
	<?php
}

function getMargins($fastAvgSlowMode, $potsCatArray, $quantityThreshold){
	$lowest = 0;
	$highest = 0;
	$marginPercent = 0;
	$marginGold = 0;
	
	foreach ($potsCatArray as $potion => $potInfo) {
		if($potInfo["totalVolume"] > $quantityThreshold){
			$latestLowDosePrice = $potInfo['latestLowDosePrice'];
			$avgAvgDosePrice = $potInfo["avgAvgDosePrice"];
			$latestHighDosePrice = $potInfo["latestHighDosePrice"];
			switch ($fastAvgSlowMode) {
				case 1:
					//Fast latest
					if($latestLowDosePrice > $highest){
						$highest = $latestLowDosePrice;
					}	
					if($latestHighDosePrice > 0 && $lowest > $latestHighDosePrice || $lowest == 0){
						$lowest = $latestHighDosePrice;
					}
					break;
				case 11:
					//Fast avg
					//TODO
					break;
				case 2:
					//Average avg
					if($avgAvgDosePrice > $highest){
						$highest = $avgAvgDosePrice;
					}	
					if($avgAvgDosePrice > 0 && $lowest > $avgAvgDosePrice || $lowest == 0){
						$lowest = $avgAvgDosePrice;
					}
					break;
				case 3;
					//Slow latest
					if($latestHighDosePrice > $highest){
						$highest = $latestHighDosePrice;
					}	
					if($latestHighDosePrice > 0 && $lowest > $latestHighDosePrice || $lowest == 0){
						$lowest = $latestHighDosePrice;
					}
					break;
				case 33:
					//Slow avg
					//TODO
					break;
				default:
					break;
			}
		}
	}
	if($lowest > 0){
		$marginPercent = round((($highest/$lowest)-1)*100);
	}	
	$marginGold = $highest - $lowest;
	return array(
		$marginPercent, $marginGold
	);
}
function getTimeToForm($timeInHours){
	switch ($timeInHours) {
		case "1":
		$options = "<option selected>1</option>
		<option>6</option>
		<option>24</option>";
		break;
		case "6":
		$options = "<option>1</option>
		<option selected>6</option>
		<option>24</option>";
		break;
		case "24":
		$options = "<option>1</option>
		<option>6</option>
		<option selected>24</option>";
		break;
		default:
		$options = "<option selected>1</option>
		<option>6</option>
		<option>24</option>";
		break;
	}
	return $options;
}
function getTimeElapsedSinceLastTrade($seconds){
	//Day
	if($seconds >= 86400){
		return round($seconds / 86400)."d";
	}elseif ($seconds >= 3600) {
		return round($seconds / 3600)."h";
	}elseif ($seconds >= 60) {
		return round($seconds / 60) ."m";
	}else{
		return "error";
	}
}

?>