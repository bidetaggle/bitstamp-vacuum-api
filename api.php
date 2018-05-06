<?php

class Database
{
    /**
     * @var PDO
     */
    public static $dbh;

    public static function connect()
    {
        try{
            self::$dbh = new PDO(
                "mysql:host=localhost;dbname=bitstamp",
                "root",
                "root"
            );

            self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$dbh->exec("SET NAMES 'UTF8'");
        }

        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }
}

Database::connect();

if(isset($_GET['from_timestamp']) && isset($_GET['to_timestamp']))
{
    $request = "SELECT * FROM btc_usd 
                WHERE timestamp >= ? 
                AND timestamp <= ?";
    $db = Database::$dbh->prepare($request);
    $db->execute(array($_GET['from_timestamp'], $_GET['to_timestamp']));
}
else
{
    $request = "SELECT * FROM btc_usd LIMIT 100";
    $db = Database::$dbh->prepare($request);
    $db->execute();
}

echo json_encode($db->fetchAll());