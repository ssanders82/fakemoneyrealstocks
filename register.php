<?
require("includes/commonfuncs.php");
$page = new PageView;
$page->GetPageData();

if ($page->signedMember) Redirect($page->signedMember->memberUrl);

$errMsg = "";
if (isset($_POST['submit']))
{
    $name	        = $_POST['name'];
    $emailAddress   = $_POST['emailAddress'];
    $password       = $_POST['password'];
    $terms 			= isset($_POST['terms']) && $_POST['terms'] == "on";
    $isValid 	    = true;
    $member         = new Member;
    
    if (!IsValidUsername($name))
    {
        $isValid = false;
        $errMsg.= "Invalid username (can only contain letters, numbers, underscores and dashes)<br>";
    }
    else if (Member::Static_GetMemberFromMemberName($name) )
    {
        $isValid = false;
        $errMsg.= "This username is already registered with us<br>";
    }

    if (!IsValidEmailAddress($emailAddress))
    {
        $isValid = false;
        $errMsg.= "Invalid email address<br>";
    }
    else if (Member::Static_GetMemberFromEmailAddress($emailAddress) )
    {
        $isValid = false;
        $errMsg.= "This email address is already registered with us<br>";
    }

    if (!IsValidPassword($password))
    {
        $isValid = false;
        $errMsg.= "Your password cannot contain spaces and must be at least " . MIN_PASSWORD_LENGTH  . " characters<br>";
    }
    
    if (!$terms)
    {
    	$isValid = false;
        $errMsg.= "Please confirm the Terms Of Use<br><br>";
    }

    if ($isValid == true)
    {
        $member->name = $name;
        $member->password = $password;
        $member->emailAddress = $emailAddress;
        $member->accountValue = GetCurrentAccountValue(false);
        $member->confirmTerms = true;
        if ( $member->InsertIntoDatabase() )
        {
            $member->DoOnRegister();
            $member->DoOnLogin();
            $member->memberUrl = "/members/{$member->name}";
            Redirect($member->memberUrl);
        }
        else
        {
            CatchError("Could not add new user");
        }
    }
}
else
{
    $name	        = "";
    $emailAddress   = "";
    $password       = "";
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Register with FakeMoneyRealStocks.com</title>
<link href="/styles/default.css" rel="stylesheet" type="text/css" media="screen" />
<script src="/scripts/functions.js" type="text/javascript"></script>
</head>
<body>
<?  require "includes/make_header.php";  ?>
<div id="content">
	<h2 class="title">Register with FakeMoneyRealStocks.com<br><span style="font-size:11pt"></span></h2>
    
	<div class="entry"> 

<form name="register" action="register.php" method="post" enctype="multipart/form-data">
<table cellpadding="3" cellspacing="0" style="width:99%">

<?if (strlen($errMsg) > 0)
{
    ?><tr><td colspan="3"><span class="error"><?=$errMsg?></span></td></tr><?
}?>

<tr valign="top"><td class="leftformcell">Choose a Username:</td>
<td width="10">&nbsp;</td>
<td><input type="text" name="name" size="25" maxlength="100" value="<? echo $name ?>" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'" tabindex="1"></td></tr>

<tr valign="top"><td class="leftformcell">Your Email Address:</td>
<td width="10">&nbsp;</td>
<td><input type="text" name="emailAddress" size="35" maxlength="100" value="<? echo $emailAddress ?>" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'" tabindex="3">
</td></tr>

<tr valign="top"><td class="leftformcell">Choose a Password:</td>
<td width="10">&nbsp;</td>
<td><input type="password" name="password" size="14" maxlength="14" value="" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'" tabindex="4"></td></tr>
<tr valign="top"><td class="leftformcell">Terms</td>
<td width="10">&nbsp;</td>
<td><input type="checkbox" name="terms"> I have read and agree with FakeMoneyRealStocks's <a target="_blank" href="terms.php">Terms Of Use</a></td></tr>

<tr><td colspan="3" style="text-align:center"><input type="submit" name="submit" value="Submit" tabindex="5"></td></tr>

<tr height="25"><td colspan="3"> </td></tr>
<tr height="25"><td colspan="3" style="text-align:center;font-weight:bold">Already have an account? <a href="/login.php">Sign in</a>.</td></tr>
</table>
</form>

<div style="width:80%" class="msgpanel">
	<span class="title">Why should I register?</span>
    <div style="text-align:left">
    <ul>
    <li>Track your account value permanently</li>
    <li>Awesome stats <a href="http://www.FakeMoneyRealStocks.com/members/fakemoneyrealstocks/index">like this</a></li>
    </div>
</div>

</div></div>


<? require "includes/make_footer.php"; ?>
</body>
</html>
