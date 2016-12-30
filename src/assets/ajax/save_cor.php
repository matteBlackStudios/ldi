<?php

include(dirname(__FILE__) . '/include/db.php');
include(dirname(__FILE__) . '/include/config.php');
include(dirname(__FILE__) . '/include/states.php');

$et = new Config();
$config = $et->connect();
$db = new MysqliDb ($config[0],$config[1],$config[2],$config[3]);

$db->where('deleted_at', '0000-00-00 00:00:00');
$db->where('lat IS NULL');
$locations = $db->get('postings');
foreach($locations as $row){
    $cor = getCoor($row['address1'].' '.$row['city'].', '.$row['stateabbr'].' '.$row['postalcode']);
print_r($cor);
    $location['lat'] = $cor['lat'];
    $location['lng'] = $cor['lng'];
    $location['updated_at'] = date('Y-m-d h:i:s');
    $id = $db->where('id', $row['id'])->update('locations', $location);
}

function getCoor($address){
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false&key=AIzaSyAkI326Rru9eIklCNd-3csjH54C9-jdhjk";
    $result_string = file_get_contents($url);
    if(isset($result_string)){
        $result = json_decode($result_string, true);
print_r($result);
        if(isset($result['results'][0])){
            $result1[]=$result['results'][0];
            $result2[]=$result1[0]['geometry'];
            $result3[]=$result2[0]['location'];
            return $result3[0];
        }
    }
}

echo 'SUCCESS!';