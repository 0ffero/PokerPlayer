<?php
ini_set("display_errors",0);
if ($_COOKIE["showWorking"]) {
    $working=$_COOKIE["showWorking"];
} else {
    // echo "No COOKIE FOUND";
}
if ($_COOKIE["players"]) {
    $players=$_COOKIE["players"];
} else {
    $players=9;
}
if ($working==1) { $display="block"; } else { $display="none"; }
/* *************************************
This code doesnt 'deal' from the top of
a shuffled deck. Instead it takes random
cards from an un-shuffled deck ill sort
it when I can be arsed...
*/
$cardArray=array("hearts","diams","clubs","spades");
foreach ($cardArray as $suit) {
    for ($i=1;$i<=13;$i++) {
        switch ($i) {
            case 1:  $card="A";   break;
            case 11: $card="J";  break;
            case 12: $card="Q"; break;
            case 13: $card="K";  break;
            default: $card=$i;
        }
        
        $fullDeckArray[]=$card . " &" . $suit . ";";
    }
}

// Set up the hands for all the players
for ($o=1; $o<=2;$o++){
    for ($i=1;$i<=$players;$i++) {
        $selectedCard=rand(0,(count($fullDeckArray)-1));
        $hand[$i][$o]= $fullDeckArray[$selectedCard];
        unset($fullDeckArray[$selectedCard]);
        $fullDeckArray=array_values($fullDeckArray);
    }
}
// END

$badASuits = array(" &hearts;", " &diams;", " &clubs;", " &spades;");
$goodASuits = array(",H", ",D", ",C", ",S");
$badANums = array("A","J","Q","K");
$goodANums = array("1","11","12","13");

    
// Set up flop, turn, river
for ($i=1;$i<=5;$i++) {
    $selectedCard=rand(0,(count($fullDeckArray)-1));
    $flop[$i]= $fullDeckArray[$selectedCard];
    unset($fullDeckArray[$selectedCard]);
    $fullDeckArray=array_values($fullDeckArray);
    $tempStr1=$flop[$i];

    $tempStr1=str_replace($badASuits,$goodASuits, $flop[$i]);
    $tempStrJ=$tempStr1 . ",";
    $tempStrJ=str_replace($badANums,$goodANums,$tempStrJ);
    $flopString.=$tempStrJ;
}
// echo "FLOPSTRING: $flopString<p/>";
// END

// Set up the HTML to be shown to user of players hands
$hands="";
$tempStrJ=""; $tempStr1=""; $tempStr2="";
foreach ($hand as $playerNum=>$playerHand) {
    if (substr_count($playerHand[1],"hearts")>0 || substr_count($playerHand[1],"diams")>0) { $tintColor="red"; } else { $tintColor="black"; }
    $hands.='<div id="player' . $playerNum . '" class="player"><div id="p' . $playerNum . 'c1" class="' . $tintColor . 'Tint">' . $playerHand[1] . '</div>';
    if (substr_count($playerHand[2],"hearts")>0 || substr_count($playerHand[2],"diams")>0) { $tintColor="red"; } else { $tintColor="black"; }
    $hands.='<div id="p' . $playerNum . 'c2" class="' . $tintColor . 'Tint">' . $playerHand[2] . '</div></div>';
    // ^^ Im laughing inside but it really isnt funny o.O
    
    // Set up the string in a way that the parser can read it
    $tempStr1=str_replace($badASuits,$goodASuits, $playerHand[1]);
    $tempStr2=str_replace($badASuits,$goodASuits, $playerHand[2]);
    $tempStrJ=$tempStr1 . "," . $tempStr2;
    $tempStrJ=str_replace($badANums,$goodANums,$tempStrJ);
    $output[$playerNum]=$tempStrJ . "," . substr($flopString,0,-1); // <--- this array is holding the players cards inc flop etc. in a format that the parser can understand
}
// END

foreach ($output as $handsStr) {
    $dataDrop.=$handsStr . "|";
}
$dataDrop=substr($dataDrop,0,-1);


