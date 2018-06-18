<?php
/*
usage :
candlesticks.php?pair=btc_usd&range=60&from_timestamp=1529316000&to_timestamp=1529319600
*/

include_once('config.php');
include_once('db.php');

$requestValues = array();
if(isset($_GET['pair']) && in_array($_GET['pair'], PAIRS_LIST) && isset($_GET['range'])){
    $request = "SELECT * FROM ".DB_TABLE_NAME." ";
    $request .= "WHERE pair = ?";
    $requestValues[] = $_GET['pair'];

    if(isset($_GET['from_timestamp']))
    {
        $request .= "AND timestamp >= ?";
        $requestValues[] = $_GET['from_timestamp'];

        if(isset($_GET['to_timestamp'])){
            $request .= "AND timestamp <= ?";
            $requestValues[] = $_GET['to_timestamp'];
        }

        $db = Database::$dbh->prepare($request);
        $db->execute($requestValues);
    }
    else
    {
        $request .= "ORDER BY id_local DESC
                    LIMIT 200";
        $db = Database::$dbh->prepare($request);
        $db->execute($requestValues);
    }

    $transactions = $db->fetchAll();

    $candlesticks = array();
    $current_candlestick = array("timestamp" => (int) $_GET['from_timestamp'], "opening" => null, "closing" => null, "low" => null, "high" => null);
    foreach ($transactions as $id_transaction => $transaction)
    {
        //echo $current_candlestick['timestamp']."<br />";
        //if the current transaction timestamp exceed the limit of the current candlestick
        if($transaction['timestamp'] >= $current_candlestick['timestamp'] + $_GET['range'])
        {
            //then we close the candlestick and start the next one
            $current_candlestick["closing"] = $transactions[$id_transaction-1]['price'];
            array_push($candlesticks, $current_candlestick);
            $current_candlestick = array("timestamp" => $current_candlestick['timestamp'] + $_GET['range'], "opening" => null, "closing" => null, "low" => null, "high" => null);
        }
        //if the candlestick is empty (new creation)
        if($current_candlestick['opening'] == null){
            if($transaction['timestamp'] >= $current_candlestick['timestamp'] + $_GET['range'])
            {
                //echo "test";
                $current_candlestick["closing"] = $transactions[$id_transaction-1]['price'];
                $current_candlestick["opening"] = $transactions[$id_transaction-1]['price'];
                $current_candlestick["low"] = $transactions[$id_transaction-1]['price'];
                $current_candlestick["high"] = $transactions[$id_transaction-1]['price'];

                array_push($candlesticks, $current_candlestick);
                $current_candlestick = array("timestamp" => $current_candlestick['timestamp'] + $_GET['range'], "opening" => null, "closing" => null, "low" => null, "high" => null);
            }
            else
            {
                $current_candlestick['opening'] = $transaction['price'];
                $current_candlestick['low'] = $transaction['price'];
                $current_candlestick['high'] = $transaction['price'];
            }
        }
        //if we are currently filling the candlestick
        else {
            if($transaction['price'] > $current_candlestick['high'])
                $current_candlestick['high'] = $transaction['price'];
            if($transaction['price'] < $current_candlestick['low'])
                $current_candlestick['low'] = $transaction['price'];
        }
    }
    //echo count($candlesticks);
    echo json_encode($candlesticks);
}
else {
    echo json_encode(array("error"));
}
