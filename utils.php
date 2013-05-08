<?php

/**
 * @return the value at $index in $array or $default if $index is not set.
 */
function idx(array $array, $key, $default = null) {
  return array_key_exists($key, $array) ? $array[$key] : $default;
}

function he($str) {
  return htmlentities($str, ENT_QUOTES, "UTF-8");
}


function getSslPage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}



function get_signed_request($signed_request)
{
	if ($signed_request != "")
	{
		list($encoded_sig, $payload) = explode(".", $signed_request, 2);
		$sig                         = base64_decode(strtr($encoded_sig, "-_", "+/"));
		$signed_request              = json_decode(base64_decode(strtr($payload, "-_", "+/"), TRUE), TRUE);
	}
    return $signed_request;
}



// Time format is UNIX timestamp or
// PHP strtotime compatible strings
function dateDiff($time1, $time2, $locale, $precision = 6) {


    // If not numeric then convert texts to unix timestamps
    if (!is_int($time1)) {
      $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
      $time2 = strtotime($time2);
    }
 
    // If time1 is bigger than time2
    // Then swap time1 and time2
    if ($time1 > $time2) {
      $ttime = $time1;
      $time1 = $time2;
      $time2 = $ttime;
    }
 
    $diffs = array();
    $intervals = array('year','month','day','hour','minute','second');

    // Loop thru all intervals
    foreach ($intervals as $interval) {
      // Set default diff to 0
      $diffs[$interval] = 0;
      // Create temp time from time1 and interval
      $ttime = strtotime("+1 " . $interval, $time1);
      // Loop until temp time is smaller than time2
      while ($time2 >= $ttime) {
	$time1 = $ttime;
	$diffs[$interval]++;
	// Create new temp time from time1 and interval
	$ttime = strtotime("+1 " . $interval, $time1);
      }
    }
 
    $count = 0;
    $times = array();
    $timesSimple = "";
    // Loop thru all diffs
    foreach ($diffs as $interval => $value) {

    if(stristr(substr($locale, 0, 2), 'fr'))
    {
      if($interval == 'year') { $interval = 'ann&eacute;e'; }
      if($interval == 'month') { $interval = 'mois'; }
      if($interval == 'day') { $interval = 'jour'; }
      if($interval == 'hour') { $interval = 'heure'; }
      if($interval == 'minute') { $interval = 'minute'; }
      if($interval == 'second') { $interval = 'seconde'; }
    }

      // Break if we have needed precission
      if ($count >= $precision) {
	    break;
      }
      // Add value and interval 
      // if value is bigger than 0
      if ($value > 0) {
	    // Add s if value is not 1
	    if ($value != 1 && $interval != "mois") {
	      $interval .= "s";
	    }
	    // Add value and interval to times array
	    $times[] = $value . " " . $interval;
	    $timesSimple = $value . " " . $interval;
	    $count++;

        if ($interval == "year" || $interval == "years" ||$interval == "month" || $interval == "months" || $interval == "day" || $interval == "days" || $interval == "ann&eacute;e" || $interval == "ann&eacute;es" ||$interval == "mois" || $interval == "months" || $interval == "jour" || $interval == "jours") {
	        break;
        }

        if ($interval == "minute" || $interval == "minutes") {
	        break;
        }

      }
    }
 


    // Return string with times
    return implode(", ", $times);
    //return $timesSimple;
  }