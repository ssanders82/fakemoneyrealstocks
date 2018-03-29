<?
class Member
{
    var $memberID = 0;
    var $memberUrl;
    var $name = "";
    var $emailAddress = "";
    var $password = "";
    var $realName = "";
    var $ipAddress = "";
    var $confirmTerms = false;
    var $dateAdded;
    var $dateUpdated;
    var $pic1;
    var $pic1Thumb;
    var $pic2;
    var $pic2Thumb;
    var $pic3;
    var $pic3Thumb;
    var $birthday = DATE_BLANK;
    var $website;
    var $aboutMe;
    var $address1;
    var $address2;
    var $zipCode;
    var $homePhone;
    var $cellPhone;
    var $screenName;
    var $IMService;
    var $city;
    var $state;
    var $country;
    var $daysInMarket = 0;
    var $defaultGroupID = 0;
    var $referrer = "";
    var $accountValue = 100000;
    var $weeklyAccountCapped = 100000;
    var $weeklyAccount = 100000;
    var $memberLevel = 0;
    var $chartOptions;
    
    function __construct($memberID = 0)
    {
        if ($memberID) $this->PopulateFromDatabase($memberID);
    }
    
    static function HashPassword($pwd)
    {
    	return sha1($pwd);
    }

    function InsertIntoDatabase()
    {
        $this->dateAdded = ConvertTimestampToMySqlDate(GetThisTime());
        $this->ipAddress = $_SERVER["REMOTE_ADDR"];
        if (defined("REMOTE_SITE_CODE")) $this->referrer = REMOTE_SITE_CODE;

        $sql = "INSERT INTO Member (Name,RealName,EmailAddress,Password,AccountValue,IPAddress,Referrer,";
        $sql.= "DateAdded) VALUES (:name,:real_name,:email,:password,:account_value,:ip_address,:referrer, :date_added)";
        
        $params = array(
        	':name' => $this->name, 
        	':real_name' => $this->realName, 
        	':email' => $this->emailAddress,
        	':password' => Member::HashPassword($this->password), 
        	':account_value' => number_format($this->accountValue,2,".",""),
        	':ip_address' => $this->ipAddress, 
        	':referrer' => $this->referrer, 
        	':date_added' => $this->dateAdded);
        
        $new_id = DatabaseAccess::Insert($sql, $params );
        $this->memberID = $new_id;
        return $this;
    }
    
    function PopulateFromDatabase($memberID)
    {
    	$results = DatabaseAccess::Select("select * from Member where MemberID=:id", array(':id' => $memberID) );
    	if (count($results) == 0) return false;
        $this->PopulateFromDataRow($results[0]);
    }

    function PopulateFromDataRow($row)
    {
        $this->memberID 	= $row->MemberID;
        $this->name  	    = $row->Name;
        $this->realName     = $row->RealName;
        $this->emailAddress = $row->EmailAddress;
        $this->ipAddress 	= $row->IPAddress;
        $this->dateAdded    = $row->DateAdded;
        $this->dateUpdated  = $row->DateUpdated != '0000-00-00 00:00:00' ? $row->DateUpdated : "";
        $this->aboutMe      = $row->AboutMe;
        $this->website          = $row->Website;
        $this->address1     = $row->Address1;
        $this->address2     = $row->Address2;
        $this->zipCode      = $row->ZipCode;
        $this->homePhone    = $row->HomePhone;
        $this->cellPhone    = $row->CellPhone;
        $this->screenName   = $row->ScreenName;
        $this->IMService    = $row->IMService;
        $this->city         = $row->City;
        $this->state        = $row->State;
        $this->country        = $row->Country;
        $this->birthday     = ($row->Birthday != '0000-00-00 00:00:00') ? $row->Birthday : "";
        $this->numTrades     = $row->NumTrades;
        $this->numWinners    = $row->NumWinners;
        $this->numOpenTrades    = $row->NumOpenTrades;
        $this->memberLevel     = $row->MemberLevel;
        $this->accountValue     = $row->AccountValue;
        $this->daysInMarket     = $row->DaysInMarket;
        $this->profit		= $row->Profit;
        $this->averageTrade		= $row->AverageTrade;
        $this->profitPerDay		= $row->ProfitPerDay;
		$this->referrer		= $row->Referrer;
        
        $this->memberUrl    = "/members/index.php?member={$this->name}"; // "/members/{$this->name}";
        $this->dashboardUrl    = "/members/dashboard.php?member={$this->name}"; // "/members/{$this->name}/index";
        
        $this->weeklyAccountCapped     = $row->WeeklyAccountCapped;
        $this->weeklyAccount     = $row->WeeklyAccount;
        $this->weeklyNumTrades     = $row->WeeklyTrades;
        
        $this->defaultGroupID     = $row->DefaultGroupID;
        $this->chartOptions = Trade::DefaultChartOptions();
        if ($row->ChartOptions)
        {
            $chartOptions = json_decode($row->ChartOptions, true);
            foreach ($chartOptions as $key=>$val) $this->chartOptions[$key] = $val; 
        }      
        //$this->memberUrl    = "/members/index.php?member={$this->name}";
        //$this->dashboardUrl = $this->memberUrl;
        
        return true;
    }
    
