<?php
include_once('config.php');
include_once('db.php');

$requestValues = array();
$request = "SELECT * FROM ".DB_TABLE_NAME." ";
if(isset($_GET['pair']) && in_array($_GET['pair'], PAIRS_LIST)){
    $request .= "WHERE pair = ?";
    $requestValues[] = $_GET['pair'];
}
else $request .= "WHERE 1 ";

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

//remove numeral ids
$return = [];
$i = 0;
foreach ($db->fetchAll() as $transaction) {
    foreach ($transaction as $key => $value) {
        if(!is_int($key))
        $return[$i][$key] = $value;
    }
    $i++;
}

echo json_encode($return);
// echo json_encode($db->fetchAll());
