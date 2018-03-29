<?
$boss = isset($boss) && $boss;
$premium = $page->signedMember && $page->signedMember->memberLevel;
$advanced = isset($advanced) && $advanced;
?>

<script type="text/javascript">

function ChartSettingChange(el, defaultValue, requiresPremium, doUpdate)
{
    var isMember = <? echo ($page->signedMember ? "true" : "false") . ";\n"; ?>
    var isPremium = <? echo ($page->IsPremium() ? "true" : "false") . ";\n"; ?>
    
    if (requiresPremium && !isPremium)
    {
        if (isMember) WV1.ShowUpgradePopup(true);
        else WV1.ShowRegisterPopup(true);
        
        // Revert values
        if ($(el).attr('type') == 'checkbox') $(el).attr('checked',defaultValue)
        else $(el).val(defaultValue);
    }
    else if (doUpdate)
    {
        <? echo $advanced ? "WV1A.UpdateChart();" : "WV1.UpdateChart();" ?>
    }
}

function Toggle(el, otherIDs)
{
    if ($(el).attr('checked'))
    {
        for (i=0;i<otherIDs.length;i++)
        {
            $("#" + otherIDs[i]).attr('checked', false);
        }
    }
}

</script>

<form name="wv1settings">

<input type="hidden" name="boss" value="<? echo $boss ? '1' : '0' ?>" />
<div style="width:415px;border:2px solid orange" class="msgpanel">
<span class="title">Settings</span><br>
<table>
<?php
if (!$advanced)
{
    ?>
    <tr><td class="leftformcell">Trade length</td><td>&nbsp;</td> 
    <td style="text-align:left"><select id="tradeLength" name="tradeLength" <? if ($boss) echo 'disabled' ?> >
    <?
    $days = $boss ? 10 : $chartOptions['tradeLength'];
    $options = array(1,2,3,5,10,20);
    foreach ($options as $option)
    {
        $addl = $days == $option ? " selected='selected'" : "";
        echo "<option value='$option' $addl>$option days&nbsp;&nbsp;</option>\n";
    }
    ?>
    </select></td></tr>
    <?php
}
// Add hidden field so this is padded in call
if ($boss || $advanced) echo "<input type='hidden' name='tradeLength' value='{$chartOptions['tradeLength']}' />";
?>


<tr><td class="leftformcell">Position size</td><td>&nbsp;</td> 
<td style="text-align:left"><select id="tradeSize" name="tradeSize" onchange="ChartSettingChange(this, <? echo $chartOptions['tradeSize'] ?>, true, false)">
<?
$options = array(10,25,50,75,100);
foreach ($options as $option)
{
    $addl = $chartOptions['tradeSize'] == $option ? " selected='selected'" : "";
    echo "<option value='$option' $addl>{$option}%&nbsp;&nbsp;</option>\n";
}
?>
</select></td></tr>

<tr><td class="leftformcell">Chart length</td><td width="6">&nbsp;</td> 
<td style="text-align:left"><select id="chartLength" name="chartLength" onchange="ChartSettingChange(this, <? echo $chartOptions['chartLength'] ?>, false, true)">
<?
$options = array(60,90,120,250);
foreach ($options as $option)
{
    $addl = $chartOptions['chartLength'] == $option ? " selected='selected'" : "";
    echo "<option value='$option' $addl>$option days&nbsp;&nbsp;</option>\n";
}
?>
</select></td></tr>

<tr valign="top"><td class="leftformcell">Upper Indicators</td><td>&nbsp;</td>
<td style="text-align:left">
<input type="checkbox" name="ma20" id="ma20" onclick="ChartSettingChange(this, true, false, true)" <?php if ($chartOptions['ma20']) echo "checked='checked'" ?> /> <label for="ma20">20-day moving average</label><br />
<input type="checkbox" name="ma50" id="ma50" onclick="ChartSettingChange(this, false, true, true)" <?php if ($chartOptions['ma50']) echo "checked='checked'" ?> /> <label for="ma50">50-day moving average</label><br />
<input type="checkbox" name="bollinger" id="bollinger" onclick="ChartSettingChange(this, false, true, true)" <?php if ($chartOptions['bollinger']) echo "checked='checked'" ?> /> <label for="bollinger">Bollinger bands</label><br />

