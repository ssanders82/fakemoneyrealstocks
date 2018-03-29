<?
$sessionNumTrades = isset($_SESSION['sessionNumTrades']) ? $_SESSION['sessionNumTrades'] : 0;
$sessionNumWinners = isset($_SESSION['sessionNumWinners']) ? $_SESSION['sessionNumWinners'] : 0;
$sessionProfit = isset($_SESSION['sessionProfit']) ? $_SESSION['sessionProfit'] : 0;
?>
<div id="sidebar">
	
    <h2>Session Stats</h2>
	<div id="SessionStats" style="padding:0px;margin:0px">
		<h3><b>Trades</b>: <span style="font-size:13pt" id="WSV1_NumTrades"><? echo $sessionNumTrades ?></span></h3>
		<h3>Profit: <span id="WSV1_Profit"><? echo FormatCurrencySide($sessionProfit) ?></span></h3>
	</div>
    
    <?
    if ($page->signedMember && !$page->signedMember->memberLevel)
    {
        ?><h3 style=""><a href="/upgrade.php">Why Upgrade?</a></h3><?
    }
    ?>
    <h3><a href="/">Home</a></h3>
    <h3><a href="/?boss">BOSS MODE!</a></h3>
    
    
    <h3><a href="/advanced.php">Day-by-day walkthrough</a></h3>
    <h3><a href="/invite.php">Invite your friends</a></h3>
    <?
    if ($page->signedMember)
    {
    	echo "<h3><a href='{$page->signedMember->dashboardUrl}'>My Dashboard</a></h3>";
        echo "<h3><a href='/logout.php'>Sign Out</a></h3>";
    }
    else
    {
    	echo "<h3><a href='/register.php'>Register</a></h3>";
        echo "<h3><a href='/login.php'>Sign In</a></h3>";
    }
    ?>
    <h3><a href="/leaderboard.php">Leaderboard</a></h3>
    <!--<h3><a href="/viewmembers.php">Members</a></h3>-->
    <!--<h3><a href="http://www.FakeMoneyRealStocks.com/forum/">Forum</a></h3>-->
    <!--<h3><a href="/partnerships.php">Partnerships</a></h3>-->
    <h3><a href="/contactus.php">Feedback</a></h3>
                
	<h2>Share</h2>
    
    <? if (false && !$page->signedMember) 
    {
    	echo '<div style="width:98%;text-align:center;line-height:180%"><span id="sideAds">';
    	require_once "asyncAds.php";
        echo "</span>";
       	echo "<br><a href='/login.php' class='small'>get rid of ads</a></div><br>"; 
	} ?>
    
    <div style="width:98%;text-align:center;line-height:180%">
    <a href="https://secure.del.icio.us/login?url=http%3A%2F%2Fwww.fakemoneyrealstocks.com&title=fakemoneyrealstocks.com%20-%20Tools%20for%20Traders&noui&partner&v=4" class="c1a"><img src="http://s7.addthis.com/services/delicious.png" width="16" height="16" border="0" alt="" /> Del.icio.us</a><br>
    <a href="http://www.stumbleupon.com/submit?url=http%3A%2F%2Fwww.fakemoneyrealstocks.com"> <img border=0 src="http://www.stumbleupon.com/images/stumble7.gif" alt="StumbleUpon Toolbar"></a><br>
    </div>
    
    <br>
    
	<h2>Newest Members</h2>
    <ul id="TopMembers">
    <?
    $topMembers = GetTopMembers();
    foreach ($topMembers as $data)
    {
        echo "<li>{$data}</li>\n";
    }
    ?>
    </ul>
   
		</div>
      
		<!-- end sidebar -->
		<div style="clear: both;">&nbsp;</div>
	
</div>
<!-- end page -->
<div id="footer">
	<p>&copy;2012 FakeMoneyRealStocks.com.</p>
</div>

