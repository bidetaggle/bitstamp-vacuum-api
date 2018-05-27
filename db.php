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
                "mysql:host=localhost;dbname=".DB_NAME,
                DB_USER,
                DB_PASSWORD
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
?>
