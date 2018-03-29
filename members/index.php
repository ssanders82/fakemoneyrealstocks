<?
require("../includes/commonfuncs.php");
$page = new PageView;
$page->isMemberPage = true;
$page->GetPageData();
$lookingAtMember = $page->lookingAtMember;
$signedMember = $page->signedMember;

$stats = isset($_GET['stats']) ? $_GET['stats'] : "";
if ($stats == "realtime") $titleText = "Real-time trades";
elseif ($stats == "year") $titleText = "Year-by-year";
else $titleText = "Profile"; 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>FakeMoneyRealStocks.com :: <? echo $lookingAtMember->name ?></title>
<link rel = "stylesheet" href="/styles/default.css" type="text/css">


</head>

<? require("../includes/make_header.php") ?>

<div id="content">
	<h2 class="title"><? echo $lookingAtMember->name . ": " . $titleText ?></h2>
    <!-- 
    <div style="width:98%;text-align:center;font-weight:bold">
    <a href="<? echo $lookingAtMember->memberUrl ?>/index?stats=profile">profile</a><| 
    <a href="<? echo $lookingAtMember->memberUrl ?>/index?stats=year">year-by-year</a> 
    <a href="<? echo $lookingAtMember->memberUrl ?>/index?stats=realtime">real-time trades</a>
    </div>
    -->
    <br>
    <?
    if ($stats == "year") require_once("inc_year.php");
    elseif ($stats == "realtime") require_once("inc_realtime.php");
    else require_once("inc_profile.php");
    ?>
    
	<br><br>
    <a href="/index.php">Trade!</a>

</div>

<? require("../includes/make_footer.php") ?>