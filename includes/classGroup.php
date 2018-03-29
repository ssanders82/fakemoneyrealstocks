<?
class Group
{
    var $groupID = 0;
    var $name = "";
    var $adminID = 0;
    var $dateAdded = DATE_BLANK;
    var $referrer = "";
    
    function __construct($groupID = 0)
    {
        if ($groupID) $this->PopulateFromDatabase($groupID);
    }

    function InsertIntoDatabase()
    {
        $this->dateAdded = ConvertTimestampToMySqlDate(GetThisTime());
        if (defined("REMOTE_SITE_CODE")) $this->referrer = REMOTE_SITE_CODE;

        $sql = "INSERT INTO TradingGroup (Name,AdminID,Referrer,DateAdded) VALUES (:name,:admin_id,:referrer, :date_added)";
        $params = array(':name' => $this->name, ':admin_id' => $this->adminID, ':referrer' => $this->referrer, ':date_added' => $this->dateAdded);
        
        $this->groupID = DatabaseAccess::Insert($sql, $params);
        
        if ($this->groupID && $this->adminID)
        {
        	$member = new Member($this->adminID);
            DatabaseAccess::Insert("INSERT INTO GroupUser (GroupID,UserEmail,IsConfirmed) VALUES ('{$this->groupID}', '{$member->emailAddress}', '1')");
        }
        return $this;
    }
    
    function PopulateFromAuthCode($authCode)
    {
    	$sql = "SELECT TradingGroup.* FROM TradingGroup INNER JOIN GroupUser ON GroupUser.GroupID=TradingGroup.GroupID WHERE AuthCode=:auth_code";
        $results = DatabaseAccess::Select($sql, array(':auth_code' => $authCode) );
        if (count($results) == 0) return false;
        return $this->PopulateFromDataRow($results[0]);
    }

    function PopulateFromDatabase($groupID)
    {
        if (!is_numeric($groupID)) return false;
        $sql = "SELECT * FROM TradingGroup WHERE GroupID=:id";
        $results = DatabaseAccess::Select($sql, array(':id' => $groupID) );
        if (count($results) == 0) return false;
        return $this->PopulateFromDataRow($results[0]);
    }
    
    function PopulateFromDataRow($row)
    {
        $this->groupID 		= $row->GroupID;
        $this->name  	    = $row->Name;
        $this->adminID 		= $row->AdminID;
        $this->dateAdded    = $row->DateAdded;
		$this->referrer		= $row->Referrer; 
        return true;
    }
    
    function InviteUser($invitorEmail, $inviteeEmail, $message)
    {
    	if ($this->GetGroupUserStatus($emailAddress) !== false) return;
        
        $authCode = md5(mt_rand() . mt_rand() . time());
        $sql = "INSERT INTO GroupUser (GroupID,UserEmail,AuthCode) VALUES (:group_id,:email,:auth_code)";
        DatabaseAccess::Insert($sql, array(':group_id' => $this->groupID, ':email' => $inviteeEmail, ':auth_code' => $authCode) );

        $url = GetPartnerWidgetUrl();
        if (strpos($url, "?") === false) $url .= "?ref=1";
        $url .= "&authCode=" . $authCode;
        
        $subject = GetPartnerSiteName() . " Trading Game Group Invite";
        $body = "You have been invited to a " . GetPartnerSiteName() . " Trading Game Group created by {$invitorEmail}";
        if ($message) $body.= "<br><br>{$invitorEmail} says,<br><br>\"{$message}\"";
        $body.= "<br><br><a href='{$url}'>View the invitation</a>.";
        
        $fromEmail = (function_exists("GetPartnerSiteName")) ? GetPartnerSiteName() . "@fakemoneyrealstocks.com" : ADMIN_EMAIL;
        SendEmail($inviteeEmail, $subject, $body);
    }
    
    function ConfirmUser($authCode)
    {
        $sql = "UPDATE GroupUser SET IsConfirmed=1 WHERE AuthCode=:auth_code'";
        DatabaseAccess::Update($sql, array(':auth_code' => $authCode));
    }
    
    function GetMessages()
    {
    	$sql = "SELECT * FROM GroupMessage WHERE GroupID={$this->groupID} ORDER BY DateAdded desc";
        $results = DatabaseAccess::Select($sql);
		return $results;
    }
    
    function AddMessage($memberID, $message)
    {
    	$sql = "INSERT INTO GroupMessage (GroupID,MemberID,Message,DateAdded) VALUES (:group_id,:member_id,:message,NOW())";
        DatabaseAccess::Insert($sql, array(':group_id' => $this->groupID, ':member_id' => $memberID, ':message' => $message) );
    }
    
    function GetGroupUserStatus($emailAddress)
    {
    	$sql = "SELECT * FROM GroupUser WHERE GroupID=:group_id AND UserEmail=:email";
        $results = DatabaseAccess::Select($sql, array(':group_id' => $this->groupID, ':email' => $emailAddress) );
        if (count($results)) return false;
        return $results[0]->IsConfirmed;
    }
    
    function GetGroupUsers($isConfirmed = "1")
    {
        $arrUsers = array();    
    	$sql = "SELECT * FROM GroupUser WHERE GroupID={$this->groupID} AND IsConfirmed=$isConfirmed";
        $results = DatabaseAccess::Select($sql);
        // If it's confirmed, return array of Member objects. Else return array of email addresses
        foreach ($results as $row)
        {
        	if ($isConfirmed == "1")
            {
                if ($user = Member::Static_GetMemberFromEmailAddress($row->UserEmail)) $arrUsers[] = $user;
            }
            else
            {
            	$arrUsers[] = $row->UserEmail;
            }
        }
		return $arrUsers;
    }
  
}
?>
