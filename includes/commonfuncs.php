<?php

clearstatcache();
//error_reporting(E_ALL);
//ini_set('error_reporting', E_ALL);
// Compact privacy policy for IE third party cookies (widget login). DO NOT REMOVE
header('P3P: CP="NOI DSP CURa ADMa DEVa TAIa OUR BUS IND UNI COM NAV INT"');

//ini_set('display_startup_errors', "1");
if (isset($_GET['debug'])) define("DEBUG_LEVEL", "debug");
else define("DEBUG_LEVEL", "production");

require_once('passwords.php');
define("ADMIN_EMAIL", "info@fakemoneyrealstocks.com");
//define("ADMIN_EMAIL", "ssanders82@gmail.com");
define("HOURS_FROM_GMT", 5);
define("USER_AGENT", "fakemoneyrealstocks.com");
define("DATE_BLANK", "0000-00-00 00:00:00");
define("ENCRYPTION_KEY", "adccRD_$3356");
define("MIN_PASSWORD_LENGTH", 4);

//define("PAYPAL_PREMIUM_LINK", "https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=T9HFG6S2469F2");
define("PAYPAL_PREMIUM_LINK", "http://www.fakemoneyrealstocks.com/startpaypal.php");

if (!defined("CHART_IMAGE_HEIGHT")) define("CHART_IMAGE_HEIGHT", "330");
if (!defined("CHART_IMAGE_WIDTH")) define("CHART_IMAGE_WIDTH", "510");

define("SITE_DOMAIN", "http://" . $_SERVER["HTTP_HOST"] . "/");
define("SUB_DOMAIN", strtolower(str_replace(".fakemoneyrealstocks.com", "", $_SERVER["HTTP_HOST"])));
define("IS_WIDGET", SUB_DOMAIN == "widget" || SUB_DOMAIN == "widgetdev");
define("CHART_SERVER", "http://146.71.76.173:84/");
define("HOME_DIR", dirname(__FILE__) . "/");
define("CHARTS_FOLDER", "charts/");
define("WEEKLY_LEADERBOARD_MAX_TRADES", 25);

date_default_timezone_set('America/New_York');
require_once('class.phpmailer.php');
require_once 'classStockDate.php';
require_once 'classGroup.php';
require_once 'classDailyData.php';
require_once 'classDatabaseAccess.php';
require_once 'classMember.php';
require_once 'classTrade.php';
require_once 'classStock.php';
require_once 'classPageView.php';
session_start();
$page = new PageView;
StartPage($page); // THIS RUNS ON ALL PAGES

define("IS_ADMIN", $page->signedMember && $page->signedMember->name == "fakemoneyrealstocks");

if (SUB_DOMAIN == "www" && !IS_ADMIN)
{
    //set_error_handler("MyErrorHandler", E_ALL & ~E_NOTICE); 
    set_error_handler("MyErrorHandler", E_ALL);
}

////////////////////////////////////////////////////////////////
// Common functions for all pages
////////////////////////////////////////////////////////////////

function MyErrorHandler($errno, $errstr, $errfile, $errline)
{
	$errorstring = "Error [$errno] $errstr<br>Error on line $errline in file $errfile<br>";
	$errorstring .= "Time: " . date('l jS \of F Y h:i:s A') . "<br>";
	ob_start();
    phpinfo();
    $errorstring .= ob_get_contents();
    ob_end_clean();
	SendEmail(ADMIN_EMAIL, "FakeMoneyRealStocks.com error", $errorstring);
	try
	{
		Redirect("http://www.FakeMoneyRealStocks.com/apperror.php");
	}
	catch (Exception $e)
	{
		echo "<b>Error processing file.</b>";
		die();
	}
    /* Don't execute PHP internal error handler */
    return true;
}

// http://stackoverflow.com/questions/4329260/cross-platform-php-to-c-sharp-net-encryption-decryption-with-rijndael
function mc_encrypt($text)
{
    // to append string with trailing characters as for PKCS7 padding scheme
    $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $padding = $block - (strlen($text) % $block);
    $text .= str_repeat(chr($padding), $padding);

    $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, ENCRYPT_KEY, $text, MCRYPT_MODE_CBC, ENCRYPT_IV);
    //return $crypttext;
    
    $crypttext64=base64_encode($crypttext);
    return $crypttext64;
}

