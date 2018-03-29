<?
require("includes/commonfuncs.php");
$page = new PageView;
$page->GetPageData();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>FakeMoneyRealStocks.com - Invite Your Friends</title>
<link rel = "stylesheet" href="/styles/default.css" type="text/css">
<script language="javascript">
function validateEmail(email) {
    regEx1 = /\w+@\w+/
    regEx2 = /\w+\.\w/
    if (regEx1.test(email) && regEx2.test(email)) {
        return true;
    }
    else {
        return false;
    }
}
function validateForm(thisForm) {
    allFilled = true;

    errMsg = "Please fill in the following field(s):\n";

    thisName = thisForm.name.value.replace(/ /g, "")
    email = thisForm.email.value.replace(/ /g,"")
    message = thisForm.message.value.replace(/ /g,"")

    if (thisName== "") {
        errMsg+= "- Your name\n"
        allFilled = false;
    }

    if (!validateEmail(email)) {
        errMsg+= "- A valid email\n"
        allFilled = false;
    }

    if (message == "") {
        errMsg+= "- A brief message\n"
        allFilled = false;
    }
    
    if (thisForm.sendTo.value == "") {
        errMsg+= "- A list of your friend's emails\n"
        allFilled = false;
    }
    
    if (allFilled == false) {
        alert(errMsg);
        return false;
    }
    else {
        return true;
    }


}
</script>
<script src="/scripts/functions.js" type="text/javascript"></script>
</head>
<body>

<?  require "includes/make_header.php";  ?>

<div id="content">
	<h2 class="title">Invite your friends<br>
    <span style="font-size:11pt">Let them know what they're missing</span></h2>
    
	<div class="entry">
    
<?php
if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];

    $message = "You've been sent a message from $name ($email) via <a href='http://www.fakemoneyrealstocks.com'>FakeMoneyRealStocks.com</a><br>\r\n\r\n";
    $message.= nl2br(stripslashes($_POST['message']));
    $message.= "<br><br>Visit <a href='http://www.fakemoneyrealstocks.com'>FakeMoneyRealStocks.com</a>";
    
    $subject = "Message from $name ($email)";

    $sendTo = explode("\n", $_POST['sendTo']);
    foreach ($sendTo as $to)
    {
        $to = trim($to);
        SendEmail($to, $subject, $message);
    }
    //mail to us
    ?>
    <p><b>Thank you, your message has been sent!</b></p>
    <?php
} // end if submit

else {
    $email = $page->signedMember ? $page->signedMember->emailAddress : "";
    $name = $page->signedMember ? $page->signedMember->realName : "";
    
    ?>
    <form action="/invite.php" method="post" onSubmit="return validateForm(this)">
    <table cellspacing="0" cellpadding="2" border="0" width="99%">
    
    <tr valign="top">
    <td width="350" class="leftformcell">Your name</td>
    <td width="10">&nbsp;</td>
    <td><input size="25" maxlength="100" type="text" name="name" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'" value="<?php echo $name ?>">
    </tr>
    <tr valign="top">
    <td class="leftformcell">Your email address</td>
    <td width="10">&nbsp;</td>
    <td><input size="40" maxlength="100" type="text" name="email" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'" value="<?php echo $email ?>">
    </tr>

    <tr valign="top">
    <td class="leftformcell">Send to:<br>
    <span style="font-size:11px">(Enter one email per line)</span>
    </td>
    <td width="10">&nbsp;</td>
    <td>
    <textarea cols="30" rows="8" name="sendTo" class="inputtextareablur" onfocus="this.className='inputtextareafocus'"  onblur="this.className='inputtextareablur'"></textarea>
    </td>
    </tr>

    <tr valign="top">
    <td class="leftformcell">Personal Message</td>
    <td width="10">&nbsp;</td>
    <td>
    <textarea cols="30" rows="4" name="message" class="inputtextareablur" onfocus="this.className='inputtextareafocus'"  onblur="this.className='inputtextareablur'"></textarea>
    </td>
    </tr>
    <tr height="6"><td colspan="3">A link to FakeMoneyRealStocks.com will be included at the bottom of the message sent.</td></tr>

    <tr valign="top">
    <td colspan="3" style="text-align:center">
    <input type="submit" value="Send" name="submit" style="font:9pt Verdana">
    </td>
    </tr>

    <tr height="35"><td colspan="3">&nbsp;</td></tr>
    <tr><td colspan="3" style="line-height:21px" class="small">
    <b>
    Sam Sanders<br>
    <a href="http://www.samsanders.net">http://www.samsanders.net</a><br>
    <a href="mailto:info@FakeMoneyRealStocks.com">info@FakeMoneyRealStocks.com</a><br>
    Phone: (864) 903-1915<br>
    </td></tr>
    </table>
    </form>


    <?php
    // show form
}
?>

</div></div>


<? require "includes/make_footer.php"; ?>
</body>
</html>
