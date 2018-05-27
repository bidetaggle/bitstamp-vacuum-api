<?php

include_once('db.php');

define("DB_TABLE_NAME", "transactions");
define("PAIRS_LIST", array("btc_usd", "eth_usd", "btc_eur"));

if(isset($_GET['pair']) && in_array($_GET['pair'], PAIRS_LIST)){
    if(isset($_GET['from_timestamp']))
    {
        if(isset($_GET['to_timestamp'])){
            $request = "SELECT * FROM ".DB_TABLE_NAME."
                        WHERE pair = ?
                        AND timestamp >= ?
                        AND timestamp <= ?";
            $db = Database::$dbh->prepare($request);
            $db->execute(array($_GET['pair'], $_GET['from_timestamp'], $_GET['to_timestamp']));
        }
        else{
            $request = "SELECT * FROM ".DB_TABLE_NAME."
                        WHERE pair = ?
                        AND timestamp >= ?";
            $db = Database::$dbh->prepare($request);
            $db->execute(array($_GET['pair'], $_GET['from_timestamp']));
        }
    }
    else
    {
        $request = "SELECT * FROM ".DB_TABLE_NAME."
                    WHERE pair = ?
                    ORDER BY id_local DESC
                    LIMIT 200";
        $db = Database::$dbh->prepare($request);
        $db->execute(array($_GET['pair']));
    }

    echo json_encode($db->fetchAll());
}
else
    echo json_encode(array('error' => 'pair parameter missing or the provided pair is not valid'));
