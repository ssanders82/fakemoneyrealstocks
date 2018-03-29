<?
require("includes/commonfuncs.php");
$page = new PageView;
$page->GetPageData();
if (!$page->signedMember) Redirect("index.php");
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
    <h2>Upgrade cancelled</h2>
	<div class="entry"> 

<div style="width:80%" class="msgpanel">
	Are you sure? Please consider <a href="<?php echo PAYPAL_PREMIUM_LINK ?>">upgrading your account</a>
     for a single lifetime payment of just <strike>$24.95</strike> $9.95!
</div><br>

<p></p>

<div style="width:99%;text-align:center"><a style="font-size:18pt;color:blue" href="index.php" target="_top">Continue Trading</a></div>

</div></div>




<? require "includes/make_footer.php"; ?>
</body>
</html>
