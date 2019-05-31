<?php
ini_set("display_errors",0);
//$handString="6,H,6,S,6,D,8,C,8,H,8,D,11,C"; // MIX
$minLen=strlen("1,A,1,A,1,A,1,A,1,A,1,A,1,A");
$maxLen=strlen("13,A,13,A,13,A,13,A,13,A,13,A,13,A");
$handStringA=$_GET["handString"];

$handStringA=explode('|',$handStringA);
// Check input string to see if its safe
for ($i=0;$i<count($handStringA);$i++) {
    $check=$handStringA[$i];
    if (strlen($check)>=$minLen && strlen($check)<=$maxLen) {
        $continue++;
    } else {
        $continue=0;
    }
}
if ($continue!=count($handStringA)) { echo "Invalid Hand! - IP recorded"; exit; } // should probably add this functionality at some point :)

function checkFor234oaK($hand,$chIndex) { // This function now includes straight, flush etc; function will be renamed when I can be arsed
    
    global $returnHand;
    
    $cardsA=explode(",",$hand);
    $cardNums=array(); $cardSuits=array();
    if (count($cardsA)==4) { // Players Cards Only (no flop etc)
        
        
        if ($cardsA[0]==$cardsA[2]) {
            echo '<div class="blueS">Starting Pair</div>'; 
        }
        if ($cardsA[1]==$cardsA[3]) {
            echo '<div class="blueS">Starting Flush</div>';
        }
        
        
        
        


    } else { // Players Cards inc. Flop etc




        // Set up Card Nums Array
        for ($i=0; $i<count($cardsA); $i+=2) {
            $cardNums[$cardsA[$i]]=$cardNums[$cardsA[$i]] + 1;
        }
        // Set Up Card Suits Array
        for ($i=1; $i<=count($cardsA); $i+=2) {
            $cardSuits[$cardsA[$i]]=$cardSuits[$cardsA[$i]] + 1;
        }
        
        ksort($cardNums);

        // THIS SECTION SETS UP THE $have ARRAY FOR LATER USE
        
        // Check for Pairs, 3oaK and 4oaK
        foreach($cardNums as $index => $peekAtCard) {
            if ($peekAtCard==2) {
                $have["pair"]=$have["pair"]+1;
                if ($index==1) { $index=14; }
                $highCard=$index;
            }
            if ($peekAtCard==3) {
                $have["3oaK"]=$have["3oaK"]+1;
                if ($index==1) { $index=14; }
                $highCard=$index;
            }
            if ($peekAtCard==4) {
                $have["4oaK"]=true;
                if ($index==1) { $index=14; }
                $highCard=$index;
            }
        }
            
        // Check for flush
        foreach($cardSuits as $index => $peekAtCard) {
            if ($peekAtCard==5) {
                $have["flush"]=true;
                // we have to look at $cardNums for the highest card here as the index will simply hold the suit type
                if ($cardNums[1]<2) { // looks for an ace at the start of the hand, if it doesnt find it it checks the right most card
                    $highCard=end(array_keys($cardNums));
                } else {
                    $highCard=14;
                }
            }
        }
    
        // Check for straight
        ksort($cardNums);
        if ($cardNums[1]==1) { $cardNums[14]=true; }
        foreach($cardNums as $index=>$suit) {
            $straight.=$index;
        }
        if (substr_count($straight,"12345")==1 || substr_count($straight,"23456")==1 || substr_count($straight,"34567")==1 || substr_count($straight,"45678")==1 || substr_count($straight,"56789")==1 || substr_count($straight,"678910")==1 || substr_count($straight,"7891011")==1 || substr_count($straight,"89101112")==1 || substr_count($straight,"910111213")==1) {
            $have["straight"]=true;
            if (!$cardNums[1]) { // looks for an ace at the start of the hand, if it doesnt find it it checks the right most card
                $highCard=end(array_keys($cardNums));
            } else {
                $highCard=14;
            }
        }
        
        // Check for Royal Straight
        if (substr_count($straight,"1011121314")==1) {
            $have["royalStraight"]=true;
            $highCard=14;
        }
        
        // DO THE CHECK TO SEE IF THE FLUSH CARDS = STRAIGHT CARDS
        if (($have["straight"] || $have["royalStraight"]) && $have["flush"]) {
            for ($i=1;$i<count($cardsA);$i+=2) {
                $suitString.=$cardsA[$i];
            }
            $straightHand=$straight;
            include("findSF.php");
        }
    
        // check for fullhouse
        if ($have["3oaK"]==1 && $have["Pair"]>0) {
            $have["fullHouse"]=true;
        }
        if ($have["3oaK"]==2) {
            $have["fullHouse"]=true;
        }
        
        if (!$have) { $have["FUQOL"]=1; }

    }    
    
    // THIS SECTION CHECKS FOR WINNING HANDS (using $have array)
    if ($have["royalStraight"] && $have["flush"])   { $highHand[]="RF"; } // Royal Flush
    if ($have["4oaK"])                              { $highHand[]="4K"; } // 4 of a Kind
    if ($have["3oaK"]>0 && $have["pair"]>0)         { $highHand[]="FH"; } // Full House
    // if ($have["flush"] && $have["straight"])        { $highHand[]="SF"; } // Straight Flush
                                                                             /* if user has a non flush straight but ALSO has a flush then the computer thinks its a straight flush
                                                                                ie 3H,4H,5S,6H,7H,10H,12S <-- is seen as a straight flush (5 hearts + straight), which is def wrong :) */
    if ($have["flush"])                             { $highHand[]="FL"; } // Flush
    if ($have["straight"])                          { $highHand[]="S8"; } // Straight
    if ($have["3oaK"])                              { $highHand[]="3K"; } // 3 of a Kind
    if ($have["pair"])                              { $highHand[]=$have["pair"] . "P"; } // 1P[air], 2P[air], 3P[air]?

    $highHand=array_unique($highHand);
    $highHand=array_reverse($highHand);
    $returnHand=implode("<br/>",$highHand);
    unset($have); $highHand=''; // reset the arrays
}





