<?
require("../includes/commonfuncs.php");

$account = GetCurrentAccountValue($page->signedMember);
$trade = GetSessionTrade();

$sessionNumTrades = isset($_SESSION['sessionNumTrades']) ? $_SESSION['sessionNumTrades'] : 0;
$sessionNumWinners = isset($_SESSION['sessionNumWinners']) ? $_SESSION['sessionNumWinners'] : 0;
$sessionProfit = isset($_SESSION['sessionProfit']) ? $_SESSION['sessionProfit'] : 0;

$chartOptions = $page->signedMember ? $page->signedMember->chartOptions : Trade::DefaultChartOptions();
foreach ($chartOptions as $key=>$val)
{
    if ($key == "tradeLength") continue;
    if (isset($_GET[$key])) $chartOptions[$key] = $_GET[$key];
    else $chartOptions[$key] = "";
}
if ($page->signedMember && $page->signedMember->memberLevel) $page->signedMember->UpdateDBValue("ChartOptions", json_encode($chartOptions));
$chartOptions['isAsync'] = true;
$chartOptions['advanced'] = true;
$chartOptions['tradeLength'] = 0;

if ($trade && $account)
{
	$trade->chartOptions = $chartOptions;
    $trade->tradeLength++;
    $trade->member = $page->signedMember;
    $isContinue = false;
    
    if (isset($_GET['new']))
    {
    	if ($_GET['tradeType'] == "0")
        {
        	$trade->SetIsSkip();
            $trade->tradeLength = 0;
        }
        else
        {
            $trade->tradeLength = 1;
			$trade->SetTradeSize($_GET['tradeSize']);
    		$trade->SetIsShort($_GET['tradeType']);
            $trade->accountAtStart = $account;
            $isContinue = true;
		}
    }
    else if (isset($_GET['continue']))
    {
    	$isContinue = true;
    }
    
	if (isset($_GET['close']) || $trade->tradeLength > 40)
    {
    	$isContinue = false;
    	$trade->tradeLength--;
        $sessionNumTrades++;
        if ($trade->profit > 0) $sessionNumWinners++;
        $sessionProfit += $trade->profit;
        if ($trade->tradeLength > 40) echo "alert('You have reached the 40 day maximum trade length');\n";
    }
    
    //////////////////////
    
	if ($trade->tradeID) $trade->DeleteFromDatabase();
    $trade->BuildTradeData();
    $trade->InsertIntoDatabase();
    
    if (!$trade->isSkip)
    {
    	//$profitMultiplier = 1 + ($trade->profit / $trade->accountAtStart);
        $account = $trade->accountAtStart + $trade->profit;
        if ($page->signedMember)
        {
        	$page->signedMember->SetAccount($account);
            // Update weekly trades
            $page->signedMember->UpdateWeeklyAccounts();
        }
        if ($account <= 100) $account = 100;
    	$_SESSION['AccountValue'] = $account;
    }
    //echo "$thisProfitPercentage, $trade->profitPercentage, $change, $prevAccount, $account, $profit, $trade->accountPercentage, $trade->exitPrice, $trade->entryPrice<br>";
    
    //////////////////////
    
    
	echo "var currentTrade = new Object();\n";
	echo "currentTrade.tradeType = '" . $trade->GetTradeType() . "'\n";
	echo "currentTrade.tradeLength = '" . $trade->tradeLength . "'\n";
	// Trade length
	echo "currentTrade.endChartSrc = '" . $trade->EndChartSrc(!$isContinue) . "'\n";
	echo "currentTrade.exitPrice = '" . $trade->GetExitPrice() . "'\n";
	echo "currentTrade.change = '" . $trade->GetChange() . "'\n";
	echo "currentTrade.profit = '" . FormatCurrency($trade->profit) . "'\n";
    
    // Update Member data
    if ($page->signedMember) $page->signedMember = new Member($page->signedMember->memberID);
    $accountText = FormatCurrency(IS_WIDGET && $page->signedMember ? GetWeeklyAccountValue($page->signedMember) : $account);
    
    $leaderboardText = "";
    if (function_exists("BuildLeaderboard"))
    {
    	$standing = 0;
        $group = GetFirstGroup($page->signedMember);
    	$leaderboardText = CleanOutputForJS(BuildLeaderboard($page->signedMember, $standing, $group));
	}
    
    $sessionNumTradesText = IS_WIDGET && $page->signedMember ? GetWeeklyNumTrades($page->signedMember) : $sessionNumTrades;
	$sessionProfitText = FormatCurrencySide($sessionProfit);

	if ($isContinue)
    {
    	echo "WV1A.ContinueTradeAsyncCallback(currentTrade,'{$accountText}','$sessionNumTradesText','$sessionProfitText', '{$leaderboardText}')";
        $_SESSION['session_trade'] = serialize($trade);
    }
    else
    {
    	$nextTrade = Trade::GenerateRandomTrade($page->signedMember, $chartOptions);
        if ($page->signedMember && $page->signedMember->memberLevel) $page->signedMember->UpdateDBValue("ChartOptions", json_encode($chartOptions));
        $_SESSION['session_trade'] = serialize($nextTrade);
        
		$startDateString = date("M. j, Y", strtotime($trade->startDate->date) );
    	$endDateString = date("M. j, Y", strtotime($trade->endDate->date) );
		echo "currentTrade.company = '" . str_replace("'", "\'", $trade->stock->CompanyToPrint()) . "'\n";
		echo "currentTrade.startDateString = '$startDateString'\n";
    	echo "currentTrade.endDateString = '$endDateString'\n";
		echo "var nextTrade = new Object();\n";
        echo "nextTrade.startChartHeight = " . $nextTrade->ChartHeight() . "\n";
		echo "nextTrade.startChartSrc = '" . $nextTrade->StartChartSrc() . "'\n";
		echo "nextTrade.entryPrice = '" . $nextTrade->GetEntryPrice() . "'\n";
        echo "nextTrade.industry = '" . $nextTrade->stock->industry . "'\n";
		
        if ($trade->isSkip)
        {
            echo "WV1A.nextTrade = nextTrade;\n";
            echo "WV1A.ShowNextTrade();\n";
        }
        else
        { 
		    echo "WV1A.ExecuteTradeAsyncCallback(currentTrade, nextTrade,'{$accountText}', '$sessionNumTradesText','$sessionProfitText', '{$leaderboardText}')\n";
        }
    }   
}
else
{
	echo "window.location=document.location\n";
}

$_SESSION['sessionNumTrades'] = $sessionNumTrades;
$_SESSION['sessionNumWinners'] = $sessionNumWinners;
$_SESSION['sessionProfit'] = $sessionProfit;
session_write_close();
?>