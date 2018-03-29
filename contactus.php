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
<title>FakeMoneyRealStocks.com - Contact Me</title>
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
function validateContactUs(thisForm) {
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
	<h2 class="title">Feedback / questions<br>
    <span style="font-size:11pt">
    Do you have feedback, questions, or comments?<br>I would love to hear from you.</span></h2>
    
	<div class="entry">
    
<?php
if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];

    $subject = "Message from $name";
    $body = "Message from FakeMoneyRealStocks.com - $name ($email)<br>\r\n\r\n";
    $body.= nl2br(stripslashes($_POST['message']));

    SendEmail(ADMIN_EMAIL, $subject, $body)
    //mail to us
    ?>
    <p><b>Thank you for contacting us.  We will reply as soon as possible.</b></p>
    <?php
} // end if submit

else {
    ?>
    <form action="/contactus.php" method="post" onSubmit="return validateContactUs(this)">
    <table cellspacing="0" cellpadding="2" border="0" width="99%">
    
    <tr valign="top">
    <td width="230" class="leftformcell">Your name</td>
    <td width="10">&nbsp;</td>
    <td><input size="25" maxlength="100" type="text" name="name" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'">
    </tr>
    <tr valign="top">
    <td class="leftformcell">Your email address</td>
    <td width="10">&nbsp;</td>
    <td><input size="40" maxlength="100" type="text" name="email" class="inputtextblur" onfocus="this.className='inputtextfocus'"  onblur="this.className='inputtextblur'">
    </tr>

    <tr valign="top">
    <td class="leftformcell">Message</td>
    <td width="10">&nbsp;</td>
    <td>
    <textarea cols="30" rows="6" name="message" class="inputtextareablur" onfocus="this.className='inputtextareafocus'"  onblur="this.className='inputtextareablur'"></textarea>
    </td>
    </tr>
    <tr height="6"><td colspan="3">&nbsp;</td></tr>

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
