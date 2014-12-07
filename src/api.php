<?php
///////////////////////////
// VelibStationsLocator //
/////////////////////////

//DB
$dns = 'mysql:host=XXX;dbname=XXX';
$user = 'XXX';
$pass = 'XXX';

try{
    $db = new PDO( $dns, $user, $pass );
} catch ( Exception $e ) {
    echo json_encode(array("error" => $e->getMessage()));
    die();
}
$db->exec("SET CHARACTER SET utf8");

//Parameters
$maxDistance = (isset($_GET['distance']) && $_GET['distance']!= "") ? $_GET['distance'] : 1;//in km
$limit = (isset($_GET['limit']) && $_GET['limit']!= "") ? $_GET['limit'] : 1;

//MAIN
//Expects a position in GET
if(isset($_GET['position']) && $_GET['position']!= ""){
    //position must be sent in a lat,lng format
    $tabPos = explode(',',$_GET['position']);
    if(isset($tabPos[0]) && isset($tabPos[1])){
        $lat = $tabPos[0];
        $lng = $tabPos[1];

        //Query using Haversine formula to calculate the distance
        //Todo: use the geo functions of Mysql5.6 to achieve the same
        $query = "SELECT id, name, address,( 6371 * acos( cos( radians({$lat}) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( latitude ) ) ) ) AS distance FROM stations HAVING distance < {$maxDistance} ORDER BY distance LIMIT 0 , {$limit}";

        $select = $db->query($query);
        $tabRes = $select->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($tabRes);
        die();
    }

}

echo json_encode(array("error" => "Position parameter not valid"));