function mc_decrypt($decrypt)
{
    $decoded = base64_decode($decrypt);
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    mcrypt_generic_init($td, ENCRYPT_IV, ENCRYPT_KEY);
    $decrypted = mdecrypt_generic($td, $decoded);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return trim($decrypted);
}

function GetUrlBool($var)
{
    return isset($_GET[$var]) && $_GET[$var];
}

function StartPage($page)
{
    DispelMagicQuotes();
    $page->GetPageData();
    //Debug($page);
}

function DispelMagicQuotes() 
{ 
    if (ini_get('magic_quotes_gpc')) 
    { 
        foreach (array('_GET', '_POST', '_COOKIE') as $super) 
        { 
            foreach ($GLOBALS[$super] as $k => $v) 
            { 
                $GLOBALS[$super][$k] = stripslashes_r($v); 
            } 
        } 
    } 
} 

function GetFirstGroup($member)
{
    if ($member && $member->defaultGroupID)
    {
    	$group = new Group($member->defaultGroupID);
    }
    else
    {
        $email = $member ? $member->emailAddress : "";
        $code = GetAuthCode();
    	if (!$email && !$code) return false;
    	
    	$results = DatabaseAccess::Select(
    		"SELECT * FROM GroupUser WHERE (UserEmail=:email OR (AuthCode=:code AND AuthCode<>'') ) ORDER BY GroupUserID", 
    		array(':email' => $email, ':code' => $code) );
    	if (count($results) == 0) return false;
    	$group = new Group($results[0]->GroupID);
    }
    
    return $group;
}

function GetAuthCode()
{
	$code = isset($arrQS['authCode']) ? $arrQS['authCode'] : "";
    if (!$code && isset($_SESSION['authCode'])) $code = $_SESSION['authCode'];
    if (!$code && isset($_GET['authCode'])) $code = $_GET['authCode'];
    return $code;
}

function ResetAccount($member)
{
	$account = 100000;
	$_SESSION['AccountValue'] = $account;
    if ($member)
    {
        $member->ClearTrades();
        $member->UpdateDBValue("HasReset", "1");
	}
    $_SESSION['sessionNumTrades'] = 0;
    $_SESSION['sessionNumWinners'] = 0;
    $_SESSION['sessionProfit'] = 0;
}

function GetSessionTrade()
{
	if (!isset($_SESSION['session_trade'])) return false;
    $trade = unserialize($_SESSION['session_trade']);
    return $trade;
}

function GetSessionAdvTrade()
{
	if (!isset($_SESSION['session_adv_trade'])) return false;
    $trade = unserialize($_SESSION['session_adv_trade']);
    return $trade;
}

function SaveSessionTrade()
{
}

function GetCurrentAccountValue($signedMember)
{
	if ($signedMember) $account = $signedMember->accountValue;
	else $account = isset($_SESSION['AccountValue']) ? $_SESSION['AccountValue'] : 100000;
    
    if ($account == 0) $account = 100000;
    return $account;
}

function GetWeeklyAccountValue($signedMember)
{
	if ($signedMember) return $signedMember->weeklyAccount;
	return is_numeric($_SESSION['AccountValue']) ? $_SESSION['AccountValue'] : 100000;
}

function GetWeeklyNumTrades($signedMember)
{
    if ($signedMember) return $signedMember->weeklyNumTrades;
    return is_numeric($_SESSION['sessionNumTrades']) ? $_SESSION['sessionNumTrades'] : 0;
}

