var WV1A = {};

WV1A.accountText = "";
WV1A.sessionNumTradesText = "";
WV1A.sessionProfitText = "";
WV1A.globalRequireRegister = false;
WV1A.doShowRegisterThisTime = false;
WV1A.nextChartImgPreloader = new Image();
WV1A.currentTrade = new Object();
WV1A.nextTrade = new Object();
WV1A.leaderboardText = "";

WV1A.ReloadPage = function()
{
	window.location = document.location;
}

WV1A.ShowNextTrade = function()
{
    $('#btnNewTrade').attr("disabled", "disabled");
    $('#btnBuy').attr("disabled", "disabled");
    $('#btnSkip').attr("disabled", "disabled");
    $('#btnSell').attr("disabled", "disabled");
    
	WV1A.ChangeChart(WV1A.nextTrade.startChartSrc, nextTrade.startChartHeight, WV1A.ShowNewTradeCallback);
}

WV1A.ShowNewTradeCallback = function()
{
	$('#StartPrice').html(WV1A.nextTrade.entryPrice);
	$('#newtrade').hide();
    $('#whatwould').show();
    $('#StockResults').html("Industry: " + WV1A.nextTrade.industry);
    $('#StartDate').empty();
    $('#EndPrice').empty();
    $('#LastTrade').empty();
    $('#Compare').hide();
	
    $('#btnBuy').get(0).disabled = false;
    $('#btnSkip').get(0).disabled = false;
    $('#btnSell').get(0).disabled = false;	
	$('#continuetrade').hide();
}

// Update settings for a chart in the middle of a trade
WV1A.UpdateChart = function()
{
	f = document.forms["wv1settings"];
    $('#Loading').show();
    if ($('#whatwould').is(":visible") || $('#continuetrade').is(":visible"))
    {
        // Update current chart
        var url = WV1SiteDir + "async/syncUpdateChart.php?src=index&advanced=1&" + $(f).serialize();
	    $.getScript(url);
    }
    else
    {
        // "Skip" current and grab next chart
        // TODO???
        var url = WV1SiteDir + "async/asyncAjaxAdv.php?src=index&new=1&tradeType=0&" + $(f).serialize();
	    $.getScript(url);
    }
}

WV1A.ChangeChart = function(newSrc, newHeight, callback, param)
{    
	$img = $('#ChartImage');
    $img.unbind('load');
    $img.load(function() {
        $('#Loading').hide();
        if (newHeight > 0) $(".WV1_ChartImageDiv").height(newHeight + 'px');
        if (typeof callback != 'undefined') callback(param);
	});
	$('#Loading').show();
	$img.attr('src', newSrc);    
}

WV1A.StartTrade = function(tradeType)
{
	f = document.forms["wv1settings"];
	$('#btnBuy').get(0).disabled = true;
    $('#btnSkip').get(0).disabled = true;
    $('#btnSell').get(0).disabled = true;
    
	$('#Loading').show();
    var url = WV1SiteDir + "async/asyncAjaxAdv.php?src=adv&new=1&tradeType=" + tradeType + "&" + $(f).serialize();
	$.getScript(url);
}

WV1A.ContinueTrade = function(f, isClose)
{
    f = document.forms["wv1settings"];
	$('#btnNext').get(0).disabled = true;
    $('#btnClose').get(0).disabled = true;
    
	$('#Loading').show();
    var url = WV1SiteDir + "async/asyncAjaxAdv.php?src=adv&" + (isClose ? "close=1" : "continue=1") + "&" + $(f).serialize();
	$.getScript(url);
}

WV1A.ContinueTradeAsyncCallback = function(currentTrade, accountText, sessionNumTradesText, sessionProfitText, leaderboardText)
{
	WV1A.currentTrade = currentTrade;
    WV1A.accountText = accountText;
	WV1A.sessionNumTradesText = sessionNumTradesText;
	WV1A.sessionProfitText = sessionProfitText;
    
    if (leaderboardText === undefined) leaderboardText = "";
    WV1A.leaderboardText = leaderboardText;
    
    
	WV1A.ChangeChart(WV1A.currentTrade.endChartSrc, 0, WV1A.ShowContinueTradeResultsCallback);
}

