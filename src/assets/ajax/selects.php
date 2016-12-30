<?php
include('include/db.php');
include('include/config.php');

$et = new Config();
$config = $et->connect();
$db = new MysqliDb ($config[0],$config[1],$config[2],$config[3]);

$get = $_GET;

$locations = $db->where('JobLocationState',NULL,' IS NOT')->orderBy('JobLocationState', 'asc')->groupBy('JobLocationState')->get('postings');
$category = $db->where('JobCategory',NULL,' IS NOT')->orderBy('JobCategory', 'asc')->groupBy('JobCategory ')->get('postings');

$result = array();
$result['locations'] = '<option value="">LOCATION: SHOW ALL</option>';
$result['category'] = '<option value="">JOB: SHOW ALL</option>';

foreach($locations as $row){
    if(!empty($row['JobLocationState'])){
        $result['locations'] .= '<option value="' . $row['JobLocationState'] . '" ' . (isset($get['location']) && $get['location'] == $row['JobLocationState'] ? 'selected="selected"' : '') . '>LOCATION: ' . strtoupper($row['JobLocationState']) . '</option>';
    }
}

foreach($category as $row){
    if(!empty($row['JobCategory'])){
        $result['category'] .= '<option value="' . $row['JobCategory'] . '" ' . (isset($get['category']) && $get['category'] == $row['JobCategory'] ? 'selected="selected"' : '') . '>JOB: ' . strtoupper($row['JobCategory']) . '</option>';
    }
}

echo json_encode($result);