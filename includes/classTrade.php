<?
class Trade
{
	var $tradeID;
    var $member;
    var $stock;
    var $startDate;
    var $endDate;
    var $startDailyData;
    var $endDailyData;
    var $entryPrice = 0;
    var $exitPrice = 0;
    var $accountPercentage = 100;
    var $isShort = false;
    var $isSkip = false;
    var $change = 0;
    var $profit = 0;
    var $profitPercentage = 0;
    var $tradeLength = 20;
    var $dateAdded = DATE_BLANK;
	var $isBoss = false;
    var $chartOptions;
    var $numShares = 0;
    var $accountAtStart = 0;
    var $isOpen = false;
    var $lastDateDataUpdated = DATE_BLANK;
    var $openLengthDays = 0;
    var $isAsync = false;
    
    function __construct($member = false)
    {
    	if ($member) $this->member = $member;
    }
    
    static function DefaultChartOptions()
    {
    	$chartOptions = array(
            'isAsync'=>false,
            'chartLength'=>'90',
            'tradeLength'=>'10',
            'tradeSize'=>'100',
            'ma20'=>true,
            'ma50'=>false,
            'bollinger'=>false,
            'spy'=>false,
            'qqq'=>false,
            'dia'=>false,
            'overbought'=>false,
            'oversold'=>false,
            'highvol'=>false,
            'sp100'=>true,
            'sp500'=>false,
            'volume'=>false
        );
        return $chartOptions;
    }
    
    function InsertIntoDatabase()
    {
        $this->dateAdded = ConvertTimestampToMySqlDate(GetThisTime());
		
        $memberID = ($this->member) ? $this->member->memberID : 0;
        $referrer = defined("REMOTE_SITE_CODE") ? REMOTE_SITE_CODE : ""; 
        
        $params = array(':member_id' => $memberID, ':session_id' => session_id(), ':stock_id' => $this->stock->stockID, 
        	':start_date_id' => $this->startDate->dateID, ':end_date_id' => $this->endDate->dateID, ':entry_price' => $this->entryPrice,
        	':exit_price' => $this->exitPrice, ':account_percentage' => $this->accountPercentage, ':is_skip' => (int)$this->isSkip,
        	':is_short' => (int)$this->isShort, ':profit' => $this->profit, ':profit_percentage' => $this->profitPercentage,
        	':trade_length' => $this->tradeLength, ':date_added' => $this->dateAdded, ':referrer' => $referrer, ':is_boss' => (int)$this->isBoss
         );
        	
        $sql = "INSERT INTO Trade (MemberID,SessionID,StockID,StartDateID,EndDateID,EntryPrice,ExitPrice,AccountPercentage,IsSkip, IsShort,Profit,ProfitPercentage,";
        $sql.= "TradeLength,DateAdded,Referrer,IsBoss) VALUES (:member_id, :session_id, :stock_id, :start_date_id, :end_date_id, :entry_price, :exit_price,
        :account_percentage,:is_skip, :is_short, :profit, :profit_percentage, :trade_length, :date_added, :referrer, :is_boss)";
        
        $new_id = DatabaseAccess::Insert($sql, $params );
        $this->tradeID = $new_id;
        
