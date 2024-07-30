<?php
/*==========> INFO 
 * CODE     : BY ZLAXTERT
 * SCRIPT   : VALIDATOR APPLE
 * VERSION  : 1
 * TELEGRAM : t.me/zlaxtert
 * BY       : DARKXCODE
 */

require_once "function/function.php";
require_once "function/settings.php";

echo banner();
echo banner2();
enterlist:
echo "\n\n$WH [$BL+$WH]$BL Enter your list $WH($DEF eg:$YL list.txt$WH )$GR >> $WH";
$listname = trim(fgets(STDIN));
if (empty($listname) || !file_exists($listname)) {
    echo " [!] Your Fucking list not found [!]" . PHP_EOL;
    goto enterlist;
}
$lists = array_unique(explode("\n", str_replace("\r", "", file_get_contents($listname))));

$total = count($lists);
$live = 0;
$die = 0;
$rto = 0;
$unknown = 0;
$limit = 0;
$no = 0;
echo "\n\n$WH [$YL!$WH] TOTAL $GR$total$WH LISTS [$YL!$WH]$DEF\n\n";

foreach ($lists as $list) {
    $no++;
    // GET SETTINGS
    if (strtolower($mode_proxy) == "off") {
        $Proxies = "";
        $proxy_Auth = $proxy_pwd;
        $type_proxy = $proxy_type;
        $apikey = GetApikey($thisApikey);
        $APIs = GetApiS($thisApi);
    } else {
        $Proxies = GetProxy($proxy_list);
        $proxy_Auth = $proxy_pwd;
        $type_proxy = $proxy_type;
        $apikey = GetApikey($thisApikey);
        $APIs = GetApiS($thisApi);
    }


    // EXPLODE
    $email = multiexplode(array(":", "|", "/", ";", ""), $list)[0];
    $pass = multiexplode(array(":", "|", "/", ";", ""), $list)[1];
    //API
    $api = $APIs."/validator/apple/?list=$email&proxy=$Proxies&proxyAuth=$proxy_Auth&type_proxy=$type_proxy&apikey=$apikey";
    // CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $x = curl_exec($ch);
    curl_close($ch);
    $js = json_decode($x, TRUE);
    $msg = $js['data']['msg'];
    $jam = Jam();

    if(strpos($x, '"status":"live"')){
        $live++;
        save_file("result/live.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$GR LIVE$DEF =>$BL $email$DEF | [$YL MSG$DEF: $WH$msg$DEF ] | BY$CY DARKXCODE$DEF (V1)".PHP_EOL;
    }else if (strpos($x, '"msg":"Incorrect APIkey!"')){
    
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$RD DIE$DEF =>$BL $email$DEF | [$YL MSG$DEF:$MG Incorrect APIkey!$DEF ] | BY$CY DARKXCODE$DEF (V1)".PHP_EOL;
    
    }else if (strpos($x, '"status":"die"')){
        $die++;
        save_file("result/die.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$RD DIE$DEF =>$BL $email$DEF | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (V1)".PHP_EOL;
    }else if(strpos($x, '"status":"unknown"')){
        $limit++;
        save_file("result/limit.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$CY LIMIT$DEF =>$BL $email$DEF | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (V1)".PHP_EOL;
    }else if($x == ""){
        $rto++;
        save_file("result/RTO.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$DEF TIMEOUT$DEF =>$BL $email$DEF | [$YL MSG$DEF:$MG REQUEST TIMEOUT!$DEF ] | BY$CY DARKXCODE$DEF (V1)".PHP_EOL;
    }else if(strpos($x, 'Request Timeout')){
        $rto++;
        save_file("result/RTO.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$DEF TIMEOUT$DEF =>$BL $email$DEF | [$YL MSG$DEF:$MG REQUEST TIMEOUT!$DEF ] | BY$CY DARKXCODE$DEF (V1)".PHP_EOL;
    }else if(strpos($x, 'Service Unavailable')){
        $rto++;
        save_file("result/RTO.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$DEF TIMEOUT$DEF =>$BL $email$DEF | [$YL MSG$DEF:$MG REQUEST TIMEOUT!$DEF ] | BY$CY DARKXCODE$DEF (V1)".PHP_EOL;
    }else{
        $unknown++;
        save_file("result/unknown.txt","$list");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$YL UNKNOWN$DEF =>$BL $email$DEF | BY$CY DARKXCODE$DEF (V1)".PHP_EOL;
    }

}
 //============> END
 
 echo PHP_EOL;
 echo "================[DONE]================".PHP_EOL;
 echo " DATE          : ".$date.PHP_EOL;
 echo " LIVE          : ".$live.PHP_EOL;
 echo " DIE           : ".$die.PHP_EOL;
 echo " TIMEOUT       : ".$rto.PHP_EOL;
 echo " UNKNOWN       : ".$unknown.PHP_EOL;
 echo " TOTAL         : ".$total.PHP_EOL;
 echo "======================================".PHP_EOL;
 echo "[+] RATIO VALID => $GR".round(RatioCheck($live, $total))."%$DEF".PHP_EOL.PHP_EOL;
 echo "[!] NOTE : CHECK AGAIN FILE 'unknown.txt' or 'RTO.txt' [!]".PHP_EOL;
 echo "This file '".$listname."'".PHP_EOL;
 echo "File saved in folder 'result/' ".PHP_EOL.PHP_EOL;


// ==========> FUNCTION

function collorLine($col)
{
    $data = array(
        "GR" => "\e[32;1m",
        "RD" => "\e[31;1m",
        "BL" => "\e[34;1m",
        "YL" => "\e[33;1m",
        "CY" => "\e[36;1m",
        "MG" => "\e[35;1m",
        "WH" => "\e[37;1m",
        "DEF" => "\e[0m"
    );
    $collor = $data[$col];
    return $collor;
}
?>