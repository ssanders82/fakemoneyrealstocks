<?
$tradeData = $page->lookingAtMember->GetTradeData();
?>
<table>
    <tr><td class="leftformcell" width="120">Account:</td><td width='10'> </td><td> <? echo FormatCurrencySide($lookingAtMember->accountValue, false) ?></td></tr>
    <tr><td class="leftformcell">Profit:</td><td width='10'> </td><td> <? echo FormatCurrencySide($lookingAtMember->profit, true) ?> <span class='small'>(since registering)</span></td></tr>
    <tr><td class="leftformcell">Trades:</td><td width='10'> </td><td> <? echo number_format($lookingAtMember->numTrades,0) ?></td></tr>

    <?
	if ($lookingAtMember->numTrades > 0)
	{
		?>
		<tr><td class="leftformcell">Days In Market:</td><td width='10'> </td><td> <? echo number_format($lookingAtMember->daysInMarket,0) ?></td></tr>
	    <tr><td class="leftformcell">Profit Per Day:</td><td width='10'> </td><td> <? echo number_format($lookingAtMember->profitPerDay *100,2) ?>%</td></tr>
		<tr><td class="leftformcell">Winners:</td><td width='10'> </td><td> <? echo number_format($lookingAtMember->numWinners,0) . " (" . @number_format(100*$lookingAtMember->numWinners/$lookingAtMember->numTrades,2) . "% of trades)"; ?></td></tr>
		<tr><td class="leftformcell">Average Profit:</td><td width='10'> </td><td> <? echo number_format($lookingAtMember->averageTrade*100,2) ?>%</td></tr>
	    <tr><td class="leftformcell">Standard Deviation:</td><td width='10'> </td><td> <? echo number_format($tradeData['std']*100,2) ?>%</td></tr>
    	<tr><td class="leftformcell">Worst trade:</td><td width='10'> </td><td> <? echo number_format($tradeData['min']*100,2) ?>%</td></tr>
    	<tr><td class="leftformcell">Best trade:</td><td width='10'> </td><td> <? echo number_format($tradeData['max']*100,2) ?>%</td></tr>
		<?
	}
	?>
    
    
    
    
    <?
    if ($lookingAtMember->website)
    {
    	echo "<tr><td class='leftformcell'>Website:</td><td width='10'> </td><td><a href='{$lookingAtMember->website}'>{$lookingAtMember->website}</a></td></tr>";
    }
    if ($lookingAtMember->aboutMe)
    {
    	echo "<tr valign='top'><td class='leftformcell'>About Me:</td><td width='10'> </td><td>{$lookingAtMember->aboutMe}</td></tr>";
    }
    
    if (false)
    {
        echo "<tr valign='top'><td>Options</td><td width='10'><td><pre>";
        print_r($lookingAtMember->chartOptions);
        echo "</pre></td></tr>";
    }
    ?>
</table>
<br><br>
<?
if ($page->isOwnPage)
{
	echo "<b><a href='/logout.php'>logout</a></b><br>";
	echo "<b><a href='{$lookingAtMember->memberUrl}/editprofile'>edit your profile</a></b><br><br>";
}
?>