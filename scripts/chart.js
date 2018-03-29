var WV1 = {};

WV1.accountText = "";
WV1.sessionNumTradesText = "";
WV1.sessionProfitText = "";
WV1.globalRequireRegister = false;
WV1.doShowRegisterThisTime = false;
WV1.nextChartImgPreloader = new Image();
WV1.currentTrade = new Object();
WV1.nextTrade = new Object();
WV1.isAsync = false;
WV1.asyncStartTime = 0;
WV1.leaderboardText = "";
WV1.HasShownWinnerPopup = false;
WV1.HasShownLoserPopup = false;
WV1.IsWidget = false;

WV1.ShowUpgradePopup = function(warning)
{
	window.scrollTo(0,0);
    var url = WV1SiteDir + "popups/upgrade_popup.php?a=1"
    if (warning) url += "&warning=1";
    url+= "&TB_iframe=true&height=430&width=550";
	tb_show("Upgrade", url, false);
	$("#TB_window").bind("unload", function() {WV1.CloseUpgradePopup(); } );
}

WV1.CloseUpgradePopup = function()
{
    tb_remove();
	$("#TB_window").unbind();
}


WV1.ShowRegisterPopup = function(warning)
{
	window.scrollTo(0,0);
    var url = WV1SiteDir + "popups/register_popup.php?a=1"
    if (warning) url += "&warning=1";
    url+= "&TB_iframe=true&height=430&width=550";
	tb_show("Register", url, false);
	$("#TB_window").bind("unload", function() {WV1.CloseLoginPopup(); } );
}

WV1.ShowLoginPopup = function()
{
	window.scrollTo(0,0);
    var url = WV1SiteDir + "popups/login_popup.php?TB_iframe=true&height=390&width=520";
	tb_show("Login", url, false);
	// Bind event to popup close
	$("#TB_window").bind("unload", function() {WV1.CloseLoginPopup(); } );
}

WV1.ShowLogoutPopup = function()
{
	window.scrollTo(0,0);
    var url = WV1SiteDir + "popups/logout_popup.php?TB_iframe=true&height=390&width=520";
	tb_show("Logout", url, false);
	// Bind event to popup close
	$("#TB_window").bind("unload", function() {WV1.CloseLoginPopup(); } );
}

WV1.CloseLoginPopup = function()
{
    tb_remove();
	$("#TB_window").unbind();
	url = WV1SiteDir + "async/asyncLoadSignedAccount.php";
	$.getScript(url);
}

WV1.ReloadPage = function()
{
	window.location = document.location;
}

// Update settings for a chart in the middle of a trade
WV1.UpdateChart = function()
{
	f = document.forms["wv1settings"];
	//$('#btnBuy').get(0).disabled = true;
    //$('#btnSkip').get(0).disabled = true;
    //$('#btnSell').get(0).disabled = true;
    $('#Loading').show();
    if ($('#whatwould').is(":visible"))
    {
        // Update current chart
        var url = WV1SiteDir + "async/asyncUpdateChart.php?src=index&" + $(f).serialize();
	    $.getScript(url);
    }
    else
    {
        // "Skip" current and grab next chart
        var url = WV1SiteDir + "async/asyncAjax.php?src=index&tradeType=0&" + $(f).serialize();
	    $.getScript(url);
    }
}

WV1.ShowNextTrade = function()
{
	if (WV1.globalRequireRegister && WV1.doShowRegisterThisTime)
    {
    	WV1.doShowRegisterThisTime = false;
        WV1.ShowRegisterPopup(true);
    }
    else
    { 
    	$('#btnNewTrade').attr("disabled", "disabled");
        WV1.ChangeChart(WV1.nextTrade.startChartSrc, nextTrade.startChartHeight, WV1.ShowNewTradeCallback);
    }
}

WV1.ShowNewTradeCallback = function()
{
    $('#StartPrice').html(WV1.nextTrade.entryPrice);
	$('#newtrade').hide();
    $('#whatwould').show();
    $('#StockResults').html("Industry: " + WV1.nextTrade.industry);
    $('#StartDate').empty();
    $('#EndPrice').empty();
    $('#LastTrade').empty();
    $('#Compare').hide();
	
	$('#btnBuy').get(0).disabled = false;
    $('#btnSkip').get(0).disabled = false;
    $('#btnSell').get(0).disabled = false;
}

WV1.ShowOldChart = function()
{
	$('#Loading').show();
	$('#ChartImage').unbind().load(function() {
        $('#Loading').hide();})
	.attr("src", WV1.currentTrade.startChartSrc);
	return false;
}

WV1.ShowNewChart = function()
{
	$('#Loading').show();
	$('#ChartImage').unbind().load(function() {
        $('#Loading').hide();})
	.attr("src", WV1.currentTrade.endChartSrc);
	return false;
}

WV1.ChangeChart = function(newSrc, newHeight, callback, param)
{
	$img = $('#ChartImage');
    $img.unbind('load');
    $img.load(function() {
        $('#Loading').hide();
        $(".WV1_ChartImageDiv").height(newHeight + 'px');
        if (typeof callback != 'undefined') callback(param);
	});
    
	$img.attr('src', newSrc);    
}

WV1.StartTrade = function(tradeType)
{
	f = document.forms["wv1settings"];
	$('#btnBuy').get(0).disabled = true;
    $('#btnSkip').get(0).disabled = true;
    $('#btnSell').get(0).disabled = true;
    
	$('#Loading').show();
    var url = WV1SiteDir + "async/asyncAjax.php?src=index&tradeType=" + tradeType + "&" + $(f).serialize();
	$.getScript(url);
}