    function GetTradeData($filter = "")
    {
        $sql = "select count(*) as cnt, std(ProfitPercentage) as std, max(ProfitPercentage) as max, min(ProfitPercentage) as min from Trade where MemberID={$this->memberID}";
        if ($filter) $sql .= " AND ($filter)";
        $results = DatabaseAccess::Select($sql);
        return $results[0];
    }
    
    function GetTrades($filter = "")
    {
        $sql = "SELECT * FROM Trade WHERE MemberID='$this->memberID'";
        if ($filter) $sql .= " AND ($filter)";
        $sql.= " ORDER BY DateAdded DESC";
        $results = DatabaseAccess::Select($sql);
        
        $arrTrades = Array();
        foreach ($results as $row)
        {
            $trade = new Trade($this);
            $trade->PopulateFromDataRow($row);
            $arrTrades[] = $trade;
        }
        return $arrTrades;
    }
    
    function UpdateTrades()
    {
        $sql = "update Member SET";
        $sql.= " Profit=(select sum(Profit) from Trade WHERE IsSkip=0 AND Trade.MemberID=Member.MemberID),";
        $sql.= " DaysInMarket=(select sum(TradeLength) from Trade WHERE IsSkip=0 AND Trade.MemberID=Member.MemberID),";
		$sql.= " NumTrades=(select count(*) from Trade WHERE IsSkip=0 AND Trade.MemberID=Member.MemberID),";
		$sql.= " NumWinners=(select count(*) from Trade WHERE IsSkip=0 AND Trade.MemberID=Member.MemberID AND ProfitPercentage>0),";
		$sql.= " AverageTrade=(select avg(ProfitPercentage) from Trade WHERE IsSkip=0 AND Trade.MemberID=Member.MemberID),";
		$sql.= " ProfitPerDay=(select avg(ProfitPercentage/TradeLength) from Trade WHERE IsSkip=0 AND Trade.MemberID=Member.MemberID)";
        
 		$sql.= " WHERE MemberID={$this->memberID}";
        DatabaseAccess::__Execute($sql);
        
    }
    
