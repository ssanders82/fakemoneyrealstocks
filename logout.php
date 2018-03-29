<?
require("includes/commonfuncs.php");
$page = new PageView;
$page->isSignedPage = true;
$page->GetPageData(); 

if (isset($_POST['submit']))
{
    $_SESSION['SignedMemberID'] = "";
    setcookie("SignedMemberID", "", GetThisTime()+ (3600*24*30) );
    session_write_close();
    header("location: /index.php");
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Log out of FakeMoneyRealStocks.com</title>
<link rel = "stylesheet" href="/styles/default.css" type="text/css">
<script src="/scripts/functions.js" type="text/javascript"></script>
</head>
<body>
<?  require "includes/make_header.php";  ?>

<div id="content">
	<h2 class="title">Log out of FakeMoneyRealStocks.com</h2>
	<div class="entry">
    
<form name="register" action="logout.php" method="post">
<table cellpadding="2" cellspacing="0" width="99%">

<tr><td colspan="3" class="header">Are you sure you want to log out?</td></tr>
<tr><td colspan="3" style="text-align:center"><input type="submit" name="submit" value="Log Out"></td></tr>
</table>
</form>

</div>
</div>


<? require "includes/make_footer.php"; ?>
</body>
</html>