function FormatCurrencySide($amount, $includeColor = true)
{
	//$amount = -4500000000000;
	if ($amount == 0) $color = "#444444";
    else if ($amount > 0) $color = "green";
    else $color = "red";
    if (!$includeColor) $color = "";
    $abs = abs($amount);
    
    $size = log10($abs);
    if ($size < 6) $num = number_format($amount,0,".",",");
    else if ($size < 9)  $num = number_format($amount/1000000,2,".", ",") . " million";
    else if ($size < 12) $num = number_format($amount/1000000000,2,".", ",") . " billion";
    else if ($size < 15) $num = number_format($amount/1000000000000,2,".", ",") . " trillion";
    else if ($size < 18) $num = number_format($amount/1000000000000000,2,".", ",") . " quadrillion";
    else if ($size < 21) $num = number_format($amount/1000000000000000000,2,".", ",") . " quintillion";
    else if ($size < 24) $num = number_format($amount/1000000000000000000000,2,".", ",") . " sextillion";
	else if ($size < 27) $num = number_format($amount/1000000000000000000000000,2,".", ",") . " septillion";
	else if ($size < 30) $num = number_format($amount/1000000000000000000000000000,2,".", ",") . " octillion";
    else $num = $amount;
    //return $num;
    $text = "<span style=\"color:{$color}\">$" . $num . "</span>";
    return $text;
}

/** 
* Recursive stripslashes. array_walk_recursive seems to have great trouble with stripslashes(). 
* 
* @param  mixed $str String or array 
* @return mixed      String or array with slashes removed 
*/ 
function stripslashes_r($str) 
{ 
    if (is_array($str)) 
    { 
        foreach ($str as $k => $v) 
        { 
            $str[$k] = stripslashes_r($v); 
        }      
        return $str;
    } 
    else 
    { 
        return stripslashes($str); 
    } 
}
////////////////////////////////////////////


function IsValidUsername($string)
{
    if (strlen($string) == 0) return false;
    
    if (strlen($string) > 30) return false;
    // Don't let it be all numbers
    if (preg_match("/^[0-9]+$/i", $string)) return false;
    // Allow alphanumeric, plus _ and -
    return !preg_match("/[^a-z0-9_\-]+/i", $string);
    //return (strpos($string, " ") === false);
}

function IsUsernameProfane($string)
{
    $string = strtolower($string);
    $arrProfane = array("ass","ass lick","asses","asshole","assholes","asskisser","asswipe","balls","bastard","beastial",
    "beastiality","beastility","beaver",
    "belly whacker","bestial","bestiality","bitch","bitcher","bitchers","bitches","bitchin","bitching","blowjob","blowjob",
    "blowjobs","bonehead","boner","brown eye","browneye","browntown","bucket cunt","bull shit","bullshit","bum","bung hole",
    "butch","butt","butt breath","butt fucker","butt hair","buttface","buttfuck","buttfucker","butthead","butthole","buttpicker",
    "chink","christ","circle jerk","clam","clit","cobia","cock","cocks","cocksuck","cocksucked","cocksucker","cocksucking",
    "cocksucks","cooter","crap","cum",
    "cummer","cumming","cums","cumshot","cunilingus","cunillingus","cunnilingus","cunt","cuntlick","cuntlicker","cuntlicking",
    "cunts","cyberfuc","cyberfuck","cyberfucked","cyberfucker","cyberfuckers","cyberfucking","damn","dick","dike","dildo",
    "dildos","dink","dinks","dipshit","dong","douche bag","dumbass","dyke","ejaculate","ejaculated","ejaculates","ejaculating",
    "ejaculatings","ejaculation","fag","fagget","fagging","faggit","faggot","faggs","fagot","fagots","fags","fart","farted",
    "farting","fartings","farts","farty","fatass","fatso","felatio","fellatio","fingerfuck","fingerfucked","fingerfucker",
    "fingerfuckers","fingerfucking","fingerfucks","fistfuck","fistfucked","fistfucker","fistfuckers","fistfucking",
    "fistfuckings","fistfucks","fuck","fucked","fucker","fuckers","fuckin","fucking","fuckings","fuckme","fucks",
    "fuk","fuks","furburger","gangbang","gangbanged","gangbangs","gaysex","gazongers","goddamn","gonads","gook",
    "guinne","hard on","hardcoresex","hell","homo","hooker","horniest","horny","hotsex","hussy","jack off","jackass",
    "jacking off","jackoff","jack-off","jap","jerk","jerk-off","jesus","jesus christ","jew","jism","jiz","jizm",
    "jizz","kike","knob","kock","kondum","kondums","kraut","kum","kummer","kumming","kums","kunilingus","lesbian",
    "lesbo","loser","lust","lusting","merde","mick","mothafuck","mothafucka","mothafuckas","mothafuckaz",
    "mothafucked","mothafuckr","mothafuckers","mothafuckin","mothafucking","mothafuckings","mothafucks",
    "motherfuck","motherfucked","motherfucker","motherfuckers","motherfuckin","motherfucking","motherfuckings",
    "motherfucks","mound","muff","nerd","nigger","niggers","orgasim","orgasims","orgasm","orgasms","pecker",
    "penis","phonesex","phuk","phuked","phuking","phukked","phukking","phuks","phuq","pimp","piss","pissed",
    "pisser","pissers","pisses","pissin","pissing","pissoff","porn","porno","pornography","pornos","prick",
    "pricks","prostitute","punk","pussies","pussy","pussys","queer","retard","schlong","screw","sheister",
    "shit","shited","shitfull","shiting","shitings","shits","shitted","shitter","shitters","shitting",
    "shittings","shitty","slag","sleaze","slut","sluts","smut","snatch","spunk","twat","wetback","whore","wop");
    foreach ($arrProfane as $profane)
    {
        if(strpos($string, $profane) !== false) return true;
    }
    return false;
}

