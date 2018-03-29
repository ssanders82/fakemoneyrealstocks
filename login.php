<?
require("includes/commonfuncs.php");
$page = new PageView;
$page->GetPageData(); 

if ($page->signedMember) Redirect($page->signedMember->dashboardUrl);

if (isset($_GET['refpage'])) $refpage = $_GET['refpage'];
else if (isset($_POST['refpage'])) $refpage = $_POST['refpage'];
else $refpage = "";

$errMsg = "";
if (isset($_POST['submit']))
{
    $login     	 = $_POST['login'];
    $password    = $_POST['password'];
    $refpage     = $_POST['refpage'];
    $isValid 	 = true;

    if ($member = Member::Static_GetMemberFromLogin($login, $password))
    {
		//if ($member->referrer == "tradeshow") Redirect("http://gohere");
		
        $member->DoOnLogin();
        $strRedirect = (strlen($refpage)>0) ? $refpage : $member->dashboardUrl;
        Redirect($strRedirect);
    }
    else
    {
        $isValid = false;
        $errMsg.= "Invalid username or password.<br>";
    }
}
else
{
    $login = "";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Log in to your FakeMoneyRealStocks.com account</title>
<link href="/styles/default.css" rel="stylesheet" type="text/css" media="screen" />
<script src="/scripts/jquery-1.2.3.js" type="text/javascript"></script>
<script src="/scripts/functions.js" type="text/javascript"></script>
</head>
<body>

<?  require "includes/make_header.php";  ?>

<div id="content" style="">
	<h2 class="title">Sign in to access your account</h2>
	<div class="entry">
    

<form name="register" action="login.php" method="post">
<input type="hidden" name="refpage" value="<? echo $refpage ?>">
<table cellpadding="3" cellspacing="0" width="95%">

<?if (strlen($errMsg) > 0)
{
    ?><tr><td colspan="3" style="text-align:center"><span class="error"><?=$errMsg?></span></td></tr><?
}?>

<tr valign="top"><td class="leftformcell">Username or email address:</td>
<td width="10">&nbsp;</td>
<td><input type="text" name="login" size="35" maxlength="100" value="<?=$login?>" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'"></td></tr>
<tr valign="top"><td class="leftformcell">Password:</td>
<td width="10">&nbsp;</td>
<td><input type="password" name="password" size="20" maxlength="20" value="" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'"></td></tr>

<tr><td colspan="3" style="text-align:center"><input type="submit" name="submit" value="Submit"></td></tr>

<tr height="25"><td colspan="3"> </td></tr>
<tr height="25"><td colspan="3" style="text-align:center;font-weight:bold">Don't have an account? <a href="/register.php">Register now</a>.<br>
<a href="/forgotpassword.php">Forgot your password?</a></td></tr>

</table>
<p></p>
</form>

<div style="width:80%" class="msgpanel">
	<span class="title">Why should I register?</span>
    <div style="text-align:left">
    <ul>
    <li>Track your account value permanently</li>
    <li>Awesome stats <a href="http://www.fakemoneyrealstocks.com/members/fakemoneyrealstocks/index">like this</a></li>
    </div>
</div>

</div></div>

<? require "includes/make_footer.php"; ?>
</body>
</html>

