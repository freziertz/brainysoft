<?php
class Database
{
	
	    
    private $host = "localhost";
    private $db_name = "datahouserpdb";
    private $username = "root";
    private $password = "";
    public 	$db_con;
     
    public function dbConnection()
	{
     
     $this->db_con = null;    
        try
			{
            $this->db_con = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
			$this->db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
			}
		catch(PDOException $exception)
			{
            echo "Connection error: " . $exception->getMessage();
			}	
         
        return $this->db_con;
    }
}
?>