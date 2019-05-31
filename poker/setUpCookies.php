<?php
ini_set("display_errors",0);

if ($_GET["working"]==1) {
    $working=$_GET["working"];
    if ($working==1) {
        setcookie("showWorking", 1, time()+(60*60*24*28));
    }
} else {
    setcookie("showWorking", 0, time()-(60*60*24));
}

if ($_GET["players"]>1 && $_GET["players"]<12) {
    setcookie("players", $_GET["players"], time()+(60*60*24*28));
    echo "refresh";
}
?>