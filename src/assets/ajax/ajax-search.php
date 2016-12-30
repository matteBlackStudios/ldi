<?php
include('include/db.php');
include('include/config.php');
include('include/states.php');
$et = new Config();
$config = $et->connect();
$db = new MysqliDb ($config[0],$config[1],$config[2],$config[3]);

$get = $_GET;
//$db->setTrace (true);

/*
if(!empty($get['zip'])){
    $zipcodes = getZctasInRange($db, $get['zip'], '');
}
*/

$offset = 0;
$result = [];
$get_states = [];
$get_job = [];
$page = 1;

if(!empty($get['spage'])){
    $page = $get['spage'];
    $next = $get['spage']-1;
    $offset = $next*10;
}
$cols = Array ("p.JobTitle", "p.JobDescription", "p.PostingDate", "p.JobLink", "p.ReqGuid", "p.JobLocationState", "p.JobLocationCity", "p.jobCategory");

$db->where('p.deleted_at', '0000-00-00 00:00:00');

// Search
if(!empty($get['keywords'])){
    $db->where('(p.JobTitle LIKE "%' . $get['keywords'] . '%" OR p.JobDescription LIKE "%'  . $get['keywords'] . '%" OR p.JobLocationState LIKE "%' . $get['keywords'] . '%" OR p.JobLocationCity LIKE "%' . $get['keywords'] . '%" OR p.jobCategory LIKE "%' . $get['keywords'] . '%" OR p.ReqGuid LIKE "%' . $get['keywords'] . '%")');
}

// Job Category
if(!empty($get['category'])){
    $db->where('p.jobCategory', $get['category']);
}

// Job Location
if(!empty($get['location'])){
    $db->where('p.JobLocationState', $get['location']);
}

/*
if(!empty($get['zip'])){

    $zip_array = [];
    foreach($zipcodes as $zip){
        $zip_array['zips'][] = $zip['zip'];
    }
    $db->where('(p.JobZip IN (' . implode(',', $zip_array['zips']) . '))');

}*/

$count_query = $db->copy();
$count = $count_query->getValue ("postings p", "count(p.ReqGuid)");

$pages = ceil($count/20);
$pages_round = round($count/20);

$postings = $db->arraybuilder()->paginate('postings p', $page, $cols);
//print_r($postings);
//print_r( $db->setTrace(true) );

if($count != 0){
    $result['postings'] =  '<section id="postings">
								<table style="width: 100%;">
								<thead>
									<tr>
								        <th class="id wow fadeIn">JOB ID</th>
								        <th class="title wow fadeIn">JOB TITLE</th>
								        <th class="location wow fadeIn">LOCATION</th>
										<th class="posted wow fadeIn">POSTED DATE</th>
								    </tr>
								</thead>
								<tbody>';
    foreach ($postings as $row)
    {
        $result['postings'] .= '<tr onclick="window.document.location=\'posting.php?ReqGuid='. $row['ReqGuid'] .'\'">
								    <td class="id wow fadeIn">'. $row['ReqGuid'] .'</td>
								    <td class="title wow fadeIn">'. $row['JobTitle'] .'</td>
								    <td class="location wow fadeIn">'. $row['JobLocationCity'] .', '. $row['JobLocationState'] .'</td>
								    <td class="posted wow fadeIn">'. date('F d, Y', strtotime($row['PostingDate'])) .'</td>
								</tr>';
    }
    $result['postings'] .= '</tbody>
                            </table>';
} else {
    $result['postings'] = '<div class="vac loader"><div class="va"><div class="text-center"><h4>No Listings Found. Please Try Again.</h4></div></div></div>';
}

$result['page_jump'] = '';
$result['page_jump'] .= '<p class="results">Results <select name="page_jump" class="page_jump">';
$num = 0;
for($i = 1; $i < ($pages + 1); ++$i){
    if($i == 1)	{
        $result['page_jump'] .= '<option value="1">1 &ndash; '.($count < 20 ? $count : '20').'</option>';
    } elseif($i == $pages) {
        $result['page_jump'] .= '<option value="'.$i.'" '.($i == $page ? 'selected="selected"' : '').'>' . ($num + 1) . ' &ndash; ' . $count. '</option>';
    } else {
        $result['page_jump'] .= '<option value="'.$i.'" '.($i == $page ? 'selected="selected"' : '').'>' . ($num + 1) . ' &ndash; ' . ($num + 20 ). '</option>';
    }
    $num = $num + 20;
}
$result['page_jump'] .= '</select> of ' . $count . '</p>';

