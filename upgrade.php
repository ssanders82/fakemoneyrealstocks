<?
require("includes/commonfuncs.php");
$page = new PageView;
$page->GetPageData();

if (!$page->signedMember) Redirect("register.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Upgrade</title>
<link href="/styles/default.css" rel="stylesheet" type="text/css" media="screen" />
<script src="/scripts/functions.js" type="text/javascript"></script>
</head>
<body>
<?  require "includes/make_header.php";  ?>
<div id="content">
	<h2 class="title">Upgrade to a premium account<br><span style="font-size:11pt"></span></h2>
    
	<div class="entry"> 

<div style="width:80%" class="msgpanel">
	<span class="title">Please consider <a href="<?php echo PAYPAL_PREMIUM_LINK ?>">upgrading your account</a>
     for a single lifetime payment of just <strike>$24.95</strike> $9.95!</span>
</div><br>

<h2>Why?</h2>
<ul>
<li>No more of annoying popups</li>
<li>Additional chart features such as volume, moving averages, Bollinger bands, and stochastics</li>
<li>Customizable chart length, trade length, and position size</li>
<li>Option to show the Dow, NASDAQ 100, and S&amp;P 500 returns on the chart alongside the stock</li>
<li>Additional stocks outside the S&amp;P 100</li>
<li>Keep your default chart length, trade length, and position size each time you return</li>
</ul>

<div style="width:95%;text-align:center">
    <a href="<?php echo PAYPAL_PREMIUM_LINK ?>">
    <img src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" alt="PayPal - The safer, easier way to pay online!">
    </a>
</div>

</div></div>



<? require "includes/make_footer.php"; ?>
</body>
</html>