/* Running order starts here */

// Global Variables
$returnHand='';


foreach ($handStringA as $handString) {
    $tmp=explode(",",$handString);
    // ill eventually do this in a loop: although this code works it is long-winded :)
    $hand[1]=$tmp[0] . "," . $tmp[1] . "," . $tmp[2] . "," . $tmp[3];
    $hand[2]=$tmp[0] . "," . $tmp[1] . "," . $tmp[2] . "," . $tmp[3] . "," . $tmp[4] . "," . $tmp[5] . "," . $tmp[6] . "," . $tmp[7] . "," . $tmp[8] . "," . $tmp[9];
    $hand[3]=$tmp[0] . "," . $tmp[1] . "," . $tmp[2] . "," . $tmp[3] . "," . $tmp[4] . "," . $tmp[5] . "," . $tmp[6] . "," . $tmp[7] . "," . $tmp[8] . "," . $tmp[9] . "," . $tmp[10] . "," . $tmp[11];
    $hand[4]=$tmp[0] . "," . $tmp[1] . "," . $tmp[2] . "," . $tmp[3] . "," . $tmp[4] . "," . $tmp[5] . "," . $tmp[6] . "," . $tmp[7] . "," . $tmp[8] . "," . $tmp[9] . "," . $tmp[10] . "," . $tmp[11] . "," . $tmp[12] . "," . $tmp[13];
    
    foreach($hand as $index=>$hsTmp) {
        checkFor234oaK($hsTmp,$index);
        if ($index==4) {
            if ($returnHand) { echo $returnHand . "|"; } else { echo "|"; }
        }
    }
    
    /* The following functions are now incorporated into the above (checkFor234oaK) function
    checkForFullHouse($handString);
    checkForFlush($handString);
    checkForStraight($handString); // This will also look to see if its a royal straight/flush
    checkForStraightFlush($handString);
    */
}

?>