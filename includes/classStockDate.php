<?
class StockDate
{
	var $dateID = 0;
    var $date = "";
    
    function __construct($dateID = false)
    {
    	if ($dateID) $this->PopulateFromDatabase($dateID);
    }
    
    function PopulateFromDataRow($row)
    {
    	$this->dateID 		= $row->DateID;
        $this->date  	    = date("Y-m-d", strtotime($row->StockDate));
    }
    
    function PopulateFromDatabase($dateID)
    {
    	$results = DatabaseAccess::Select("select * from Date where DateID=:date_id", array(':date_id' => $dateID) );
    	if (count($results) == 0) return false;
        $this->PopulateFromDataRow($results[0]);
    }
    
}
?>
