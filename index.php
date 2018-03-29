<?
require("includes/commonfuncs.php");
$account = GetCurrentAccountValue($page->signedMember);

if (isset($_GET['reset']))
{
	$account = 100000;
    ResetAccount($page->signedMember);
}

$boss = isset($_GET['boss']);
$chartOptions = $page->signedMember ? $page->signedMember->chartOptions : Trade::DefaultChartOptions();
$trade = Trade::GenerateRandomTrade($page->signedMember, $chartOptions, $boss );

$_SESSION['session_trade'] = serialize($trade);
session_write_close();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>FakeMoneyRealStocks.com - Historical Stock Market Charts and Training Tools for Traders.</title>
<meta name="keywords" content="" />
<meta name="description" content="" />

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>  
<link href="/styles/default.css" rel="stylesheet" type="text/css" media="screen" />

<link href="/scripts/thickbox.css" rel="stylesheet" type="text/css" media="screen" />
<script src="/scripts/thickbox.js" type="text/javascript"></script>

<script src="/scripts/chart.js" type="text/javascript"></script>
<script src="/scripts/functions.js" type="text/javascript"></script>

<script type="text/javascript">
var WV1SiteDir = '<? echo SITE_DOMAIN ?>';
</script>

</head>
<body>

	<? require_once("includes/make_header.php"); ?>
    
		<div id="latest-post">
            <? if ($boss) { ?>
            You are in <b>BOSS MODE</b>! The stock below will <b style="color:blue;font-size:12pt">rise or fall at least 10%</b> in 10 days. Which will it be?<br>
            <? } else { ?>
			Test your stock-picking skills against real historical data.
            We'll display a random chart from the past, and you guess whether the stock rose or fell. We'll even give you $100,000 in play money to test your skill. 
            But beware: it's addictive!<br>
            <? } ?>
            
            <span id='' style='width:100%;text-align:center'><b>Account Value</b>: 
            <span id='Account' style=';color:green;font-weight:bold;font-size:16pt'><? echo FormatCurrency($account) ?></span>
            &nbsp;<span id='LastTrade'></span> (<a href="index.php?reset=1<?php if ($boss) echo '&boss'; ?>" onclick="return confirm('This will reset your account value and clear all your trades. Are you sure you want to do that?')">reset</a>)
            </span>
		</div>
		<!-- start content -->
        <div id="content" style="">
			<? require_once("includes/include_chart.php"); ?><br>
			
			<? require_once("includes/include_settings.php"); ?>
            
        </div>
		<!-- end content -->
		<!-- start sidebar -->
     <? require_once("includes/make_footer.php"); ?>  
     
</body>
</html>
      
		