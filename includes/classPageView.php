<?
class PageView
{
    var $signedMember = false;
    var $lookingAtMember = false;
    var $isMemberPage = false;
    var $isSignedPage = false;
    var $isOwnPage = false;
    var $viewerID;
    var $sessionID;
    var $userAgent;
    var $ipAddress;
    var $referrer;
    var $isGoogle;
    var $numAdsense=0;
    var $pageID;
    var $adsenseStyle;
    var $uri;
    var $server;
    var $filePath;
    var $queryString;
    var $colorStyle = "";
    var $adsenseChannel = "";
    var $colorBorder = "";
    var $colorBg     = "";
    var $colorLink   = "";
    var $colorUrl    = "";
    var $colorText   = "";
    var $fileName    = "";

    //var $document;
    
    function PageView()
    {
    }
    
    function IsPremium()
    {
    	return true; // TODO
        return $this->signedMember && $this->signedMember->memberLevel > 0; 
    }

    function GetPageData()
    {
        //phpinfo();exit;
        // They must be logged in as the "looking at" member to view this
        if ($this->isOwnPage)
        {
            $this->isSignedPage = true;
            $this->isMemberPage = true;
        }
        if (isset($_SESSION['SignedMemberID']) && $_SESSION['SignedMemberID'] != "")
        {
            $this->signedMember = new Member($_SESSION['SignedMemberID']);
        }
        elseif (isset($_COOKIE['SignedMemberID']) && $_COOKIE['SignedMemberID'] != "")
        {
            $this->signedMember = new Member($_COOKIE['SignedMemberID']);
            $_SESSION['SignedMemberID'] = $_COOKIE['SignedMemberID'];
        }
        
        if ($this->signedMember && !$this->signedMember->memberID)
        {
            $this->signedMember = false;
            $_SESSION['SignedMemberID'] = "";
            setcookie("SignedMemberID", "", GetThisTime()+ (3600*24*30) );
            session_write_close();
            Redirect("/index.php");
        }

        // Name of file
        $this->uri           = $_SERVER["REQUEST_URI"];
        $this->filePath      = strtolower($_SERVER["PHP_SELF"]);
        $this->fileName 	 = basename($_SERVER["SCRIPT_NAME"]);
        $this->sessionID     = session_id();
        $this->userAgent     = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
        $this->ipAddress     = $_SERVER["REMOTE_ADDR"];
        $this->referrer      = getenv('HTTP_REFERER');
        $this->queryString   = $_SERVER["QUERY_STRING"];

        // Make sure we're looking at a valid member
        if ($this->isMemberPage)
        {
            $tmpMemberName = $_GET['member'];
            if (is_numeric($tmpMemberName) && $member = Member::Static_GetMemberFromMemberID($tmpMemberName))
            {
                $this->lookingAtMember = $member;
                if ($this->signedMember->memberID == $this->lookingAtMember->memberID) $this->isOwnPage = true;
            }
            elseif ($member = Member::Static_GetMemberFromMemberName($tmpMemberName))
            {
                $this->lookingAtMember = $member;
                if ($this->signedMember && $this->signedMember->memberID == $this->lookingAtMember->memberID) $this->isOwnPage = true;
            }
            else
            {
                CatchError("Member name $tmpMemberName does not exist");
            }
        } // end if is member page

        // if it's a signed page, make sure only logged in members can see it
        if ($this->isSignedPage && !$this->signedMember)
        {
            //CatchError("You don't have valid permissions", "");
            $redirect = "/login.php?refpage=" . urlencode($this->uri);
            Redirect($redirect);
        }

        // Make sure signed id equals member name from directory
        if ($this->isOwnPage && $this->signedMember->memberID != $this->lookingAtMember->memberID)
        {
            //CatchError("Invalid permissions");
            $redirect = "/login.php?refpage=" . urlencode($this->uri);
            Redirect($redirect);
        }
       
        //if ($logPageView) $this->LogPageView();
    } // end function GetPageData

    function LogPageView()
    {
    	return;
        $currentTime   = GetThisTime();
        $currentDate	= ConvertTimestampToMySqlDate($currentTime);
        $signedID = ($this->signedMember) ? $this->signedMember->memberID : 0;
        $sql = "INSERT INTO PageView(ViewDate,SignedID,ViewerID,";
        $sql.= "SessionID,Referrer,UserAgent,IPAddress,Document,URI,Page)";
        $sql.= " VALUES ('$currentDate','$signedID','$this->viewerID','$this->sessionID',";
        $sql.= "'$this->referrer','$this->userAgent','$this->ipAddress','$this->document','$this->uri','$this->fileName')";
        //echo $sql;
        mysql_query($sql);

        $this->pageID = mysql_insert_id();
        $_SESSION['pageID'] = $this->pageID;
    }
}  // end class PageView
?>