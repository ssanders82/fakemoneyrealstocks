<?
require("includes/commonfuncs.php");

$account = GetCurrentAccountValue($page->signedMember);
if (isset($_GET['reset']))
{
	$account = 100000;
    ResetAccount($page->signedMember);
}

$chartOptions = $page->signedMember ? $page->signedMember->chartOptions : Trade::DefaultChartOptions();
$chartOptions['tradeLength'] = 0;
$trade = Trade::GenerateRandomTrade($page->signedMember, $chartOptions);

$_SESSION['session_trade'] = serialize($trade);
session_write_close();

$advanced = true;
//Debug($trade);
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

<script src="/scripts/chartAdv.js" type="text/javascript"></script>
<script src="/scripts/chart.js" type="text/javascript"></script>
<script src="/scripts/functions.js" type="text/javascript"></script>
<script type="text/javascript">
var WV1SiteDir = '<? echo SITE_DOMAIN ?>';
</script>

</head>
<body>
	<? require_once("includes/make_header.php"); ?>
    
		<div id="latest-post">
			<b>Advanced Trading</b>.
			Walk through a stock trade day-by-day, just like in real life. Each day, choose whether to take your profits or hold out for more.<br>
            <span id='' style='width:100%;text-align:center'><b>Account Value</b>: 
            <span id='Account' style=';color:green;font-weight:bold;font-size:16pt'><? echo FormatCurrency($account) ?></span>
            &nbsp;<span id='LastTrade'></span> (<a href="advanced.php?reset=1" onclick="return confirm('This will reset your account value and clear all your trades. Are you sure you want to do that?')">reset</a>)
            </span>
		</div>
		<!-- start content -->
        <div id="content" style="">

            <? require_once("includes/include_chartAdv.php"); ?> <br>
			
			<? require_once("includes/include_settings.php"); ?>

        </div>
		<!-- end content -->
		<!-- start sidebar -->
     <? require_once("includes/make_footer.php"); ?>  
     
</body>
</html>
      
		