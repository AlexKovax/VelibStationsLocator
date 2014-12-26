<?php
///////////////////////////
// VelibStationsLocator //
/////////////////////////

//DB configuration
//conf.php must contain the following variables
//$dns = 'mysql:host=XXX;dbname=XXX';
//$user = 'XXX';
//$pass = 'XXX';
require_once('conf.php');

try{
    $db = new PDO( $dns, $user, $pass );
} catch ( Exception $e ) {
    echo json_encode(array("error" => $e->getMessage()));
    die();
}
$db->exec("SET CHARACTER SET utf8");

//Parameters
$maxDistance = (isset($_GET['distance']) && $_GET['distance']!= "") ? $_GET['distance'] : 1;//in km
$limit = (isset($_GET['limit']) && $_GET['limit']!= "") ? $_GET['limit'] : 1;//It isn't really recommended to return more than 1 as the JCDecaux APi is quite slow

//MAIN
//Expects a position in GET
if(isset($_GET['position']) && $_GET['position']!= ""){
    //position must be sent in a lat,lng format
    $tabPos = explode(',',$_GET['position']);
    $tabReturn = array();
    if(isset($tabPos[0]) && isset($tabPos[1])){
        $lat = $tabPos[0];
        $lng = $tabPos[1];

        //Query using Haversine formula to calculate the distance
        //Todo: use the geo functions of Mysql5.6 to achieve the same
        $query = "SELECT id, ( 6371 * acos( cos( radians({$lat}) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( latitude ) ) ) ) AS distance FROM stations HAVING distance < {$maxDistance} ORDER BY distance LIMIT 0 , {$limit}";

        $select = $db->query($query);
        $tabRes = $select->fetchAll(PDO::FETCH_ASSOC);

        //Then for each station we get the availability infos
        //We use the API key info specified in conf.php in $jcdApiKey
        $contract = "Paris";
        foreach($tabRes as $station){
          $id = $station["id"];
          $query = "https://api.jcdecaux.com/vls/v1/stations/{$id}?contract={$contract}&apiKey={$jcdApiKey}";
          $data = json_decode(file_get_contents($query),true);//Warning: JCDecaux API can be slow to answer
          $tabReturn[] = array_merge($station,$data);
        }

        echo json_encode($tabReturn);
        die();
    }

}

echo json_encode(array("error" => "Position parameter not valid"));