WV1.ShowTradeResultsCallback = function()
{
	WV1.nextChartImgPreloader.src = WV1.nextTrade.startChartSrc;
	var stockColor = WV1.currentTrade.change > 0 ? "green" : (WV1.currentTrade.change == 0? "black" : "red");
  
	var tradeLength = WV1.currentTrade.tradeLength;
	var profitNum = WV1.currentTrade.profit.replace(',','').replace('$','');
    var profitColor = profitNum > 0 ? "green" : (profitNum == 0? "black" : "red");
  
    var message = profitNum > 0 ? "Nice!" : (profitNum == 0? "" : "Whoops...");
    message = "<span style='font-size:14pt;color:" + profitColor + "'>" + message + "</span>";
  
    $('#StartDate').html(" on " + WV1.currentTrade.startDateString + "");
    $('#EndPrice').html(", Exit Price: <span id='ExitPrice'>" + WV1.currentTrade.exitPrice + "</span> on " + WV1.currentTrade.endDateString + "");
	
    var resultsHtml = "Stock: <span id='stocktitle'>" + WV1.currentTrade.company + 
		"</span>, <strong>change:</strong> <span style='font-size:14pt;color:" + stockColor + "'>" + 
    	WV1.currentTrade.change  + "%</span> in " + tradeLength + " days";
    
    $('#StockResults').html(resultsHtml);
    $('#Account').html(WV1.accountText);
    $('#LastTrade').html("(<span style='font-weight:bold;color:" + profitColor + "'>Last trade: " + WV1.currentTrade.profit + "</span>)");
    
    $('#whatwould').hide();
    $('#newtrade').show().html(message + " <button id='btnNewTrade' class='topOptions' onclick='WV1.ShowNextTrade(\"" + WV1.nextTrade.startChartSrc + "\", \"" + WV1.nextTrade.entryPrice  + "\")'>Next Trade</button>");
    $('#WSV1_NumTrades').html(WV1.sessionNumTradesText);
	$('#WSV1_Profit').html(WV1.sessionProfitText);
    
    $('#Compare').show();
    
    if (WV1.leaderboardText != "" && WV1.leaderboardText != undefined)
    {
        $('#WV1Leaderboard').html(WV1.leaderboardText);
    }
    
    if (WV1.IsWidget && WV1SessionStartAccount != undefined)
    {
    	//alert('trying...' + WV1SessionStartAccount + ", " + WV1.accountText);
    	var currAccount = WV1.accountText.replace('$','').replace(',','');
        if (WV1WinnerPopupRatio != "" && !isNaN(WV1WinnerPopupRatio))
        {
            if (currAccount > WV1WinnerPopupRatio * WV1SessionStartAccount && !WV1.HasShownWinnerPopup)
            {
            	WV1.HasShownWinnerPopup = true;
            	WV1.ShowPopup("popups/winner_popup.php", "Congratulations", 250, 500);
            }
        }
        if (WV1LoserPopupRatio != "" && !isNaN(WV1LoserPopupRatio))
        {
            if (currAccount < WV1LoserPopupRatio * WV1SessionStartAccount & !WV1.HasShownLoserPopup)
            {
            	WV1.HasShownLoserPopup = true;
            	WV1.ShowPopup("popups/loser_popup.php", "Uh-oh", 250, 500);
            }
        }
    }
}

WV1.ExecuteTradeAsyncCallback = function(currentTrade, nextTrade, accountText, sessionNumTradesText, sessionProfitText, doRequireRegister, leaderboardText)
{
	WV1.currentTrade = currentTrade;
	WV1.nextTrade = nextTrade;
	WV1.accountText = accountText;
	WV1.sessionNumTradesText = sessionNumTradesText;
	WV1.sessionProfitText = sessionProfitText;
	WV1.globalRequireRegister = doRequireRegister;
	WV1.doShowRegisterThisTime = doRequireRegister;
    WV1.currentTrade.startChartSrc = $('#ChartImage').attr('src');
    
    if (leaderboardText === undefined) leaderboardText = "";
    WV1.leaderboardText = leaderboardText;

    WV1.ChangeChart(WV1.currentTrade.endChartSrc, nextTrade.startChartHeight, WV1.ShowTradeResultsCallback);
}

WV1.FadeContainerOut = function()
{
	$("select").hide();
	$("#WV1Container, .WV1_ChartImageDiv").unbind().fadeTo(200, 0.25);
}

WV1.FadeContainerIn = function()
{
	$("select").show();
	$("#WV1Container, .WV1_ChartImageDiv").unbind().fadeTo(200,1);
}

WV1.StartAdvancedTrading = function()
{
	WV1.FadeContainerOut();

    var url = WV1SiteDir + "async/asyncStartAdvanced.php"
	$.getScript(url, function(){
		WV1.FadeContainerIn();
		$("#WV1_QuickTradingLink").show();
		$("#WV1_AdvTradingLink").hide();
	});
}

WV1.StartQuickTrading = function()
{
	WV1.FadeContainerOut();
    var url = WV1SiteDir + "async/asyncStartBasic.php"    
	$.getScript(url, function(){
		WV1.FadeContainerIn();
		$("#WV1_QuickTradingLink").hide();
		$("#WV1_AdvTradingLink").show();
	});
}

WV1.ResetAccount = function()
{
	if (!confirm('This will reset your account value and you will be uneligible for this week\'s leaderboard. Are you still sure you want to do it?')) return false;
    
    if (WV1.IsWidget && WV1SessionStartAccount != undefined)
    {
    	WV1SessionStartAccount = 100000;
    }
	WV1.FadeContainerOut();
    
    var url = WV1SiteDir + "async/asyncResetAccount.php" 
	$.getScript(url, function(){
  		WV1.FadeContainerIn();
	});
}