<?
require("../includes/commonfuncs.php");
$page = new PageView;
$page->isMemberPage = true;
$page->GetPageData();

// Get member data
$lookingAtMember = $page->lookingAtMember;

$relationship = ($page->signedMember) ? $page->signedMember->GetMembersRelationship($lookingAtMember->memberID) : RELATIONSHIP_NONE;
$arrWishlists = $lookingAtMember->GetWishlists($page->isOwnPage ? "" : "Permissions='" . PERMISSIONS_ALL . "'");
$arrReminders = $page->lookingAtMember->GetReminders();
$arrFriends = $page->lookingAtMember->GetFriends();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><? echo $lookingAtMember->name ?>'s Profile</title>
<link rel = "stylesheet" href="/my_css.css" type="text/css">
<script language="javascript" src="/scripts.js"></script>
<script language="javascript">

</script>
</head>
<body>

<?  require "../includes/make_header.php";  ?>

<div id="content">
	<h2><? echo $page->isOwnPage ? "My" :"{$lookingAtMember->name}'s" ?> Profile</h2>
    <div class="entry">

<table cellpadding="0" cellspacing="3" width='99%'>

<?
if ( $page->isOwnPage )
{
    ?>
    <tr><td colspan="3" class="msgpanel">
    <span class="title">My Profile Options</span><br>
	<?
    if ( strpos($page->signedMember->name," ")!==false )
    {
        echo "<div class=\"pxborder\" style=\"background-color:white;width:97%;padding:4px;text-align:center;line-height:130%;font-weight:bold\">";
        echo "We have updated our system to enter usernames and \"real names\" separately, for privacy.";
        echo " Please <a href=\"/members/{$lookingAtMember->name}/editprofile\" style=\"color:red\">edit your profile</a> now";
        echo " to choose a username, and optionally enter your real name. Your real name will be hidden";
        echo " from everyone except your friends.";
        echo "</div>";
    }

    $arrNeedToConfirm = $page->signedMember->GetFriends(RELATIONSHIP_FRIEND_PENDING, "Member2ID={$page->signedMember->memberID}");
    if ( count($arrNeedToConfirm) > 0 )
    {
        echo "<img src='/images/new.png' class='pngimage'> <a href=\"/members/{$lookingAtMember->name}/confirmfriends\" style=\"font-weight:bold;font-size:12pt;color:blue\">";
        echo "you have " . GetNumberText(count($arrNeedToConfirm),"pending friend") . " awaiting confirmation</a><br>";
    }

    echo "<img src='/images/group_add.png' class='pngimage'> <a style=\"color:#2F2F4F;font-weight:bold\" href=\"/members/{$lookingAtMember->name}/invite\">invite your friends and family</a><br>";
    echo "<img src='/images/page_edit.png' class='pngimage'> <a style=\"color:#2F2F4F;font-weight:bold\" href=\"/members/{$lookingAtMember->name}/editprofile\">edit your profile</a> so people can search for you<br>\n";
    ?></td></tr>
    <tr><td>&nbsp;</td></tr><?
}
?>

<tr valign="top"><td width="140" style="text-align:right;font-weight:bold" class="small">Items</td><td width="10"></td><td>
<?
if ($page->isOwnPage)
{
	echo "you want <a href=\"/members/{$lookingAtMember->name}/\">" . GetNumberText($page->lookingAtMember->numItems, "item") . "</a>";
}
else
{
	echo "{$lookingAtMember->name} wants <a href=\"/members/{$lookingAtMember->name}/\">" . GetNumberText($page->lookingAtMember->numPublicItems, "item") . "</a>";
} 
?>
</td></tr>

<tr valign="top"><td width="140" style="text-align:right;font-weight:bold" class="small">Lists</td><td width="10"></td><td>
<?
foreach ($arrWishlists as $wishlist)
{
    echo "<strong><a href=\"/members/{$lookingAtMember->name}/index?wishlistID={$wishlist->wishlistID}\">{$wishlist->wishlistName}";
    if ( $wishlist->eventDate != "" && $wishlist->eventDate != DATE_BLANK)
    {
        echo " - " . PrintVeryShortDate($wishlist->eventDate);
    }
    echo "</strong></a> (" . GetNumberText($wishlist->numItems, "item") . ")";
    echo "<br>";
}
if (count($arrWishlists) == 0) echo "No current wishlists<br>";
?></td></tr>

<?
if ( $lookingAtMember->realName )
{
    ?>
    <tr valign="top"><td width="140" style="text-align:right;font-weight:bold" class="small">Name</td><td width="10"></td><td>
    <?
    if ( $relationship == RELATIONSHIP_SELF )
    {
        echo $page->lookingAtMember->realName;
        echo "<br><span class=\"small\">(your real name is only visible to you and your friends)";
    }
    elseif ( $relationship == RELATIONSHIP_FRIEND )
    {
        echo $page->lookingAtMember->realName;
    }
    else
    {
        echo "<span class=\"small\">(hidden - you must be this person's friend to see)";
    }
    ?>
    </td></tr>
    <?
}
?>

<tr valign="top"><td width="140" style="text-align:right;font-weight:bold" class="small">Email Address</td><td width="10"></td><td>
<?
if ( $relationship == RELATIONSHIP_SELF )
{
    echo "<a href=\"mailto:{$lookingAtMember->emailAddress}\">$lookingAtMember->emailAddress</a>";
    echo "<br><span class=\"small\">(your email address is only visible to you and your friends)";
}
elseif ( $relationship == RELATIONSHIP_FRIEND )
{
    echo "<a href=\"mailto:{$lookingAtMember->emailAddress}\">$lookingAtMember->emailAddress</a>";
}
else
{
    echo "<span class=\"small\">(hidden - you must be this person's friend to see)";
}
?>
</td></tr>

