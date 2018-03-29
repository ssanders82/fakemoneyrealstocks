<?
require_once("../includes/commonfuncs.php");
$account = GetCurrentAccountValue($page->signedMember);
$trade = GetSessionTrade();
$boss = isset($_GET['boss']) && $_GET['boss'];

$chartOptions = $page->signedMember ? $page->signedMember->chartOptions : Trade::DefaultChartOptions();
$chartOptions['isAsync'] = true;
foreach ($chartOptions as $key=>$val)
{
    if ($boss && $key == "tradeLength") continue;
    if (isset($_GET[$key])) $chartOptions[$key] = $_GET[$key];
    else $chartOptions[$key] = "";
}
$nextTrade = Trade::GenerateRandomTrade($page->signedMember, $chartOptions, $boss );
//Debug($nextTrade);
if ($page->signedMember && $page->signedMember->memberLevel) $page->signedMember->UpdateDBValue("ChartOptions", json_encode($chartOptions));

// Save in session
$sessionNumTrades = isset($_SESSION['sessionNumTrades']) ? $_SESSION['sessionNumTrades'] : 0;
$sessionNumWinners = isset($_SESSION['sessionNumWinners']) ? $_SESSION['sessionNumWinners'] : 0;
$sessionProfit = isset($_SESSION['sessionProfit']) ? $_SESSION['sessionProfit'] : 0;

if ($trade && $account)
{
	$trade->accountAtStart = $account;
    /*$trade->GetChartOptions();*/
    $trade->SetTradeLength($boss ? 10 : $chartOptions['tradeLength']);
    $trade->SetTradeSize($chartOptions['tradeSize']);
    $trade->SetIsShort($_GET['tradeType']);
    $trade->chartLength = $chartOptions['chartLength'];
    $trade->chartOptions = $chartOptions;
    $trade->BuildTradeData();
    $trade->member = $page->signedMember;
    
    $startDateString = date("M. j, Y", strtotime($trade->startDate->date) );
    $endDateString = date("M. j, Y", strtotime($trade->endDate->date) );
        
	if ($_GET['tradeType'] > 0)
    {
    	$trade->InsertIntoDatabase();
        
        $profitMultiplier = 1 + ($trade->profit / $account);
		$account += $trade->profit;
        // This is done in trade->insertintodatabase
        //if ($page->signedMember) $page->signedMember->UpdateTrades($profitMultiplier, $account);
        if ($page->signedMember)
        {
        	$page->signedMember->SetAccount($account);
            // Update weekly trades
            $page->signedMember->UpdateWeeklyAccounts();
        }
        
        if ($account <= 100) $account = 100;
        $_SESSION['AccountValue'] = $account;
        
        $sessionNumTrades++;
        if ($trade->profit > 0) $sessionNumWinners++;
        $sessionProfit += $trade->profit;     
    }
    else
    {
    	// Skip
    	$trade->SetIsSkip();
        $trade->InsertIntoDatabase();
    }
	
	if (isset($_GET['d'])) Debug($trade);
	
    // Update member data
    if ($page->signedMember) $page->signedMember = new Member($page->signedMember->memberID);    
	$sessionNumTradesText = IS_WIDGET && $page->signedMember ? GetWeeklyNumTrades($page->signedMember) : $sessionNumTrades;
	$sessionProfitText = FormatCurrencySide($sessionProfit);
	$accountText = FormatCurrency(IS_WIDGET && $page->signedMember ? GetWeeklyAccountValue($page->signedMember) : $account);
    
    $doRequireRegister = ( $sessionNumTrades > 3 && $sessionNumTrades % 4 == 0 ) && !$page->signedMember ? "true" : "false";
    $leaderboardText = "";
    if (function_exists("BuildLeaderboard"))
    {
    	$standing = 0;
        $group = GetFirstGroup($page->signedMember);
    	$leaderboardText = CleanOutputForJS(BuildLeaderboard($page->signedMember, $standing, $group));
	}
    
    if (!IS_WIDGET) $doRequireRegister = "false"; 
    
	echo "var currentTrade = new Object();\n";
	echo "currentTrade.company = '" . str_replace("'", "\'", $trade->stock->CompanyToPrint()) . "'\n";
	echo "currentTrade.tradeType = '" . $trade->GetTradeType() . "'\n";
	echo "currentTrade.tradeLength = '" . $trade->tradeLength . "'\n";
	echo "currentTrade.endChartSrc = '" . $trade->EndChartSrc() . "'\n";
	echo "currentTrade.exitPrice = '" . $trade->GetExitPrice() . "'\n";
	echo "currentTrade.change = '" . $trade->GetChange() . "'\n";
	echo "currentTrade.profit = '" . FormatCurrency($trade->profit) . "'\n";
	echo "currentTrade.startDateString = '$startDateString'\n";
	echo "currentTrade.endDateString = '$endDateString'\n";
	echo "var nextTrade = new Object();\n";
    echo "nextTrade.startChartHeight = " . $nextTrade->ChartHeight() . "\n";
	echo "nextTrade.startChartSrc = '" . $nextTrade->StartChartSrc() . "'\n";
	echo "nextTrade.entryPrice = '" . $nextTrade->GetEntryPrice() . "'\n";
    echo "nextTrade.industry = '" . $nextTrade->stock->industry . "'\n";
    
    if ($trade->isSkip)
    {
        echo "WV1.nextTrade = nextTrade;\n";
        echo "WV1.ShowNextTrade();\n";
    }
    else
    {
        echo "WV1.ExecuteTradeAsyncCallback(currentTrade, nextTrade,'{$accountText}', '$sessionNumTradesText','$sessionProfitText', {$doRequireRegister}, '{$leaderboardText}')\n";
    }
    
    if (!$page->signedMember && $sessionNumTrades > 10 && mt_rand(0,100)>75)
    {
        echo "WV1.ShowRegisterPopup(false);\n";
    }
    else if ($page->signedMember && !$page->signedMember->memberLevel && $sessionNumTrades > 10 && mt_rand(0,100)>90)
    {
        echo "WV1.ShowUpgradePopup(false);\n";
    }
    
}
else
{
	echo "window.location=document.location\n";
}
$_SESSION['session_trade'] = serialize($nextTrade);
$_SESSION['sessionNumTrades'] = $sessionNumTrades;
$_SESSION['sessionNumWinners'] = $sessionNumWinners;
$_SESSION['sessionProfit'] = $sessionProfit;
session_write_close();

?>