<?
class DatabaseAccess
{
	public static $link;
	static function ConnectDB()
    {
        DatabaseAccess::$link = new PDO('mysql:host=localhost;dbname=fmrs;charset=utf8mb4',
		    MYSQL_USER,
		    MYSQL_PASSWORD,
		    array(
		        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		        PDO::ATTR_PERSISTENT => false
		    )
		);
    }
    
    static function CloseDB()
    {
    	DatabaseAccess::$link = null;
    }
    
    static function __Execute($sql, $params = array() )
    {
    	$handle = DatabaseAccess::$link->prepare($sql);
        $handle->execute($params);
        return $handle;
    }
    
    static function Insert($sql, $params = array() )
    {
    	DatabaseAccess::__Execute($sql, $params);
        $new_id = DatabaseAccess::$link->lastInsertId();
        return $new_id;
    }
    
    static function Update($sql, $params = array() )
    {
    	DatabaseAccess::__Execute($sql, $params);
    }
    
    static function Select($sql, $params = array() )
    {
        $handle = DatabaseAccess::__Execute($sql, $params);
		$results = $handle->fetchAll(\PDO::FETCH_OBJ);
		return $results;
    }
}
DatabaseAccess::ConnectDB();