function IsValidPassword($string)
{
    if (strlen($string) < MIN_PASSWORD_LENGTH) return false;
    if (strpos($string, " ") !== false) return false;
    return true;
    /* return preg_match("/^[^ ]$/i", $string); */
}

function GetTopMembers()
{
	$results = DatabaseAccess::Select("select * from Member order by DateAdded desc limit 0,10" );
    $topUsers = array();
    foreach ($results as $row)
    {
    	$member = new Member($row->MemberID);
        $name = $row->Name;
        if (strlen($name) > 20) $name = substr($name,0,20);
        //$compound = 100* (pow((100000 + $row['Profit']) , 1 / ($row['DaysInMarket']/250) ) - 1);
    	$topUsers[] = "<a href='{$member->memberUrl}'>{$name}</a> <nobr>(" . FormatCurrency($row->AccountValue) . ")</nobr>";
    }
    return $topUsers;
}

function AddDaysToDate($date, $daysToAdd)
{
	$time = strtotime($date);
    $newTime = AddDaysToTime($time, $daysToAdd); 
    return ConvertTimestampToMySqlDate($newTime);
}

function AddDaysToTime($time, $daysToAdd)
{ 
    $newTime = $time + ($daysToAdd * 60*60*24);     
    return $newTime;
}

function FormatCurrency($string, $decimals = 0)
{
    return "$" . number_format($string, $decimals, ".", ",");
}

function FormatRating($rating, $numRatings=0)
{
    if ($numRatings == 0) return  "----";
    return number_format($rating, 0) . "%";
}

/////////////////////////////////////////////

function GetThisTime()
{
    $thisTime = mktime(gmdate("H") - HOURS_FROM_GMT, gmdate("i"), gmdate("s"), gmdate("m"), gmdate("d"), gmdate("Y"));
    //return time();
    return $thisTime;
}

function MySqlDate($time)
{
    return ConvertTimestampToMySqlDate($time);
}

function ConvertTimestampToMySqlDate($time)
{
    return date("Y-m-d H:i:s",$time);
}

function GetNumberText($number, $text)
{
    if ( $number == 1 ) return "1 $text";
    else return "{$number} {$text}s";
}

function GetFormValue($key, $isNumeric = false, $defaultValue = "")
{
    if ($isNumeric)
    {
        $value = isset($_GET[$key]) && is_numeric(trim($_GET[$key])) ? trim($_GET[$key]) : $defaultValue;
    }
    else $value = isset($_GET[$key]) ? $_GET[$key] : $defaultValue;
    return $value;
} 

function Redirect($url)
{
    header("location: $url");
    exit;
}


/////////////////////////////////////////////

function IsValidEmailAddress($string)
{
    if(strlen($string) == 0) return false;
    $pattern = "/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i";
    return preg_match($pattern, $string);
    //return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $string);
}