<tr valign="top"><td width="140" style="text-align:right;font-weight:bold" class="small">Relationship</td><td width="10"> </td><td>
<?
if ($page->signedMember)
{
    if ( $relationship == RELATIONSHIP_SELF )
    {
        echo "This is you";
    }
    elseif( $relationship == RELATIONSHIP_FRIEND )
    {
        echo "This person is your friend";
    }
    elseif( $relationship == RELATIONSHIP_FRIEND_PENDING )
    {
        echo "This friendship is pending";
    }
    else
    {
        echo "<a href=\"/members/" . $page->lookingAtMember->name . "/addfriend\">Add <b>{$lookingAtMember->name}</b> to your friends</a>";
    }
}
else
{
    echo "<a href=\"/login.php?refpage=/members/{$lookingAtMember->name}\">Log in</a> to add this person to your friends list";
}
?>
</td></tr>

<tr valign="top"><td width="140" style="text-align:right;font-weight:bold" class="small">Member Since</td><td width="10"></td><td><? echo PrintVeryShortDate($lookingAtMember->dateAdded) ?></td></tr>

<?
if ($lookingAtMember->city || $lookingAtMember->state || $lookingAtMember->zipCode || $lookingAtMember->country)
{
    ?>
    <tr valign="top"><td width="140" style="text-align:right;font-weight:bold" class="small">Location</td><td width="10"></td><td>
    <?
    if ( $page->lookingAtMember->city )
    {
        echo $page->lookingAtMember->city;
    }
    if ( $page->lookingAtMember->state )
    {
        echo ($page->lookingAtMember->city) ? ", $lookingAtMember->state " : "$lookingAtMember->state ";
    }
    if ( $page->lookingAtMember->zipCode )
    {
        echo $page->lookingAtMember->zipCode;
    }
    if ($page->lookingAtMember->country) echo " {$page->lookingAtMember->country}";
    ?>
    </td></tr>
    <?
}

if ( $page->lookingAtMember->aboutMe )
{
    ?>
    <tr valign="top"><td width="140" style="text-align:right;font-weight:bold" class="small">About Me</td><td width="10"></td><td><? echo nl2br($lookingAtMember->aboutMe) ?></td></tr>
    <?
}

if ( $page->lookingAtMember->birthday )
{
    ?><tr><td width="140" style="text-align:right;font-weight:bold" class="small">Birthday</td><td width="10"></td><td><? echo PrintVeryShortDate($lookingAtMember->birthday) ?></td></tr><?
}
?>

<tr valign="top"><td width="140" style="text-align:right;font-weight:bold" class="small">Friends</td><td width="10"></td><td>
<?
if (count($arrFriends) > 0)
{
    echo ($page->isOwnPage) ? "you have identified " : $lookingAtMember->name . " has identified ";
    echo "<a href=\"/members/{$lookingAtMember->name}/viewfriends\">" . GetNumberText(count($arrFriends), "friend") . "</a>";
}
else
{
    echo ($page->isOwnPage) ? "you have not identified any friends" : $lookingAtMember->name . " has not identified any friends";
}
?></td></tr>
<tr height="10"><td colspan="3"> </td></tr>
<?
$filter = "Permissions <> '" . PERMISSIONS_PRIVATE . "'";
if ($page->signedMember)
{
    $filter .= " OR (MemberID={$page->signedMember->memberID} or CommenterID={$page->signedMember->memberID})";
}
$arrComments = $lookingAtMember->GetComments($filter);
//Debug($arrComments);
if (count($arrComments) >= 1)
{
    echo "<tr height='10' valign='top'><td width=\"140\" style=\"text-align:right;font-weight:bold\" class=\"small\">" . GetNumberText(count($arrComments), "comment") . "</td><td width='10'></td><td>\n";
    foreach ($arrComments as $key)
    {
        if ( $key['CommenterID'] )
        {
            $tempMember = new Member($key['CommenterID']);
            $commenterText = "<a href=\"/members/" . $tempMember->name . "\">$tempMember->name ($tempMember->numPublicItems)</a>";
        }
        else
        {
            $commenterText = "anonymous";
        }

        echo "<b>Posted by $commenterText on " .  PrintDate($key['DateAdded']) . "</b>";
        if ($key['Permissions'] == PERMISSIONS_PRIVATE) echo " (this is a private comment)";
        if ($page->isOwnPage)
        {
            echo " <span class=\"small\">(<a href=\"/members/{$lookingAtMember->name}/deletemembercomment?commentID=" . $key['MemberCommentID'] . "\">delete this comment</a>)</span>\n";
        }
        echo "<br>" . $key['Comment'] . "<br><br>\n";
    }
    echo "</td></tr>\n";
    echo "<tr height='10'><td colspan='3'> </td></tr>\n";
}

echo "<tr><td/><td width='10'><td><img src='/images/user_comment.png' class='pngimage'> <a href=\"/members/{$lookingAtMember->name}/addmembercomment\">Post a comment</a></td></tr>\n";
?>
</table>

</div><!-- entry --></div>

<? require "../includes/make_footer.php"; ?>


</body>
</html>