WV1A.ShowContinueTradeResultsCallback = function()
{
	$('#btnNext').get(0).disabled = false;
    $('#btnClose').get(0).disabled = false;
	
	var stockColor = WV1A.currentTrade.change > 0 ? "green" : (WV1A.currentTrade.change == 0? "black" : "red");
	var profitNum = WV1A.currentTrade.profit.replace(',','').replace('$','');
    var profitColor = profitNum > 0 ? "green" : (profitNum == 0? "black" : "red");
	
    profitNum = WV1A.currentTrade.profit.replace(',','').replace('$','');
    profitColor = profitNum > 0 ? "green" : (profitNum == 0? "black" : "red");
    $('#EndPrice').html(", Current Price: <span id='ExitPrice'>" + WV1A.currentTrade.exitPrice + "</span>");
    
    var resultsHtml = "<strong>change:</strong> ";
    resultsHtml += "<span style='font-size:14pt;color:" + (WV1A.currentTrade.change>0?"green":"red") + "'>";
    resultsHtml += WV1A.currentTrade.change + "%</span> in " + WV1A.currentTrade.tradeLength + " days";
    $('#StockResults').html(resultsHtml);
    $('#Account').html(WV1A.accountText);
    $('#LastTrade').html("(<span style='font-weight:bold;color:" + profitColor + "'>Current trade: " + WV1A.currentTrade.profit + "</span>)");
    
    $('#whatwould').hide();
    $('#continuetrade').show();
    
    if (WV1A.leaderboardText != "" && WV1A.leaderboardText != undefined)
    {
        $('#WV1Leaderboard').html(WV1A.leaderboardText);
    }
    $('#Account').html(WV1A.accountText);
    $('#WSV1_NumTrades').html(WV1A.sessionNumTradesText);
	$('#WSV1_Profit').html(WV1A.sessionProfitText);
}

WV1A.ExecuteTradeAsyncCallback = function(currentTrade, nextTrade, accountText, sessionNumTradesText, sessionProfitText, leaderboardText)
{
	WV1A.currentTrade = currentTrade;
	WV1A.nextTrade = nextTrade;
	WV1A.accountText = accountText;
	WV1A.sessionNumTradesText = sessionNumTradesText;
	WV1A.sessionProfitText = sessionProfitText;
    
    if (leaderboardText === undefined) leaderboardText = "";
    WV1A.leaderboardText = leaderboardText;
    
	if (WV1A.currentTrade.tradeType == 0)
	{
		// If it's a skip, show them 20 days ahead
		WV1A.ChangeChart(WV1A.currentTrade.endChartSrc, WV1A.nextTrade.startChartHeight, WV1A.ExecuteTradeResultsCallback);
	}
	else
	{
		//$('#Loading').hide();
		//WV1A.ExecuteTradeResultsCallback();
        WV1A.ChangeChart(WV1A.currentTrade.endChartSrc, WV1A.nextTrade.startChartHeight, WV1A.ExecuteTradeResultsCallback);
	}
}

WV1A.ExecuteTradeResultsCallback = function()	
{
    WV1A.currentTrade.startChartSrc = $('#ChartImage').attr('src');

	$('#btnNext').get(0).disabled = false;
    $('#btnClose').get(0).disabled = false;

	var stockColor = WV1A.currentTrade.change > 0 ? "green" : (WV1A.currentTrade.change == 0? "black" : "red");
	var profitNum = WV1A.currentTrade.profit.replace(',','').replace('$','');
    var profitColor = profitNum > 0 ? "green" : (profitNum == 0? "black" : "red");

    $('#StartDate').html(" (" + WV1A.currentTrade.startDateString + ")");
    $('#EndPrice').html(", Exit Price: <span id='ExitPrice'>" + WV1A.currentTrade.exitPrice + "</span> (" + WV1A.currentTrade.endDateString + ")");
	

	var resultsHtml = "Stock: <span id='stocktitle'>" + WV1A.currentTrade.company + 
		"</span>, <strong>change:</strong> <span style='font-size:14pt;color:" + (WV1A.currentTrade.change>0?"green":"red") + "'>" + 
    	WV1A.currentTrade.change + "%</span> in " + WV1A.currentTrade.tradeLength + " days";		
    
    $('#StockResults').html(resultsHtml);
    $('#Account').html(WV1A.accountText);
    $('#LastTrade').html("(<span style='font-weight:bold;color:" + profitColor + "'>Last trade: " + WV1A.currentTrade.profit + "</span>)");
    
    $('#whatwould').hide();
	$('#continuetrade').hide();
    $('#newtrade').show().html("<button id='btnNewTrade' class='topOptions' onclick='WV1A.ShowNextTrade(\"" + WV1A.nextTrade.startChartSrc + "\", \"" + WV1A.nextTrade.entryPrice  + "\")'><b>New Trade</b></button>");
    $('#WSV1_NumTrades').html(WV1A.sessionNumTradesText);
	$('#WSV1_Profit').html(WV1A.sessionProfitText);
	
    if (WV1A.leaderboardText != "" && WV1A.leaderboardText != undefined)
    {
        $('#WV1Leaderboard').html(WV1A.leaderboardText);
    }
    
	WV1A.nextChartImgPreloader = WV1A.nextTrade.startChartSrc;
}