// HTML up The flop cards
$flopString="";
foreach ($flop as $index=>$card) {
    if (substr_count($card,"hearts")>0 || substr_count($card,"diams")>0) { $tintColor="red"; } else { $tintColor="black"; }
    $flopString.='<div id="fc' . $index . '" class="flopCard ' . $tintColor . 'Tint">' . $card . '</div>';
}
$flopString='<div id="flopContainer">' . $flopString . '</div>';
// END

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script type="text/javascript" src="js/jquery172.js"></script>
    <script type="text/javascript" src="js/jqueryui.js"></script>
    <style type="text/css">
        body { background-color: #072907; }
	#hands { font-size: 26px; position: absolute; left: 20px; top: 80px; }
        
        .redTint { color: red; }
        .blackTint { color: black; }
        .redTint, .blackTint { background-color: white; border: 1px solid black; border-bottom: 0px solid black; position: relative; float: left; width: 80px; height: 40px; padding-left: 5px; text-align: left; padding-right: 3px; }
        #flopContainer { clear: both; position: absolute; left: 50px; top: 10px; width: 600px; text-shadow: 1px 1px 0px #ccc; }
        .flopCard { position: relative; float: left; }
        .player { clear: left; color: #DCE060; float: left; font-size: 26px; margin-right: 10px; margin-top: 10px; position: relative; text-shadow: 1px 1px 0px #ccc; }
        #dataDrop { width:463px; height: 10px; font-size: 10px; border: 1px solid #CCCCCC; position: absolute; left: 100px; top: 70px; overflow: auto; display: <?php echo $display; ?>; font-size: 3px; }
        #results { font-size: 10px; position: absolute; left: 700px; top: 150px; display: none; }
        #player1, #player2, #player3, #player4, #player5, #player6, #player7, #player8, #player9, #player10, #player11 { position: absolute; }
        .smallText { font-size: 11px; text-shadow: 0px 0px 1px #000000; }
        
        #player1, #player6 { top: 60px; }
        #player2, #player5 { top: 30px; }
        #player3, #player4 { top: 0px;}
        
        #player7            { top: 280px; }
        #player8, #player11 { top: 310px; }
        #player9, #player10 { top: 340px; }
        
        #player1            { left: 20px; }
        #player2, #player11 { left: 120px; }
        #player3, #player10 { left: 220px; }
        #player4, #player9  { left: 320px; }
        #player5, #player8  { left: 420px; }
        #player6, #player7  { left: 520px; }
        
        #fc2, #fc3 { margin-left: 1px; }
        #fc4, #fc5 { margin-left: 7px; }
        
        .blueS { background-color: #263F78; color : yellow; clear: left;}
        .blue { display: <?php echo $display; ?>; background-color: #800000; color : #FFFFFF; clear: left; border-bottom: 1px solid black; text-shadow: 2px 0px 2px #ccc; }
        .white { color: white; }
        
        #wins {
            -moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
            -webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
            box-shadow:inset 0px 1px 0px 0px #ffffff;
            background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ededed), color-stop(1, #dfdfdf) );
            background:-moz-linear-gradient( center top, #ededed 5%, #dfdfdf 100% );
            filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ededed', endColorstr='#dfdfdf');
            text-shadow: 1px 1px 0px #ccc;
            width: 150px;
            position: absolute;
            border: 1px solid black;
            left: 660px; top: 150px;
            text-align: center;
            padding-bottom: 12px;
            padding-top: 6px;
            text-shadow: 3px 3px 3px black;
            font-variant: small-caps;
        }
        #wins { box-shadow: 0px 0px 20px 0 #61CF67; cursor: move; }
        #headerWins { font-weight: bold; font-size: 24px; padding-bottom: 10px; color: white; }
        #showWorking, #refresh { background-color: white; text-align: center; cursor: pointer; position: absolute; z-index:5; font-size: 12px; top: 350px; left: 280px; width: 100px; height: 18px; border: 1px solid #ccc; padding-top: 2px; text-shadow: 1px 1px 1px #ccc; font-variant: small-caps; }
        #refresh { top: 332px; left: 280px; }
        #playersContainer { background-color: white; position: absolute; left: 280px; top: 371px; text-align: center; border: 1px solid #ccc; }
        #removePlayer, #addPlayer, #plNum { position: relative; float: left;}
        #removePlayer, #addPlayer { cursor: pointer; width: 20px; }
        #plNum { cursor: default; width: 60px; }
        #dataDrop { display: <?php echo $display; ?> }
    </style>
</head>    

<body>
    <div id="container">
        <div id="hands">This will be filled using jQuery, so you shouldnt be able to see this message unless something serious has gone wrong. Who has eyes on North Korea? Anyone? :)</div>
        <div id="flopContainer"><?php echo $flopString; ?></div>
        <div id="dataDrop"><?php echo $dataDrop; ?></div>
        <div id="results"></div>
        <div id="wins"><div id="headerWins">Winning Hands</div>High Card<br/>One Pair<br/>Two Pair<br/>Three of a Kind<br/>Straight<br/>Flush<br/>Full House<br/>Four of a kind<br/>Straight Flush<br/>Royal Flush</div>
        <div id="refresh">Next Hand</div>
        <div id="showWorking">Show Working</div>
        <div id="playersContainer"><div id="removePlayer">-</div><div id="plNum"><?php echo $players; ?></div><div id="addPlayer">+</div></div>
    </div>
</body>

<script type="text/javascript">
    $("#hands").html("<?php
        echo str_replace('"','\\"',$hands);
    ?>");
    $('#results').load('ajax/deckInspector.php?handString=<?php echo $dataDrop; ?>', function() {
        resultArray=$('#results').html().split("|");
        
        for (i=0;i<(resultArray.length-1);i++) {
            $("#player" + (parseInt(i)+1)).append('<div id="player' + parseInt(i) + 'hand' + '" class="smallText">' + resultArray[i] + '</div>');
        }
    })
    $("#refresh").click(function() {
        location.reload();
    })
    $("#showWorking").click(function() {
        if ($(".blue").css("display")=="none" || $("#dataDrop").css("display")=="none") {
            $(".blue").css("display", "block");
            $("#dataDrop").css("display", "block");
            $.get("setUpCookies.php?working=1");
        } else {
            $(".blue").css("display", "none");
            $("#dataDrop").css("display", "none");
            $.get("setUpCookies.php?working=0");
        }
    })
    $("#removePlayer").click(function() {
        var plNum;
        plNum=parseInt($("#plNum").html());
        if (plNum>2) {
            plNum--;
            $("#plNum").html(plNum);
            $.get("setUpCookies.php?players=" + plNum, function(data) {
                if (data=="refresh") {
                    location.reload();
                }
            });
        }
    })
    $("#addPlayer").click(function() {
        var plNum;
        plNum=parseInt($("#plNum").html());
        if (plNum<11) {
            plNum++;
            $("#plNum").html(plNum);
            $.get("setUpCookies.php?players=" + plNum, function(data) {
                if (data=="refresh") {
                    location.reload();
                }
            });
        }
    })
    $("#wins").draggable();
</script>

</html>