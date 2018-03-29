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
<title>FakeMoneyRealStocks.com Leaderboard</title>
<link href="/styles/default.css" rel="stylesheet" type="text/css" media="screen" />
<script src="/scripts/jquery-1.2.3.js" type="text/javascript"></script>
<script src="/scripts/functions.js" type="text/javascript"></script>
</head>
<body>

<?  require "includes/make_header.php";  ?>

<div id="content" style="">
	<h2 class="title">Leaderboard</h2>
	<div class="entry">
<?
$results1 = DatabaseAccess::Select("SELECT MemberID FROM Member ORDER BY AccountValue DESC LIMIT 0,4");

$results2 = DatabaseAccess::Select("SELECT MemberID FROM Member ORDER BY NumTrades DESC LIMIT 0,4");

$results3 = DatabaseAccess::Select("SELECT MemberID FROM Member WHERE NumTrades>= 25 ORDER BY AverageTrade DESC LIMIT 0,4");

$results4 = DatabaseAccess::Select("SELECT ProfitPercentage,MemberID FROM Trade WHERE MemberID>0 ORDER BY ProfitPercentage DESC LIMIT 0,1");

$results5 = DatabaseAccess::Select("SELECT ProfitPercentage,MemberID FROM Trade WHERE MemberID>0 ORDER BY ProfitPercentage ASC LIMIT 0,1");

$results6 = DatabaseAccess::Select("SELECT MemberID FROM Member WHERE NumTrades>= 25 ORDER BY (NumWinners/NumTrades) DESC LIMIT 0,4");
?>    
<table>
    <tr valign='top'><td class="leftformcell" width="220">Highest account</td><td>
    <?
    foreach ($results1 as $row)
    {
    	$member1 = new Member($row->MemberID);
    	echo "<a href='{$member1->dashboardUrl}'>{$member1->name}</a>: " . FormatCurrencySide($member1->accountValue, false) . "<br>";
    }
    ?></td></tr>
    
    <tr valign='top'><td class="leftformcell">Most trades</td><td>
    <?
    foreach ($results2 as $row)
    { 
    	$member2 = new Member($row->MemberID);
    	echo "<a href='{$member2->dashboardUrl}'>{$member2->name}</a>: " . number_format($member2->numTrades) . "<br>";
    } 
    ?>
    </td></tr>
    
    <tr valign='top'><td class="leftformcell">Highest average trade*</td><td>
    <? 
    foreach ($results3 as $row)
    {
    	$member3 = new Member($row->MemberID);
    	echo "<a href='{$member3->dashboardUrl}'>{$member3->name}</a>: " . number_format(100*$member3->averageTrade,2) . "% ({$member3->numTrades} trades)<br>";
    } 
    ?>
    </td></tr>
    
    <tr valign='top'><td class="leftformcell">Best trade</td><td>
    <?
    foreach ($results4 as $row)
    {
    	$member4 = new Member($row->MemberID);
    	echo "<a href='{$member4->dashboardUrl}'>{$member4->name}</a>: ". number_format(100*$results4[0]->ProfitPercentage,2) . "%<br>";
    } 
    ?>
    </td></tr>
    
    <tr valign='top'><td class="leftformcell">Worst trade</td><td>
    <? 
    foreach ($results5 as $row)
    {
    	$member5 = new Member($row->MemberID);
    	echo "<a href='{$member5->dashboardUrl}'>{$member5->name}</a>: ". number_format(100*$results5[0]->ProfitPercentage,2) . "%<br>";
    } 
    ?>
    </td></tr>
    
    <tr valign='top'><td class="leftformcell">Highest winning percentage*</td><td>
    <? 
    foreach ($results6 as $row)
    {
    	$member6 = new Member($row->MemberID);
    	echo "<a href='{$member6->dashboardUrl}'>{$member6->name}</a>: " . number_format(100*$member6->numWinners/$member6->numTrades,2) . "% ({$member3->numTrades} trades)<br>"; 
    }
    ?>
    </td></tr>
</table>
* Minimum of 25 trades

</div></div>

<? require "includes/make_footer.php"; ?>
</body>
</html>