// Pagination
$result['pagination'] = '';
$result['pagination']   .= '<ul class="pagination" role="navigation" aria-label="Pagination">';
if($page == 1){
    $result['pagination']  .= '<li class="pagination-previous disabled"><span class="show-for-sr"></span></li>';
} else {
    $result['pagination']  .= '<li class="pagination-previous"><a data-href="'.($page-1).'" aria-label="Previous page"></a></li>';
}
//for($i = 1; $i < ($pages + 1); ++$i){
for ($i=max($page-2, 1); $i<=max(1, min($pages,$page+2)); $i++){
    $result['pagination']   .= '<li '.($i == $page ? 'class="current"' : '').'>'.($i != $page ? '<a data-href="'.$i.'" aria-label="Page '.$i.'">'.$i.'</a>' : $i ).'</li>';
}
if($pages == $page){
    $result['pagination']  .= '<li class="pagination-next disabled"><span class="show-for-sr"></span></li>';
} else {
    $result['pagination']  .= '<li class="pagination-next"><a data-href="' . ($page + 1) . '" aria-label="Next page"><span class=""></span></a></li>';
}
$result['pagination']  .= '</ul>';

if(isset($get['ajax'])){
    echo json_encode($result);
}

// Functions
function calc_distance($point1, $point2)
{
    $radius      = 3958;      // Earth's radius (miles)
    $deg_per_rad = 57.29578;  // Number of degrees/radian (for conversion)

    $distance = ($radius * pi() * sqrt(
            ($point1['lat'] - $point2['lat'])
            * ($point1['lat'] - $point2['lat'])
            + cos($point1['lat'] / $deg_per_rad)  // Convert these to
            * cos($point2['lat'] / $deg_per_rad)  // radians for cos()
            * ($point1['long'] - $point2['long'])
            * ($point1['long'] - $point2['long'])
        ) / 180);

    return $distance;  // Returned using the units used for $radius.
}

function getZctasInRange($db, $zipcode, $within){

    $db->where('zip', $zipcode);
    $zcta = $db->get('zctas');
    $range_from = 0;
    $range_to = 50; //(!empty($within) ? $within : 10);
    $tableName = "zctas";

    $sql = "SELECT 3956 * 2 * ATAN2(SQRT(POW(SIN((RADIANS({$zcta[0]['lat']}) - "
        .'RADIANS(z.lat)) / 2), 2) + COS(RADIANS(z.lat)) * '
        ."COS(RADIANS({$zcta[0]['lat']})) * POW(SIN((RADIANS({$zcta[0]['lng']}) - "
        ."RADIANS(z.lng)) / 2), 2)), SQRT(1 - POW(SIN((RADIANS({$zcta[0]['lat']}) - "
        ."RADIANS(z.lat)) / 2), 2) + COS(RADIANS(z.lat)) * "
        ."COS(RADIANS({$zcta[0]['lat']})) * POW(SIN((RADIANS({$zcta[0]['lng']}) - "
        ."RADIANS(z.lng)) / 2), 2))) AS \"miles\", z.* FROM {$tableName} z "
        ."WHERE lat BETWEEN ROUND({$zcta[0]['lat']} - (25 / 69.172), 4) "
        ."AND ROUND({$zcta[0]['lat']} + (25 / 69.172), 4) "
        ."AND lng BETWEEN ROUND({$zcta[0]['lng']} - ABS(25 / COS({$zcta[0]['lat']}) * 69.172)) "
        ."AND ROUND({$zcta[0]['lng']} + ABS(25 / COS({$zcta[0]['lat']}) * 69.172)) "
        ."AND 3956 * 2 * ATAN2(SQRT(POW(SIN((RADIANS({$zcta[0]['lat']}) - "
        ."RADIANS(z.lat)) / 2), 2) + COS(RADIANS(z.lat)) * "
        ."COS(RADIANS({$zcta[0]['lat']})) * POW(SIN((RADIANS({$zcta[0]['lng']}) - "
        ."RADIANS(z.lng)) / 2), 2)), SQRT(1 - POW(SIN((RADIANS({$zcta[0]['lat']}) - "
        ."RADIANS(z.lat)) / 2), 2) + COS(RADIANS(z.lat)) * "
        ."COS(RADIANS({$zcta[0]['lat']})) * POW(SIN((RADIANS({$zcta[0]['lng']}) - "
        ."RADIANS(z.lng)) / 2), 2))) <= $range_to "
        ."AND 3956 * 2 * ATAN2(SQRT(POW(SIN((RADIANS({$zcta[0]['lat']}) - "
        ."RADIANS(z.lat)) / 2), 2) + COS(RADIANS(z.lat)) * "
        ."COS(RADIANS({$zcta[0]['lat']})) * POW(SIN((RADIANS({$zcta[0]['lng']}) - "
        ."RADIANS(z.lng)) / 2), 2)), SQRT(1 - POW(SIN((RADIANS({$zcta[0]['lat']}) - "
        ."RADIANS(z.lat)) / 2), 2) + COS(RADIANS(z.lat)) * "
        ."COS(RADIANS({$zcta[0]['lat']})) * POW(SIN((RADIANS({$zcta[0]['lng']}) - "
        ."RADIANS(z.lng)) / 2), 2))) >= $range_from "
        ."ORDER BY 1 ASC";

    return $db->rawQuery($sql);
}