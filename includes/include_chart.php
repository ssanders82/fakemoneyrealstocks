<form action="" onsubmit="return false" name="tradeform">
    <div id="whatwould" style="height:55px;width:100%;text-align:center">
	<b>What would you do with this stock?</b><br>
	<button onclick="WV1.StartTrade(1)" id="btnBuy" class="topOptions">Buy</button>&nbsp;
	<button onclick="WV1.StartTrade(0)" id="btnSkip" class="topOptions">Skip</button>&nbsp;
	<button onclick="WV1.StartTrade(2)" id="btnSell" class="topOptions">Sell</button>
    </div>
    
    <div id="newtrade" style="display:none;height:55px;width:100%;text-align:center;"></div>

    <div style="height:45px;font-weight:bold;font-size:10pt;line-height:160%">
	Entry Price: <span id="StartPrice"><? echo $trade->GetEntryPrice() ?></span> 
	<span id="StartDate"></span><span id="EndPrice"></span><br>
	<span id="StockResults">Industry: <? echo $trade->stock->industry ?></span>
    </div>

<div class="WV1_ChartImageDiv" style="width:<? echo CHART_IMAGE_WIDTH  ?>px;height:<? echo $trade->ChartHeight()  ?>px;position:relative;border:2px solid #666666;overflow:hidden">

	<div style="position:absolute;top:5px;left:5px;z-index:10;background:white;padding:5px;border:2px solid #333333;font-weight:bold;display:none" id="Compare">
		<a href="#" onclick="return WV1.ShowOldChart()">Before Chart</a> | 
		<a href="#" onclick="return WV1.ShowNewChart()">After Chart</a>
	</div>

	<img src="<? echo $trade->StartChartSrc() ?>" style="" id="ChartImage"/>
	<img src="<? echo SITE_DOMAIN ?>images/loading_animation.gif" style="width:208px;height:13px;position:relative;top:-210px;left:135px;z-index:1000;display:none" id="Loading" />
</div>
</form>