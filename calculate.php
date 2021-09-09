<?php

$driver_data= 'driver_names.txt';
$address_data = 'address.txt';
$odd_even_spectrum = array();
$reverse_even_odd_spectrum = array();
$prime_number_factors = array(2,3,5,7,11,13,17,19,23,29,31,37,41,43,47,53,59);
$score = 0;
$boostMultiplier = 1.5;
$normalMultiplier = 1.0;

$line = readline("Addresses File name: ");
$address_data = $line;

$line = readline("Drivers File name: ");
$driver_data = $line;

if (file_exists($driver_data)) {
    $lines = file($driver_data);

    foreach ($lines as $driver) {
      $trmd_name = trim($driver);
      $odd_even_spectrum[$trmd_name] = getVCRatio(trim($driver));
    }
    arsort($odd_even_spectrum);
    $reverse_even_odd_spectrum = array_reverse($odd_even_spectrum, true);
}

if (file_exists($address_data)) {
  $addresses = file($address_data);
  $selected_driver = null;
  foreach($addresses as $address) {
    $street_name = getStreetName($address);
    if(isStreetEven($street_name)==true) {
      // get driver calculate score
      foreach($reverse_even_odd_spectrum as $driver => $ratio) {
        // calculate based on number of vowels and formula;
        $letterCount = getVowelCount($driver);
        $suitabilityMultiplierStatus = isFactorShared($street_name, $driver);
	$currentScore = ($letterCount * $boostMultiplier);
        if($suitabilityMultiplierStatus) {
          $currentScore = $currentScore * $boostMultiplier;
        }
        $score += $currentScore;
        $selected_driver = $driver;
        break;
      }
    } else {
      foreach($odd_even_spectrum as $driver => $ratio) {
        // calculate based on number of consonants and formula;
        $letterCount = getConsonantCount($driver);
        $suitabilityMultiplierStatus = isFactorShared($street_name, $driver);
        $currentScore = ($letterCount * $normalMultiplier);
        if($suitabilityMultiplierStatus) {
          $currentScore = $currentScore * $boostMultiplier;
        }
        $score += $currentScore;
        $selected_driver = $driver;
        break;
      }
    } 
    removeDriver($selected_driver);
  }
  echo "\n\nSuitability Score: " . $score;
}

function getVowelCount($subject) {
  // $total = preg_match_all('/[aeiou]/i', $subject, $matches);
  return preg_match_all('/[aeiou]/i', $subject, $matches);
}

function getConsonantCount($subject) {
  return preg_match_all('/[b-df-hj-np-tv-z]/i', $subject, $matches);
}

function getVCRatio($word) {
  $even_vowel_count = getVowelCount($word);
  $odd_vowel_count = getConsonantCount($word);
  $value = $odd_vowel_count / $even_vowel_count;
  return $value;
}

function getStreetName($addr) {
  $street_index = strpos($addr, " ");
#  echo trim(substr($addr, $street_index+1));
  return trim(substr($addr, $street_index+1));
}

function isStreetEven($str) {
  $isEvenFlag = strlen($str) % 2 == 0 ? 1 : 0;
#  echo "\n" . $isEvenFlag;
  return $isEvenFlag;
}

function removeDriver($drv) {
  global $reverse_even_odd_spectrum, $odd_even_spectrum;

  unset($reverse_even_odd_spectrum[$drv]);
  unset($odd_even_spectrum[$drv]);
  return;
}

function isFactorShared($adr, $drv) {
  global $prime_number_factors;
  $aLength = strlen($adr);
  $dLength = strlen($drv);

  $maxFactor = $aLength > $dLength ? $aLength : $dLength;
  $factorStatus = false;
  foreach($prime_number_factors as $factor) {
    if($aLength % $factor == 0 && $dLength % $factor == 0) {
      $factorStatus = true;
      // print_r("\nCommon Factor: " . $factor . " | " . $aLength . " vs " . $dLength);
      break;
    }
    if ($factor > $maxFactor) {
      // print_r("\nNo Common factors found");
      break;
    }
  }
  return $factorStatus;
}
?>



