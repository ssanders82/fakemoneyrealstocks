<h2>Open real-time trades</h2>

<?
$arrOpenTrades = $lookingAtMember->GetTrades("IsOpen=1");
if (count($arrOpenTrades)> 0)
{
	?>
    <table cellspacing="0" cellpadding="0"><tr>
    <td class="b r">Stock</td><td class="b r">Entry Date</td>
    <td class="b r">Days Left</td><td class="b r">Entry Price</td>
    <td class="b r">Current Price</td><td class="b r">Profit</td></tr>
    <?
    foreach ($arrOpenTrades as $trade)
    {
    	$trade->UpdateOpenTrade();
        $trade->currentPrice = number_format($trade->currentPrice,2); 
        $currentProfit = ($trade->isShort) ? 1 - ($trade->currentPrice / $trade->entryPrice) : ($trade->currentPrice / $trade->entryPrice) - 1;
        $color = ($currentProfit > 0) ? "green" : ($currentProfit == 0 ? "" : "red");
        $daysLeft = $trade->tradeLength - $trade->openLengthDays;
    	echo "<tr><td class='r'>{$trade->stock}</td><td class='r'>{$trade->startDate}</td><td class='r'>";
        echo "$daysLeft</td><td class='r'>{$trade->entryPrice}</td><td class='r'>";
        echo "$trade->currentPrice</td><td class='r' style='color:{$color}'>" . number_format(100*$currentProfit,2) . "%</td></tr>"; 
    }
    ?></table><?
}
else echo "(NONE)"; 
?>

<br><br>
<h2>Past real-time trades</h2>
<?
$arrClosedTrades = $lookingAtMember->GetTrades("IsOpen=0 AND IsRealTime=1");
if (count($arrClosedTrades) > 0)
{
	?>
   	<table cellspacing="0" cellpadding="0"><tr>
    <td class="b r">Stock</td><td class="b r">Entry Date</td>
    <td class="b r">Trade Length</td><td class="b r">Entry Price</td>
    <td class="b r">Exit Price</td><td class="b r">Profit</td></tr>
    <?
    foreach ($arrClosedTrades as $trade)
    {
        $color = ($trade->profitPercentage > 0) ? "green" : ($trade->profitPercentage == 0 ? "" : "red");
        $daysLeft = $trade->tradeLength - $trade->openLengthDays;
    	echo "<tr><td class='r'>{$trade->stock}</td><td class='r'>{$trade->startDate}</td><td class='r'>";
        echo "{$trade->tradeLength}</td><td class='r'>{$trade->entryPrice}</td><td class='r'>";
        echo "$trade->exitPrice</td><td class='r' style='color:{$color}'>" . number_format(100*$trade->profitPercentage,2) . "%</td></tr>"; 
    }
    ?></table><?
}
else echo "(NONE)";
?>

<br><br>
<b>What is a real-time trade?</b><br>
If you are a registered member, occasionally a chart from a real-time stock will pop up as you are trading. After you
make your buy or sell decision, you can check the status of your real-time trades from your dashboard. If you do not
wish to be shown any real-time trades, you can disable them in the settings. You will not be given a real-time
trade if you have more than 25 currently open.