<?
require("../includes/commonfuncs.php");
$page = new PageView;
$page->isOwnPage = true;
$page->GetPageData();

$errMsg = "";
$isValid = true;
$member = $page->signedMember;

if (isset($_POST['submit']) && $_POST['submit'] == "Cancel")
{
    Redirect($page->signedMember->memberUrl);
}
elseif (isset($_POST['submit']))
{
    $name	= $_POST['name'];
    $emailAddress= $_POST['emailAddress'];
    $password    = $_POST['password'];

    $realName = isset($_POST['realName']) ? strip_tags($_POST['realName']) : "";
    $city    = isset($_POST['city']) ? strip_tags($_POST['city']) : "";
    $state   = isset($_POST['state']) ? strip_tags($_POST['state']) : "";
    $country   = isset($_POST['country']) ? strip_tags($_POST['country']) : "";
    $zipCode = isset($_POST['zipCode']) ? strip_tags($_POST['zipCode']) : "";
    $aboutMe = isset($_POST['aboutMe']) ? strip_tags($_POST['aboutMe']) : "";
    $website = isset($_POST['website']) ? strip_tags($_POST['website'], '<a>') : "";

    $birthmonth   = isset($_POST['birthmonth']) ? (int)$_POST['birthmonth'] : 0;
    $birthday     = isset($_POST['birthday']) ? (int)$_POST['birthday'] : 0;
    $birthyear    = isset($_POST['birthyear']) ? (int)$_POST['birthyear'] : 0;

    if (!IsValidUsername($name))
    {
        $isValid = false;
        $errMsg.= "Invalid username. Please try again.<br>";
    }
    else if (Member::Static_GetMemberFromMemberName($name) && $name != $member->name)
    {
        $isValid = false;
        $errMsg.= "This username is already registered with us.<br>";
    }

    if (!IsValidEmailAddress($emailAddress))
    {
        $isValid = false;
        $errMsg.= "Invalid email address. Please try again.<br>";
    }
    elseif (Member::Static_GetMemberFromEmailAddress($emailAddress) && $emailAddress != $member->emailAddress)
    {
        $isValid = false;
        $errMsg.= "This email address is already registered with us.<br>";
    }

    if (!IsValidPassword($password))
    {
        $isValid = false;
        $errMsg.= "Your password cannot contain spaces and must be at least 4 characters.<br>";
    }

    if ( $birthmonth && $birthday && $birthyear && !checkdate($birthmonth,$birthday,$birthyear) )
    {
        $isValid = false;
        $errMsg.= "This birthdate is invalid<br>";
    }

    if ($isValid)
    {
        $member->UpdateDBValue("Name", $name);
        $member->UpdateDBValue("EmailAddress", $emailAddress);
        $member->UpdateDBValue("Password", $password);
        $member->UpdateDBValue("City", $city);
        $member->UpdateDBValue("State", $state);
        $member->UpdateDBValue("Country", $country);
        $member->UpdateDBValue("ZipCode", $zipCode);
        $member->UpdateDBValue("AboutMe", $aboutMe);
        $member->UpdateDBValue("RealName", $realName);
        $member->UpdateDBValue("Website", $website);

        if ( $birthmonth && $birthday && $birthyear )
        {
            $birthtime = mktime(0,0,0,$birthmonth, $birthday, $birthyear);
            $birthdate = ConvertTimestampToMySqlDate($birthtime);
            $member->UpdateDBValue("Birthday", $birthdate);
        }
        Redirect($member->memberUrl);
    }
}
else
{
    $name = $member->name;
    $password = $member->password;
    $emailAddress = $member->emailAddress;
    $realName = $member->realName;
    $city = $member->city;
    $state = $member->state;
    $zipCode = $member->zipCode;
    $country = $member->country;
    $aboutMe = $member->aboutMe;
    $website = $member->website;
    if ($member->birthday)
    {
        $birthmonth = date("n", strtotime($member->birthday));
        $birthday = date("j", strtotime($member->birthday));
        $birthyear = date("Y", strtotime($member->birthday));
    }
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Edit Your Profile</title>
<link rel = "stylesheet" href="/styles/default.css" type="text/css">
</head>
<body>

<?  require "../includes/make_header.php";  ?>

<div id="content">
	<h2 class="title">Edit profile</h2>
	<div class="entry">
    
<form name="editprofile" action="editprofile.php" method="post">
<table cellpadding="0" cellspacing="4" width="99%">

<tr><td colspan="3" class="header">
<?
if (strlen($errMsg) > 0)
{
    ?><span class="error"><?=$errMsg?></span><?
}
?></td></tr>

<tr valign="top"><td class="leftformcell">Username:</td>
<td width="10">&nbsp;</td>
<td><input type="text" name="name" size="25" maxlength="100" value="<? echo $name ?>" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'"></td></tr>

<tr valign="top"><td class="leftformcell">Email Address:</td>
<td width="10">&nbsp;</td>
<td><input type="text" name="emailAddress" size="35" maxlength="100" value="<? echo $emailAddress ?>" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'"></td></tr>

<tr valign="top"><td class="leftformcell">Password:</td>
<td width="10">&nbsp;</td>
<td><input type="password" name="password" size="14" maxlength="14" value="<? echo $password ?>" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'"></td></tr>

<tr height="10"><td colspan="3"></td></tr>
<tr height="10"><td colspan="3" style="text-align:center;font-weight:bold">Optional</td></tr>

<tr valign="top"><td class="leftformcell">Website:</td>
<td width="10">&nbsp;</td>
<td><input type="text" name="website" size="35" maxlength="255" value="<? echo $website ?>" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'"></td></tr>

<tr valign="top"><td class="leftformcell">About Me</td>
<td width="10">&nbsp;</td>
<td><textarea name="aboutMe" rows="7" cols="45" class="inputtextareablur" onfocus="this.className='inputtextareafocus'"  onblur="this.className='inputtextareablur'"><? echo $aboutMe ?></textarea></td></tr>

<tr><td colspan="3" style="text-align:center"><input type="submit" name="submit" value="Submit">
<input type="submit" name="submit" value="Cancel"></td></tr>

</table>
</form>

</div></div>

<? require "../includes/make_footer.php"; ?>


</body>
</html>
