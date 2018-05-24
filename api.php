<?php

include_once('db.php');

if(isset($_GET['from_timestamp']))
{
    if(isset($_GET['to_timestamp'])){
        $request = "SELECT * FROM btc_usd 
                    WHERE timestamp >= ? 
                    AND timestamp <= ?";
        $db = Database::$dbh->prepare($request);
        $db->execute(array($_GET['from_timestamp'], $_GET['to_timestamp']));
    }
    else{
        $request = "SELECT * FROM btc_usd 
                    WHERE timestamp >= ?";
        $db = Database::$dbh->prepare($request);
        $db->execute(array($_GET['from_timestamp']));
    }
}
else
{
    $request = "SELECT * FROM btc_usd LIMIT 100";
    $db = Database::$dbh->prepare($request);
    $db->execute();
}

echo json_encode($db->fetchAll());