/////////////////////////////////////////////

function CleanURL($string)
{
    if ( strlen($string) === 0) return "";
    if ( strpos($string, "http://") === 0) return $string;
    if ( strpos($string, "https://") === 0) return $string;
    if ( strpos($string, "www.") === 0) return "http://" . $string;
    return "http://" . $string;
}

/////////////////////////////////////////////

function PrintCurrency($string)
{
    return "$" . number_format($string, 0, ".", ",");
}

function PrintDate($string, $sameYearFormat = "l, M. j \a\\t g:i:s a", $diffYearFormat = "l, M. j, 'y \a\\t g:i:s a")
{
    $timestamp = strtotime($string);
    $currentTime = GetThisTime();
    $format = (date("Y", $timestamp) != date("Y", $currentTime)) ? $diffYearFormat : $sameYearFormat;
    return date($format, $timestamp);
}

function PrintShortDate($string)
{
    return PrintDate($string, "l, M. j", "l, M. j, 'y");
}

function PrintVeryShortDate($string)
{
    return PrintDate($string, "M. j", "M. j, 'y");
}

function Encrypt($string)
{
	$key = ENCRYPTION_KEY;
    $result = '';
    for($i=1; $i<=strlen($string); $i++)
    {
        $char = substr($string, $i-1, 1);
        $keychar = substr($key, ($i % strlen($key))-1, 1);
        $char = chr(ord($char)+ord($keychar));
        $result.=$char;
    }
	return base64_encode($result);
}

function Decrypt($string)
{
	$string = base64_decode($string);
	$key = ENCRYPTION_KEY;
    $result = '';
    for($i=1; $i<=strlen($string); $i++)
    {
        $char = substr($string, $i-1, 1);
        $keychar = substr($key, ($i % strlen($key))-1, 1);
        $char = chr(ord($char)-ord($keychar));
        $result.=$char;
    }
    return $result;
}

////////////////////////////////////////////

function CatchError($errorText)
{
    echo "ERROR $errorText";
    exit;
    //$thisDate = ConvertTimestampToMySqlDate(GetThisTime());
    //$sql = "INSERT INTO ProcessingError (ErrorText,ErrorDate) VALUES ('" . $errorText . "','$thisDate')";
}

// Use curl to retrieve a page.
function HttpGet($url, $cleanForRegExp = false, $doGetInfo = false)
{
    //return file_get_contents($url);
    $url = urldecode($url);
    $result = "";
    $ch = curl_init();
    if (!$ch) return false;
    // Follow redirects
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies/~");

    $result = curl_exec($ch);
    if ($cleanForRegExp) $result = CleanForRegExp($result);
    
    if ($doGetInfo)
    {
        $return = array();
        $return['Results'] = $result;
        $return['Info'] = curl_getinfo($ch);
    }
    else
    {
        $return = $result;
    }
    curl_close($ch);
    return $return;
}

////////////////////////////////////////////



function AddQueryParameter($querystring, $key, $value, $isArray = false)
{
    $isEntireUrl = false;
    // Get only querystring if we passed in the entire url
    if (strpos($querystring, "?") !== false || strpos($querystring, "http") === 0)
    {
        $arrTmp = parse_url($querystring);
        $querystring = $arrTmp['query'];
        $start = "{$arrTmp['scheme']}://{$arrTmp['host']}{$arrTmp['path']}";
        $isEntireUrl = true;
    }
    parse_str($querystring, $arr);
    if (!$isArray)
    {
        $arr[$key] = $value;
    }
    else
    {
        if (!isset($arr[$key])) $arr[$key] = array();
        if (!in_array($value, $arr[$key])) $arr[$key][] = $value;
    } 
    $querystring = http_build_query_custom($arr);
    if ($isEntireUrl) return "{$start}?{$querystring}";
    else return $querystring;
}

function RemoveQueryParameter($querystring, $key, $key2 = "")
{
    // Pass key2 as value to remove from array
    parse_str($querystring, $arr);
    if ($key2 && is_array($arr[$key]))
    {
        $index = array_search($key2, $arr[$key]);
        if ($index !== false) unset($arr[$key][$index]);
    }
    else
    {
        if (isset($arr[$key])) unset($arr[$key]);
    }
    return http_build_query_custom($arr);
}

