<table cellspacing="0" cellpadding="0">
<tr><td class="b r">Year</td><td class="b r">Trades</td><td class="b r">Avg. Profit</td><td class="b r">Days</td><td class="b r">Profit/Day</td></tr>
<?
$sql = "select count(TradeID) as numTrades,Year(StartDate) as year,avg(ProfitPercentage) as avg,";
$sql.= " sum(TradeLength) as days, avg(ProfitPercentage/TradeLength) as ppd";
$sql.= " from Trade where MemberID={$lookingAtMember->memberID} group by year order by year ASC";
$results = DatabaseAccess::Select($sql);
foreach ($results as $row)
{
	echo "<tr><td class='r'>{$row['year']}</td><td class='r'>{$row['numTrades']}</td><td class='r'>";
    echo number_format($row['avg']*100,2) . "%</td><td class='r'>{$row['days']}</td><td class='r'>";
    echo number_format($row['ppd']*100,2) . "%</td></tr>"; 
}
?>    
</table>