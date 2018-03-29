<?
class Stock
{
	var $stockID = 0;
    var $ticker = "";
    var $company;
    var $exchange;
    var $randomDateID;
    var $industry;
    var $startDateID;
    var $endDateID;
    
    function __construct($stockID = false)
    {
    	if ($stockID) $this->PopulateFromDatabase($stockID);
    }
    
    function PopulateFromDataRow($row)
    {
    	$this->stockID 		= $row->ID;
        $this->ticker  	    = $row->Stock;
        $this->company	    = $row->Company;
        $this->exchange	    = $row->Exchange;
        $this->randomDateID	    = $row->RandomDateID;
        $this->industry	    = $row->YahooIndustry;
        
        $this->startDateID 		= $row->StartDateID;
        $this->endDateID 		= $row->EndDateID;
    }
    
    function PopulateFromDatabase($stockID)
    {
    	$results = DatabaseAccess::Select("select * from Stock where ID=:id", array(':id' => $stockID) );
    	if (count($results) == 0) return false;
        $this->PopulateFromDataRow($results[0]);
    }
    
    function PopulateFromTicker($ticker)
    {
    	$results = DatabaseAccess::Select("select * from Stock where Stock=:ticker", array(':ticker' => $ticker) );
    	if (count($results) == 0) return false;
        $this->PopulateFromDataRow($results[0]);
    }
    
    function CompanyToPrint()
    {
		$company = $this->company;
		if (strtoupper($company) == $company && strlen($company) > 15)
		{
			$company = ucwords(strtolower($company));
		}
    	return $company;
    }
}
?>