</td></tr>

<tr valign="top"><td class="leftformcell">Lower Indicators</td><td>&nbsp;</td>
<td style="text-align:left">
<input type="checkbox" name="volume" id="volume" onclick="ChartSettingChange(this, false, true, true)" <?php if ($chartOptions['volume']) echo "checked='checked'" ?> /> <label for="volume">Volume</label><br />
<!--
<input type="checkbox" name="fast" id="fast" onclick="Revert(this, false)" /> <label for="fast">Fast Stochastic</label><br />
<input type="checkbox" name="slow" id="slow" onclick="Revert(this, false)" /> <label for="slow">Slow Stochastic</label><br />
-->
</td></tr>  

<tr valign="top"><td class="leftformcell">Compare To Index</td><td>&nbsp;</td>
<td style="text-align:left">
<input type="checkbox" name="spy" id="spy" onclick="ChartSettingChange(this, false, true, true)" <?php if ($chartOptions['spy']) echo "checked='checked'" ?> /> <label for="spy">S&amp;P 500</label><br />
<input type="checkbox" name="dia" id="dia" onclick="ChartSettingChange(this, false, true, true)" <?php if ($chartOptions['dia']) echo "checked='checked'" ?> /> <label for="dia">Dow Jones Industrial Average</label><br />
<input type="checkbox" name="qqq" id="qqq" onclick="ChartSettingChange(this, false, true, true)" <?php if ($chartOptions['qqq']) echo "checked='checked'" ?> /> <label for="qqq">NASDAQ 100</label><br />

</td></tr>  

<tr valign="top"><td class="leftformcell">Signal Restrictions</td><td>&nbsp;</td>
<td style="text-align:left">
<input type="checkbox" name="overbought" id="overbought" onclick="ChartSettingChange(this, false, true, false);Toggle(this, ['oversold']);" <?php if ($chartOptions['overbought']) echo "checked='checked'" ?> /> <label for="overbought">Over-bought (stochastic > 80)</label><br />
<input type="checkbox" name="oversold" id="oversold" onclick="ChartSettingChange(this, false, true, false);Toggle(this, ['overbought']);" <?php if ($chartOptions['oversold']) echo "checked='checked'" ?> /> <label for="oversold">Over-sold (stochastic < 20)</label><br />
<input type="checkbox" name="highvol" id="highvol" onclick="ChartSettingChange(this, false, true, false)" <?php if ($chartOptions['highvol']) echo "checked='checked'" ?> /> <label for="highvol">High recent volume</label><br />

<!--
<input type="checkbox" name="sp100" id="sp100" onclick="ChartSettingChange(this, true, true, false)" <?php if ($chartOptions['sp100']) echo "checked='checked'" ?> /> <label for="sp100">S&amp;P 100 stocks only</label><br />
<input type="checkbox" name="sp500" id="sp500" onclick="ChartSettingChange(this, false, true, false)" <?php if ($chartOptions['sp500']) echo "checked='checked'" ?> /> <label for="sp500">S&amp;P 500 stocks only</label><br />
-->
<!--
<input type="checkbox" name="yearhigh" id="yearhigh" onclick="Revert(this, false)" /> <label for="yearhigh">52-week high</label><br />
<input type="checkbox" name="yearlow" id="yearlow" onclick="Revert(this, false)" /> <label for="yearlow">52-week low</label><br />
-->
</td></tr> 

</table>

</div>

<div style="font-size:8pt;margin:0px auto; width:245;text-align:center">&copy; 2012 FakeMoneyRealStocks.com. </div>
</form>