<?
require_once("../includes/commonfuncs.php");
$account = GetCurrentAccountValue($page->signedMember);
$trade = GetSessionTrade();

$boss = isset($_GET['boss']) && $_GET['boss'];
$advanced = isset($_GET['advanced']) && $_GET['advanced'];
$chartOptions = $page->signedMember ? $page->signedMember->chartOptions : Trade::DefaultChartOptions();
$chartOptions['isAsync'] = true;
foreach ($chartOptions as $key=>$val)
{
    if (($boss || $advanced) && $key == "tradeLength") continue;
    if (isset($_GET[$key])) $chartOptions[$key] = $_GET[$key];
    else $chartOptions[$key] = "";
}

if ($page->signedMember && $page->signedMember->memberLevel) $page->signedMember->UpdateDBValue("ChartOptions", json_encode($chartOptions));
if ($trade && $account)
{
    $trade->chartOptions = $chartOptions;
    $trade->chartLength = $chartOptions['chartLength'];
    if ($advanced)
    {
        $trade->BuildTradeData();
        echo "WV1A.ChangeChart('" . $trade->EndChartSrc(false) . "', " . $trade->ChartHeight() .  ");\n";
    }
	else echo "WV1.ChangeChart('" . $trade->StartChartSrc() . "', " . $trade->ChartHeight() . ");\n";
}
else
{
	echo "window.location=document.location\n";
}
session_write_close();

?>