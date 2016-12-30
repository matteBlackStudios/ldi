<?php

error_reporting(E_ALL);

include(dirname(__FILE__) . '/include/db.php');
include(dirname(__FILE__) . '/include/config.php');
//include(dirname(__FILE__) . '/include/states.php');

$et = new Config();
$config = $et->connect();
$db = new MysqliDb ($config[0],$config[1],$config[2],$config[3]);
$postingDelete = [];
$locationDelete = [];

$xml = simplexml_load_file('https://libertydiversified-openhire.silkroad.com/api/index.cfm?fuseaction=app.getJobListings&FORMAT=xml&JOBPLACEMENT=external&KEYWORD=&LANGUAGE=en&VERSION=1', 'SimpleXMLElement', LIBXML_NOCDATA);



foreach ($xml->job as $row):

	$coord = getCoor((string)$row->jobLocation->region, (string)$row->jobLocation->municipalit);

    $posting = [
       'ReqGuid'   		  => (int)$row->jobId,
       'JobLink'		  => (string)$row->applyUrl,
       'JobTitle'         => (string)$row->title,
       'JobDescription'   => (string)$row->jobDescription,
       'ReqSkills'  	  => (string)$row->requiredSkills,
       'PostingDate'  	  => (string)$row->postingDate[0],
       'JobLocationState' => (string)$row->jobLocation->region,
       'JobLocationCity'  => (string)$row->jobLocation->municipality,
       'JobCategory'	  => (string)$row->category,
       'JobLocationLat'		  => $coord['lat'],
       'JobLocationLng'		  => $coord['lng']
    ]; 
	print_r($posting);

   if(!empty($posting['ReqGuid'])){
       $postingDelete[] =$posting['ReqGuid'];
   }

   $db->where("ReqGuid", $posting['ReqGuid']);
   $postings = $db->getOne("postings");

   if (empty($postings)){
       $posting['created_at'] = date('Y-m-d h:i:s');
       $id = $db->insert('postings', $posting);
   } else {
       $posting['updated_at'] = date('Y-m-d h:i:s');
       $posting['deleted_at'] = '0000-00-00 00:00:00';
       $id = $db->where('ReqGuid',$posting['ReqGuid'])->update('postings', $posting);
   }
   

endforeach;


// Delete Location
$delete = Array(date('Y-m-d h:i:s'));
// Delete Posting
//print_r($postingDelete);
$delete_postings = $db->rawQuery('UPDATE postings SET deleted_at = ? WHERE ReqGuid NOT IN ("'.implode('","',$postingDelete).'")', $delete);

echo 'Saved!';



function getCoor($state, $city){
	// Get Office LAT/LONG via Google Maps API:
	$url = "https://maps.googleapis.com/maps/api/geocode/json?address=". urlencode($city) .",". urlencode($state)  ."&sensor=false&key=AIzaSyCCEt7_tp76BNTecG4E4JMxmvNeMFtTT84";
    $result_string = file_get_contents($url);
    if(isset($result_string)){
       $result = json_decode($result_string, true);
       if(isset($result['results'][0])){
           $result1[]=$result['results'][0];
           $result2[]=$result1[0]['geometry'];
           $result3[]=$result2[0]['location'];
           $coord = $result3[0];
           return $coord;
       }
    }

}
function getZip($lat, $lng){
	// Get Office LAT/LONG via Google Maps API:
	$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=". $lat .",". $lng ."&sensor=false&key=AIzaSyBgyiq1oh9vozCWmi1no8kCwjTuqFvIVxo";
    $result_string = file_get_contents($url);
    if(isset($result_string)){
       $result = json_decode($result_string, true);
       if(isset($result['results'][0])){
           $result1[]=$result['results'][0];
           $result2[]=$result1[0]['address_components'];
           $zip = $result2[0];
           foreach($zip as $t) {
				foreach ($t as $l) {
					//print_r($l);
					if ($l[0] == "postal_code") {
						return $t["short_name"];
					}
				}
			}
           return $zip;
       }
    }
}