function http_build_query_custom($arrData, $key='') 
{
    $ret = array();
    foreach ((array)$arrData as $k => $v) 
    {
        if (!empty($key)) $k = $key.'['.http_build_query_custom_encode($k).']';
       
        if (is_array($v)) array_push($ret, http_build_query_custom($v, $k));
        else array_push($ret, $k.'='.http_build_query_custom_encode($v));
    }
    if (empty($sep)) $sep = ini_get('arg_separator.output');
    return implode($sep, $ret);
}

function http_build_query_custom_encode($v)
{
    return $v; // urlencode($v);
}


function Debug($data, $abort = true)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    if ($abort) exit;
}

function UnHtmlEntities($string)
{
    // replace numeric entities
    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
    $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
    // replace literal entities
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($string, $trans_tbl);
}

function GetClosestMatch($searchText, $arrToSearch, $minDistance = 40)
{
    $smp = metaphone($searchText);
    $minIndex = -1;
    $minPer = -1;
    foreach ($arrToSearch as $index => $needleWord) 
    {    
        $nmp = metaphone ($needleWord);
        $distance = levenshtein ($smp, $nmp);
        $n_len = strlen($nmp);
        $per = round(($distance/$n_len) * 100);
        echo "Distance to index $index is $per<br>\n";
        if ($per < $minDistance && ($per < $minPer || $minPer == -1))
        {
            $minPer = $per;
            $minIndex = $index;
        }
    }
    return $minIndex;
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function SendEmail($to, $subject, $body)
{
    if (strlen($to) == 0) return false;

    $mail             = new PHPMailer();    
    $mail->IsSMTP(); // telling the class to use SMTP
    //$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
                                               // 1 = errors and messages
                                               // 2 = messages only
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    $mail->Host       = "mail.fakemoneyrealstocks.com"; // sets the SMTP server
    $mail->Port       = 25;                    // set the SMTP port for the GMAIL server
    $mail->Username   = "info@fakemoneyrealstocks.com"; // SMTP account username
    $mail->Password   = "sam417";        // SMTP account password
    
    $mail->SetFrom(ADMIN_EMAIL, ADMIN_EMAIL);
    $mail->AddReplyTo(ADMIN_EMAIL, ADMIN_EMAIL);
    $mail->Subject    = $subject;
    $mail->AltBody    = $body;
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $to);
    if ($to != ADMIN_EMAIL) $mail->addBCC(ADMIN_EMAIL, ADMIN_EMAIL); 
    //$mail->AddAttachment("images/phpmailer.gif");      // attachment
    //$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
    
    if(!$mail->Send()) {
      //echo "Mailer Error: " . $mail->ErrorInfo;
      // TODO
    }
}

/*function SortObjects(&$arrObjects, $fieldToSort)
{
    $arrKeys = array_keys($arrObjects);
    for ($i = count($arrKeys) - 1; $i >= 0; $i--)
    {
        $swapped = false;
        for ($j = 0; $j < $i; $j++)
        {
            $key = $arrKeys[$j];
            echo "key is $key, comparing " . $arrObjects[$key]->$fieldToSort . " to  "  . $arrObjects[$key + 1]->$fieldToSort . "<br>\n";
            if ($arrObjects[$key]->$fieldToSort > $arrObjects[$key + 1]->$fieldToSort)
            {
                $tmp = $arrObjects[$key];
                $arrObjects[$key] = $arrObjects[$key + 1];       
                $arrObjects[$key + 1] = $tmp;
                $swapped = true;
            }
        }
        if (!$swapped) return;
    }
}*/

/////////////////////////////////////////////

function CheckReferrer() {
   //$referrer = $_SERVER['HTTP_REFERER'];
	 //if (strpos($referrer, "http://www.foothillshousing.com")!=0 && strpos($referrer, "http://foothillshousing.com")!=0) {
	 //   header("location: mypage_error.php");
	 //   exit;
	 //}
} // end function



