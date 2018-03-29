<?
require("includes/commonfuncs.php");
$page = new PageView;
$page->GetPageData(); 

if ($page->signedMember) Redirect($page->signedMember->dashboardUrl);

if (isset($_GET['refpage'])) $refpage = $_GET['refpage'];
else if (isset($_POST['refpage'])) $refpage = $_POST['refpage'];
else $refpage = "";

$errMsg = "";
$isFound = false;
if (isset($_POST['submit']))
{
    $login     	 = $_POST['login'];
    $isValid 	 = true;

    if ($member = Member::Static_GetMemberFromEmailAddress($login))
    {
        $member->DoOnForgotPassword();
        $isFound = true;
    }
    elseif ($member = Member::Static_GetMemberFromMemberName($login))
    {
        $member->DoOnForgotPassword();
        $isFound = true;
    }
    else
    {
        $isValid = false;
        $errMsg.= "Invalid email or username<br>";
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
<title>Forgot your password?</title>
<link rel = "stylesheet" href="/styles/default.css" type="text/css">
<script src="/scripts/functions.js" type="text/javascript"></script>
</head>
<body>

<?  require "includes/make_header.php";  ?>

<div id="content">
	<h2 class="title">Forgot password<br>
    <?
    if (!$isFound)
    {
    	?>
    	<span style="font-size:11pt">Enter your username or email address below and we will email you your password</span>
        <?
	}
    ?></h2>
	<div class="entry">
    
<?
if (!$isFound)
{
	?>
    <form name="register" action="" method="post">
    <input type="hidden" name="refpage" value="<? echo $refpage ?>">
    <table cellpadding="3" cellspacing="0" width="95%">
    
    <?if (strlen($errMsg) > 0)
    {
        ?><tr><td colspan="3" style="text-align:center"><span class="error"><?=$errMsg?></span></td></tr><?
    }?>
    
    <tr valign="top"><td class="leftformcell">Username or email address:</td>
    <td width="10">&nbsp;</td>
    <td><input type="text" name="login" size="35" maxlength="100" value="<?=$login?>" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'"></td></tr>
    
    <tr><td colspan="3" style="text-align:center"><input type="submit" name="submit" value="Submit"></td></tr>
    
    <tr height="25"><td colspan="3"> </td></tr>
    <tr height="25"><td colspan="3" style="text-align:center;font-weight:bold">Don't have an account? <a href="/register.php">Register now</a>.</td></tr>
    
    </table>
    </form>
    <?
}
else
{
	echo "<h3>Success! Your password has been sent.</h3>";
}
?>
</div></div>

<? require "includes/make_footer.php"; ?>
</body>
</html>

