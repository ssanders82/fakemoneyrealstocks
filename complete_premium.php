<?
require("includes/commonfuncs.php");
$page = new PageView;
$page->GetPageData();
if (!$page->signedMember) Redirect("index.php");
//if (count($_POST) > 0) print_r($_POST);

$page->signedMember->memberLevel = 1;
$page->signedMember->UpdateDBValue('MemberLevel', $page->signedMember->memberLevel);
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
    <h2><? echo $page->signedMember->name ?>, thank you for upgrading to a premium account!</h2>
	<div class="entry"> 

<div style="width:80%" class="msgpanel">
	We appreciate your support. Please take advantage of the many new features you've just unlocked.
</div><br>

<p></p>

<div style="width:99%;text-align:center"><a style="font-size:18pt;color:blue" href="index.php" target="_top">Continue Trading</a></div>

</div></div>




<? require "includes/make_footer.php"; ?>
</body>
</html>