        if ($this->member)
        {
        	$this->member->UpdateTrades();
        }
        return $this;
    }
    
    function DeleteFromDatabase()
    {
        if (!$this->tradeID) return false;
        DatabaseAccess::__Execute("DELETE FROM Trade WHERE TradeID=:trade_id", array(':trade_id' => $this->tradeID ));
        $this->tradeID = 0;
    }

    function PopulateFromDatabase($tradeID)
    {
    	$results = DatabaseAccess::Select("select * from Trade where TradeID=:trade_id", array(':trade_id' => $tradeID) );
    	if (count($results) == 0) return false;
        $this->PopulateFromDataRow($results[0]);
    }
    
    function PopulateFromDataRow($row)
    {
    	$this->tradeID 		= $row->TradeID;
        $this->member 		= $row->MemberID ? new Member($row->MemberID) : false;
        $this->stock  	    = new Stock($row->StockID);
        $this->startDateID  	    = new StockDate($row->StartDateID);
        $this->endDateID  	    = new StockDate($row->EndDateID);
        $this->entryPrice  	    = $row->EntryPrice;
        $this->exitPrice  	    = $row->ExitPrice;
        $this->accountPercentage  	    = $row->AccountPercentage;
        $this->isShort  	    = (bool)$row->IsShort;
        $this->isSkip  	    = (bool)$row->IsSkip;
        $this->profit  	    = $row->Profit;
        $this->profitPercentage  	    = $row->ProfitPercentage;
        $this->tradeLength  	    = $row->TradeLength;
        $this->dateAdded 	    = $row->DateAdded;
        $this->isBoss =     $row->IsBoss;
        
        return true;
    }
    
    function UpdateDBValue($fieldName, $value)
    {
    	DatabaseAccess::Update("UPDATE Trade SET $fieldName = :value WHERE TradeID=:trade_id", array(':value' => $value, ':trade_id' => $this->tradeID) );
    }
	
	function ChartData()
	{
		$data = "w=" . CHART_IMAGE_WIDTH . "&h=" . CHART_IMAGE_HEIGHT;
		foreach ($this->chartOptions as $key => $val)
		{
            if ($key == "chartLength") continue;
            if ($key == "tradeLength") continue;
			if ($val) $data .= "&{$key}={$val}";
		}
		return $data;
	}
    
    function ChartHeight()
    {
        $height = CHART_IMAGE_HEIGHT;
        if ($this->chartOptions['volume']) $height+= 150;
        return $height;
    }
      
    function StartChartSrc()
    {
    	$data = $this->ChartData() . "&ID={$this->startDailyData->ID}&days={$this->chartLength}";
        return CHART_SERVER . "Chart.aspx?data=" . urlencode(mc_encrypt($data));
    }
    
    function EndChartSrc($isComplete = true)
    {
        $numDays = $this->chartLength + $this->tradeLength;
        $highlightDays = $this->tradeLength == 0 ? 0 : $this->tradeLength + 1;
    	$data = $this->ChartData() . "&ID={$this->endDailyData->ID}&highlightDays={$highlightDays}&days={$numDays}";
        if ($isComplete) $data .= "&complete=1"; // If we're in the middle of an advanced trade, this will be false
		return CHART_SERVER . "Chart.aspx?data=" . urlencode(mc_encrypt($data));
    }
    
    function SetIsShort($tradeType)
    {
    	$this->isShort = $tradeType == "2" ? true : false;
    }
    
    function SetTradeLength($tradeLength)
    {
    	if (is_numeric($tradeLength) && $tradeLength > 0 && $tradeLength <= 60)
		{
        	$this->tradeLength = $tradeLength;
		}
    }
    
    function SetTradeSize($tradeSize)
    {
		if (is_numeric($tradeSize) && $tradeSize > 0 && $tradeSize <= 100)
		{
        	$this->accountPercentage = $tradeSize;
		}
    }
    
    function SetIsSkip()
    {
    	$this->isSkip = true;
        $this->SetTradeSize(0);
        $this->profit = 0;
        $this->profitPercentage = 0;
    }
    
    function GetTradeType()
    {
    	if ($this->isSkip) return "0";
        else if ($this->isShort) return "2";
        else return "1";
    }
    
    function GetChange()
    {
    	return number_format(($this->change-1)*100, 2);
    }
    
    function GetEntryPrice()
    {
    	return number_format($this->entryPrice, 2);
    }
    
    function GetExitPrice()
    {
    	return number_format($this->exitPrice, 2);
    }
    
    function BuildTradeData()
    {
    	$this->endDate = new StockDate($this->startDate->dateID + $this->tradeLength);
        $this->endDailyData = new DailyData($this->endDate->dateID, $this->stock->stockID);
        $this->exitPrice = $this->endDailyData->close;
        
        $this->change = $this->exitPrice / $this->entryPrice;
        $this->profitPercentage = ($this->isShort) ? 1 - ($this->exitPrice / $this->entryPrice) : ($this->exitPrice / $this->entryPrice) - 1;
		
		if (!$this->isSkip)
		{
			$this->numShares = ($this->accountAtStart * ($this->accountPercentage/100)) / $this->entryPrice;
			$profitPerShare = ($this->isShort) ? $this->entryPrice - $this->exitPrice : $this->exitPrice - $this->entryPrice;
			$this->profit = $profitPerShare * $this->numShares;
		}
    }
	
    static function GenerateRandomTrade($member, $chartOptions, $boss = false)
    {   
        $sessionNumTrades = isset($_SESSION['sessionNumTrades']) ? $_SESSION['sessionNumTrades'] : 0;
    	$maxTradeLength = 60;
        $filter = "";
        if ($chartOptions['overbought']) $filter .= " AND LongStoch>80";
        if ($chartOptions['oversold']) $filter .= " AND LongStoch<20";
        if ($chartOptions['highvol']) $filter .= " AND Volume/Vol50>2";
        if ($boss) $filter .= " AND NextROC10 not between .9 and 1.1 and NextROC10>0";
        
        $stock = Trade::GetRandomStock();
        if ($filter)
        {
            $sql = "SELECT * from DailyData";
            $sql.= " WHERE Close>5 AND Volume>10000 AND StockID={$stock->stockID} AND DataCalculated=1";
            $sql.= " AND DateID>{$stock->startDateID}+{$chartOptions['chartLength']}+50 AND DateID<{$stock->endDateID} - 10";
            $sql.= " $filter";
            $sql.= " ORDER BY rand() limit 0,1";
        }
        else
        {
            if ($sessionNumTrades <= 5 && $stock->randomDateID > 0) $dateRandID = $stock->randomDateID;
            else $dateRandID = mt_rand($stock->startDateID + $chartOptions['chartLength'] + 50, $stock->endDateID - $maxTradeLength);
            
            $sql = "SELECT * from DailyData";
            $sql.= " WHERE Close>5 AND Volume>10000 AND StockID={$stock->stockID} AND DataCalculated=1";
            $sql.= " and DateID=$dateRandID";
            $sql.= " limit 0,1";
        }
        //echo $sql;exit;
        $results = DatabaseAccess::Select($sql);
        if (count($results) == 0)
        {
        	return Trade::GenerateRandomTrade($member, $chartOptions);
        }
        
        $trade = new Trade($member);
        $trade->isBoss = $boss;
        $trade->startDailyData = new DailyData();
        $trade->startDailyData->PopulateFromDataRow($results[0]);
        $trade->chartLength = $chartOptions['chartLength'];
        $trade->stock = $stock;
        $trade->startDate = new StockDate($trade->startDailyData->dateID);
		$trade->tradeLength = $boss ? 10 : $chartOptions['tradeLength'];
        $trade->entryPrice = $trade->startDailyData->close;
		$trade->chartOptions = $chartOptions;
		//Debug($trade);exit;
        //if (!$chartOptions['isAsync']) Debug($trade);exit;
		return $trade; 
    }
    
	static function GetRandomStock()
    {
    	$rnd = mt_rand(1, 457);
        
        $results = DatabaseAccess::Select("SELECT * from Stock where SmallID=$rnd" );
    	$stock = new Stock();
        $stock->PopulateFromDataRow($results[0]);
        return $stock;
        
    }
}
?>
