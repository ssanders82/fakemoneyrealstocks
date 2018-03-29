<form action="" onsubmit="return false" name="tradeform">
    <div id="whatwould" style="height:55px;width:100%;text-align:center">
    <b>What would you do with this stock?</b><br>
    <button onclick="WV1A.StartTrade(1)" id="btnBuy" class="topOptions">Buy >></button>&nbsp;
    <button onclick="WV1A.StartTrade(0)" id="btnSkip" class="topOptions">Skip</button>&nbsp;
    <button onclick="WV1A.StartTrade(2)" id="btnSell" class="topOptions">Sell >></button>
    </div>
    
    <div id="continuetrade" style="height:55px;width:100%;text-align:center;display:none">
    <b>What do you want to do now?</b><br>
    <button onclick="WV1A.ContinueTrade(this.form, true)" id="btnClose" class="topOptions">Close Trade</button>&nbsp;
    <button onclick="WV1A.ContinueTrade(this.form, false)" id="btnNext" class="topOptions">Next Day >></button>
    </div>
    
    <div id="newtrade" style="display:none;height:45px;width:100%;text-align:center;"></div>
    
	<div style="height:45px;font-weight:bold;font-size:10pt;line-height:160%">
    Entry Price: <span id="StartPrice"><? echo $trade->GetEntryPrice() ?></span> <span id="StartDate"></span><span id="EndPrice"></span><br>
    <span id="StockResults">Industry: <? echo $trade->stock->industry ?></span>
    </div>
    
    <div style="height:20px"><img src="/images/arrow.png" style="position:relative;top:12px;left:423px;display:none;z-index:1000" id="ArrowImage" /></div>
    
    <div class="WV1_ChartImageDiv" style="width:<? echo CHART_IMAGE_WIDTH  ?>px;height:<? echo $trade->ChartHeight()  ?>px;position:relative;border:2px solid #666666;overflow:hidden">
    
    <img src="<? echo $trade->StartChartSrc() ?>" style="" id="ChartImage"/>
    <img src="<? echo SITE_DOMAIN ?>images/loading_animation.gif" style="width:208px;height:13px;position:relative;top:-230px;left:150px;z-index:1000;display:none" id="Loading" />
    </div>
    
    
</form>
			