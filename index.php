<?php

/*
*
* This file is clean and the list.txt file by transforming the cell numbers of various formats
* in the Brazilian standard for SMS shots. This final pattern can be easily changed. 
* If you need help, cristianweiser@gmail.com
*
*/

/* Config */
$input = "list.txt";
$output = "ok_list.txt";

function clean($string) {
   $string = trim($string); //removes whitespaces from beginning and ending
   $string = str_replace(' ', '', $string); // remove whitespace anywhere else
   $string = str_replace('-', '', $string); // remove hifens
   $string = preg_replace('/[^0-9\-]/', '', $string); // remove any non numeric chars
   $split = str_split($string, 2);
   if( (strlen($string) > 11) && ($split[0] == 55) ) { $string = str_replace('55', '', $string); } //remove country code ex: Brazil is 55
   if( (strlen($string) == 9) || (strlen($string) == 8) ) { $string = 19 . $string; } //to short, probably needs same area code that ours
   return $string;
}

/* 
* Format the string to default pattern DDNNNNNNNNN where D is area code (2 digits) and N is celphone number with 9 digits. (Brazil standard pattern)
*/

function remove_inizero($string) {  //remove old area codes started with zero
	$split = str_split($string);
	if ($split[0] == 0) { $string = substr($string, 1); }
	return $string;
	} 
	
function new_num($string) { //adds a 9 in 3rd position after 2 digits area code. Exception is Nextel numbers starting with 7 on 3rd position
	    $split = str_split($string);
	    if( (strlen($string) == 10) &&  ($split[2] != 7) ){
		    $add = '9' . $split[2];
		    $string = str_replace($split[2], $add, $string);
		    } 
    return $string;
}

function fixlen($string) {
    if(strlen($string) <= 11) { return $string; } 
 	if(strlen($string) == 13) { //suppose that 2 digit operator code must be removed.
 		$string = substr($string, 2); 
 		return $string;
 	 } 
 	 else { $erro++; }
}


function sanitize($number) {
	$number = clean($number);
	$number = remove_inizero($number);
	$number = new_num($number);
	$number = fixlen($number);
    if(strlen($number) > 1) { 
		//save new list
	    $myFile = $output;
		$fh = fopen($myFile, 'a') or die("can't open file");
		$stringData = $number . "\r\n";
		//echo $stringData . "<br />"; debug purposes.. no need to print.
		fwrite($fh, $stringData);
		fclose($fh);
 }
}

/*
*
* Starting operational loop trough output file
*/


$file = fopen($input, "r");
$members = array();
	while (!feof($file)) {
	   $members[] = fgets($file);
	}
$total_linhas = count($members);
fclose($file);

$i = 0;
foreach ($members as $member) {
    $numero = $members[$i];
    sanitize($numero); //function that does the job
    //if($i == $total_linhas-1) { echo "End of Work"; }
    $i++;   
}

if($erro > 0) { echo "We had $erro numbers with error that could not be fixed. "; }
echo "Sucessfull Work. $total_linhas celphones cleared. <a href='$output'>Click here to check results</a>";