    // After each advanced day, update these numbers
    function UpdateWeeklyAccounts()
    {
    	$startOfWeek = date('Y-m-d', mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y'))) . ' 00:00:00';
        // Update weekly profit
        $sql = "SELECT * FROM Trade where IsSkip=0 AND Trade.MemberID={$this->memberID} AND Trade.DateAdded>='{$startOfWeek}' ORDER BY DateAdded ASC";
        $results = DatabaseAccess::Select($sql);
        
        $weeklyAccount = 100000;
        $weeklyAccountCapped = $weeklyAccount;
        $i = 0;
        foreach ($results as $row)
        {
        	$i++;
            $trade = new Trade;
            $trade->PopulateFromDataRow($row);
            $thisMultiplier = ($trade->profitPercentage * ($trade->accountPercentage / 100)) + 1;
            $weeklyAccount *= $thisMultiplier;
            if ($i <= WEEKLY_LEADERBOARD_MAX_TRADES)
            {
            	$weeklyAccountCapped = $weeklyAccount;
            }
        }
        
        if ($weeklyAccount > 0 && $weeklyAccountCapped > 0)
        {
        	$params = array(':weekly' => $weeklyAccount, ':weekly_capped' => $weeklyAccountCapped, ':cnt' => $i, ':id' => $this->memberID);
        	$sqlUpdate = "UPDATE Member SET WeeklyAccount=:weekly,WeeklyAccountCapped=:weekly_capped,WeeklyTrades=:cnt WHERE MemberID=:id";
        	DatabaseAccess::Update($sqlUpdate, $params);
        }
        else
        {
        	//SendEmail("ssanders82@gmail.com", "empty weekly account", "member ID {$this->memberID}<br><br>Weekly: {$weeklyAccount}, capped: {$weeklyAccountCapped}<br><br>sql: $sql");
        }
    }
    
    function ApplyTrade($profitMultiplier, $newAccount)
    {
    	// Update account and weekly numbers
        $startOfWeek = date('Y-m-d', mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y'))) . ' 00:00:00';
        $sql = "SELECT count(*) as count FROM Trade where IsSkip=0 AND Trade.MemberID={$this->memberID} AND Trade.DateAdded>='{$startOfWeek}'";
        $results = $DatabaseAccess::Select($sql);
        $weeklyTrades = $results[0]['count'];
    	
        DatabaseAccess::Update("UPDATE Member SET AccountValue={$newAccount}, WeeklyAccount=WeeklyAccount*{$profitMultiplier},WeeklyTrades={$weeklyTrades} WHERE MemberID={$this->memberID}");
        
        if ($weeklyTrades <= WEEKLY_LEADERBOARD_MAX_TRADES)
        {
        	DatabaseAccess::Update("UPDATE Member SET WeeklyAccountCapped = WeeklyAccountCapped * {$profitMultiplier} WHERE MemberID={$this->memberID}"); 
        }
    }
    
    function ClearTrades()
    {
        $sql = "DELETE FROM Trade WHERE MemberID={$this->memberID}";
        DatabaseAccess::Update($sql); 
        
        $sql = "UPDATE Member SET AccountValue=100000,WeeklyAccountCapped = 100000,WeeklyAccount=100000,WeeklyTrades=0 WHERE MemberID={$this->memberID}";
        DatabaseAccess::Update($sql); 
        
        $this->UpdateTrades(); 
    }
    
    function UpdateAllDBValues()
    {
        $sql = "UPDATE Member SET ";
        $sql.= "Name=:name,";
        $sql.= "RealName=:real_name,";
        $sql.= "EmailAddress=:email,";
        $sql.= "AboutMe=:about_me,";
        $sql.= "Website=:website,";
        $sql.= "Address1=:address1,";
        $sql.= "Address2=:address2,";
        $sql.= "ZipCode=:zip_code,";
        $sql.= "City=:city,";
        $sql.= "State=:state,";
        $sql.= "Country=:country,";
        $sql.= "Birthday=:birthday";
        $sql.= " WHERE MemberID=:id";
        
        $params = array(
        	':name' => $this->name, 
        	':real_name' => $this->realName,
        	':email' => $this->emailAddress,
			':about_me' => $this->aboutMe,
        	':website' => $this->website,
        	':address1' => $this->address1,
        	':address2' => $this->address2,
        	':zip_code' => $this->zipCode,
        	':city' => $this->city,
        	':state' => $this->state,
        	':country' => $this->country, 
        	':birthday' => $this->birthday,
        	':id' => $this->memberID);
        DatabaseAccess::Update($sql, $params);
    }
    
    function Static_GetMemberFromMemberID($memberID)
    {
        if (!$memberID || !is_numeric($memberID)) return false;
        $member = new Member($memberID);
        return ($member->memberID) ? $member : false;
    }

    function Static_GetMemberFromEmailAddress($email)
    {
        if (!$email) return false;
        $results = DatabaseAccess::Select("select * from Member where EmailAddress=:email", array(':email' => $email) );
    	if (count($results) == 0) return false;
        
        $member = new Member;
        $member->PopulateFromDataRow($results[0]);
        return $member;
    }

    function Static_GetMemberFromMemberName($name)
    {
        if (!$name) return false;
        $results = DatabaseAccess::Select("select * from Member where Name=:name", array(':name' => $name) );
    	if (count($results) == 0) return false;
        
        $member = new Member;
        $member->PopulateFromDataRow($results[0]);
        return $member;
    }
    
    function Static_GetMemberFromLogin($name, $password)
    {
        if (!$name) return false;
        if (!$password) return false;
        
        $sql = "select * from Member where Name=:name AND Password=:password";
        
        $results = DatabaseAccess::Select($sql, array(':name' => $name, ':password' => Member::HashPassword($password) ) );
    	if (count($results) == 0) return false;
        
        $member = new Member;
        $member->PopulateFromDataRow($results[0]);
        return $member;
    }
    
    function GetGroups($isConfirmed = "1")
    {
    	$arrGroups = array();
        $sql = "SELECT GroupUser.GroupID FROM GroupUser INNER JOIN Member ON GroupUser.UserEmail=Member.EmailAddress WHERE GroupUser.IsConfirmed={$isConfirmed} AND Member.MemberID={$this->memberID} ORDER BY GroupUser.GroupUserID ASC";
	    $results = DatabaseAccess::Select($sql);
		foreach($results as $row)
        {
		    $group = new Group($row['GroupID']);
            $arrGroups[$group->groupID] = $group;        
        } 
		return $arrGroups;
    }
    
    function SetAccount($account)
    {
    	return $this->UpdateDBValue("AccountValue", $account);
    }

    function UpdateDBValue($fieldName, $value)
    {
        $sql = "UPDATE Member SET $fieldName = :value WHERE MemberID=:id";
        DatabaseAccess::Update($sql, array(':value' => $value, ':id' => $this->memberID) );
    }
    
    function DoOnLogin()
    {
        $this->UpdateDBValue("LastLogin", ConvertTimestampToMySqlDate(GetThisTime()));
        setcookie("SignedMemberID", $this->memberID, GetThisTime()+ (3600*24*30) );
        $_SESSION['SignedMemberID']  = $this->memberID;
    }

    function DoOnRegister()
    {
        $to = $this->emailAddress;
        $sourceSite = (function_exists("GetPartnerSiteName")) ? GetPartnerSiteName() : "FakeMoneyRealStocks.com";
        $supportEmail = (function_exists("GetPartnerSiteEmail")) ? GetPartnerSiteEmail() : "info@FakeMoneyRealStocks.com";
        $fromEmail = (function_exists("GetPartnerSiteName")) ? GetPartnerSiteName() . "@fakemoneyrealstocks.com" : "info@FakeMoneyRealStocks.com";
        $link = (function_exists("GetPartnerWidgetUrl")) ? GetPartnerWidgetUrl() : "http://www.FakeMoneyRealStocks.com";
        
        $subject = "Thank you for registering for the {$sourceSite} Trading Game";
        $body = "Thank you for registering for the <a href='{$link}'>{$sourceSite} Trading Game</a>.<br><br>";
    	$body.= "Your username is {$this->name}<br>";
        if (IS_WIDGET)
        {
        	$body.= "Remember that every Sunday, the Game starts over and each registered trader begins fresh with $100,000 again.  You'll have up to 25 trades to make as much money as possible.  Once you’ve made 25 trades, you can continue to play of course, but your balance at the 25th trade is what counts for the contest.<br><br>";
            $body.= "If you’d like to view a running total of your lifetime profit, login and you'll find your total by selecting \"Lifetime Leaderboard\", located under the Weekly Profit Leaderboard.<br><br>";
        }
        else
        {
        	$body.= "<a href=\"http://www.FakeMoneyRealStocks.com/login.php\">Log in to FakeMoneyRealStocks.com</a> now to test your skills against the stock market.<br><br>";
        }
        $body.= "Please address any questions, suggestions, and comments to {$supportEmail}.<br><br>Beware, this Game is addictive! Enjoy!";
        return SendEmail($to, $subject, $body);
    }

}
?>
