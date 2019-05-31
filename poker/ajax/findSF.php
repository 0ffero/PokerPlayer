<?php
// WANNA SEE THE INNER WORKINGS? ENABLE THE NEXT BLOCK && ALL COMMENTS
/* 
$have="";
$straightHand="35678912";
$suitString="SHHHHHD";
*/

$straightA=array("12345","23456","34567","45678","56789","678910","7891011","89101112","910111213","1011121314");

foreach ($straightA as $currentStraight) {
	$pos=strpos($straightHand, $currentStraight);
	if ($pos>=1 || $pos===0) {
		// echo "CS: " . $currentStraight . "; POS>0 - " . $pos . " : ";
		if (strpos($suitString,"HHHHH") === $pos || strpos($suitString,"CCCCC") === $pos || strpos($suitString,"DDDDD") === $pos || strpos($suitString,"SSSSS") === $pos) {
			//echo strpos($suitString,"HHHHH"); echo "<br/>";
			$have["SF"]=1;
		}
	}
}
//print_r($have);
?>