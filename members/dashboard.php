<?

require("../includes/commonfuncs.php");
//Redirect("index.php");

$page = new PageView;
$page->isMemberPage = true;
$page->GetPageData();
$lookingAtMember = $page->lookingAtMember;
$signedMember = $page->signedMember;

$tradeData = $page->lookingAtMember->GetTradeData();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>FakeMoneyRealStocks.com :: <? echo $lookingAtMember->name ?></title>
<link rel = "stylesheet" href="/styles/default.css" type="text/css">


</head>

<? require("../includes/make_header.php") ?>

<div id="content">
	<h2 class="title">
    <? echo $lookingAtMember->name; ?><br>
    Account: <? echo FormatCurrency($lookingAtMember->accountValue) ?><br>
    Trades: <? echo $lookingAtMember->numTrades ?><br>
    Average Profit: <? echo number_format($lookingAtMember->averageTrade,2) ?>%<br>
    
    <br><a href="/index.php">Trade!</a>
	</h2>
    

</div>

<? require("../includes/make_footer.php") ?>