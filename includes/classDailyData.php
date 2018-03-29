<?
class DailyData
{
    var $ID = 0;
	var $stockID = 0;
	var $dateID = 0;
    var $open;
    var $high;
    var $low;
    var $close;
    var $volume;
    var $nextROC10;
    
    function __construct($dateID = false, $stockID = false)
    {
    	if ($dateID && $stockID) $this->PopulateFromDatabase($dateID, $stockID);
    }
    
    function PopulateFromDataRow($row)
    {
        $this->ID = $row->ID;
    	$this->dateID = $row->DateID;
        $this->stockID = $row->StockID;
        $this->open = $row->Open;
        $this->high = $row->High;
        $this->low = $row->Low;
        $this->close = $row->Close;
        $this->volume = $row->Volume;
        $this->nextROC10 = $row->NextROC10;
    }
    
    function PopulateFromDatabase($dateID, $stockID)
    {
    	$results = DatabaseAccess::Select("select * from DailyData where DateID=:date_id AND StockID=:stock_id", array(':date_id' => $dateID, ':stock_id' => $stockID) );
    	if (count($results) == 0) return false;
        $this->PopulateFromDataRow($results[0]);
    }
}
?>
