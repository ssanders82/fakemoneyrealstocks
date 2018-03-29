<?
require_once("../includes/commonfuncs.php");
$account = GetCurrentAccountValue($page->signedMember);
$trade = Trade::GenerateRandomTrade($page->signedMember, $chartOptions, $boss );

$_SESSION['session_trade'] = serialize($trade);
session_write_close();

$boss = isset($_GET['boss']) && $_GET['boss'];

$chartOptions = $page->signedMember ? $page->signedMember->chartOptions : Trade::DefaultChartOptions();
$chartOptions['isAsync'] = true;
foreach ($chartOptions as $key=>$val)
{
    if ($boss && $key == "tradeLength") continue;
    if (isset($_GET[$key])) $chartOptions[$key] = $_GET[$key];
    else $chartOptions[$key] = "";
}
// Save in session
$sessionNumTrades = isset($_SESSION['sessionNumTrades']) ? $_SESSION['sessionNumTrades'] : 0;
$sessionNumWinners = isset($_SESSION['sessionNumWinners']) ? $_SESSION['sessionNumWinners'] : 0;
$sessionProfit = isset($_SESSION['sessionProfit']) ? $_SESSION['sessionProfit'] : 0;

if ($trade && $account)
{
    if ($page->signedMember) $page->signedMember = new Member($page->signedMember->memberID);    
	$sessionNumTradesText = IS_WIDGET && $page->signedMember ? GetWeeklyNumTrades($page->signedMember) : $sessionNumTrades;
	$sessionProfitText = FormatCurrencySide($sessionProfit);
	$accountText = FormatCurrency(IS_WIDGET && $page->signedMember ? GetWeeklyAccountValue($page->signedMember) : $account);
    
	echo "var nextTrade = new Object();\n";
    echo "nextTrade.startChartHeight = " . $trade->ChartHeight() . "\n";
	echo "nextTrade.startChartSrc = '" . $trade->StartChartSrc() . "'\n";
	echo "nextTrade.entryPrice = '" . $trade->GetEntryPrice() . "'\n";
    echo "nextTrade.industry = '" . $trade->stock->industry . "'\n";
    
    echo "WV1.accountText = '" . $accountText . "';\n";
	echo "WV1.sessionNumTradesText = '" . $sessionNumTradesText . "'\n";
	echo "WV1.sessionProfitText = '" . $sessionProfitText . "'\n";
    echo "$('#Account').html(WV1.accountText);\n";
    echo "$('#WSV1_NumTrades').html(WV1.sessionNumTradesText);\n";
	echo "$('#WSV1_Profit').html(WV1.sessionProfitText);\n";
    
    echo "WV1.nextTrade = nextTrade;\n";
    echo "WV1.ChangeChart(WV1.nextTrade.startChartSrc, nextTrade.startChartHeight, WV1.ShowNewTradeCallback)\n";
    
}
else
{
	echo "window.location=document.location\n";
}

?>