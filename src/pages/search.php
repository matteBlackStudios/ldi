<style>
	.search-cta {
		display: none!important;
	}
</style>
<div class="mask wow fadeIn search">
</div>
<section id="marq-map" class="wow fadeIn">
	<div id="map"></div>
	
</section> 

	<div class="search-filter">
		<div class="row collapse expanded">
			<div class="small-6 medium-3 columns">
				  <select class="dropdown wow fadeIn" id="location" name="location">
				    <option value="">LOCATION: <span>SHOW ALL</span></option>
				  </select>
			</div>
			<div class="small-6 medium-3 columns wow fadeIn">
				  <select class="dropdown" id="category" name="category">
				    <option value="">JOB: <span>SHOW ALL</span></option>
				  </select>
			</div>
			<div class="small-6 medium-3 columns">
				  <input type="text" placeholder="KEYWORDS" class="keywords wow fadeIn"  name="keywords">
			</div>
			<div class="small-6 medium-3 columns">
				 <a class="button wow fadeIn submit-search">SEARCH</a>
			</div>
		</div>
	</div>
<div class="search-title">
	<div class="row">
		<div class="small-12 medium-9 large-10 columns">
			<h4 class="wow fadeIn">Most Popular Jobs in <span id="current-location">United States</span></h4>
			<!--<p class="wow fadeIn">(showing 5 of 37 jobs)</p>-->
		</div>
		<div class="small-12 medium-3 large-2 columns">
			<!--<a class="button share wow fadeIn">SHARE/REFER <i class="fa fa-share-alt" aria-hidden="true"></i></a>-->
		</div>
	</div>
</div>

<section id="search-table" style="min-height: 300px;">
	<div id="postings" style="display: block; padding: 0;">
    </div>
</section>
    
<div class="row pagination-container text-center">
    <div class="small-12 columns">
    	<div class="holder--pagination">
		</div>
    </div>
</div>
 
			
<div class="cta search">
	<div class="row align-middle align-center">
		<div class="small-12 medium-6 columns" id="left">
			<div class="icon">
				<i class="fa fa-users wow fadeIn" aria-hidden="true"></i>
			</div>
			<h4 class="wow fadeIn">SEARCH JOBS BY FUNCTION</h4>
			<div class="row small-up-1 medium-up-2">
				<?php
					include('assets/ajax/include/db.php');
					include('assets/ajax/include/config.php');
					
					$et = new Config();
					$config = $et->connect();
					$db = new MysqliDb ($config[0],$config[1],$config[2],$config[3]);
					
					$get = $_GET;
					
					$category = $db->where('JobCategory',NULL,' IS NOT')->orderBy('JobCategory', 'asc')->groupBy('JobCategory ')->get('postings');
					$locations = $db->where('JobLocationCity',NULL,' IS NOT')->where('deleted_at', '0000-00-00 00:00:00')->orderBy('JobLocationCity', 'asc')->groupBy('JobLocationCity')->get('postings');

					foreach($category as $row){
					    if(!empty($row['JobCategory'])){
						    echo '<div class="column wow fadeIn"><a href="search.php?category='. $row['JobCategory'] .'">'. $row['JobCategory'] .'</a></div>';
					    }
					}
					?>
			</div>
		</div>
		<!--
		<div class="small-12 medium-6 columns" id="right" style="background-image: none;">
			<div class="row align-center border" style="margin: 0;">
				<div class="small-10 medium-9 columns">
					<div class="icon">
						<i class="fa fa-users wow fadeIn" aria-hidden="true"></i>
					</div>
					<h4 class="wow fadeIn">JOIN OUR TALENT COMMUNITY</h4>
					<p class="wow fadeIn">Receive Alerts When New  Jobs are Posted</p>
					<div class="row align-center">
						<div class="small-11 medium-8 large-6 columns">
							<a class="button share wow fadeIn">JOIN NOW <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		-->
	</div>
</div>

<script>
function initMap() {
	
	  var styleArray = [{"featureType":"administrative","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"visibility":"on"},{"color":"#e3e3e3"}]},{"featureType":"landscape.natural","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"color":"#cccccc"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"transit.station.airport","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"transit.station.airport","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#FFFFFF"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"off"}]}];

	  // Create a map object and specify the DOM element for display.
	  var map = new google.maps.Map(document.getElementById('map'), {
	    center: {lat: -34.397, lng: 150.644},
	    scrollwheel: false,
	    zoom: 4,
		center: {lat: 40.3, lng: -98.5747698},
	    styles: styleArray,
	    streetViewControl: false,
	    mapTypeControl:false
	  });
		
		// needed to close when another is clicked:
		var infowindow = new google.maps.InfoWindow();
		
		<?php

		    foreach ($locations as $row) {
			    if (strlen($row['JobLocationLat']) > 2) { ?>
			    
					var latlngset_ = new google.maps.LatLng(<?php echo $row['JobLocationLat']; ?>, <?php echo $row['JobLocationLng']; ?>);
		
					var marker = new google.maps.Marker({
						map: map,
						title: name ,
						position: latlngset_,
						icon: 'http://jobs.libertydiversified.com/assets/img/global/marker.png'
					});
		
					var content_ = '<div class="content-window" data-id="California"><a><h3><?php echo $row['JobLocationCity']; ?></h3></a></div>';
		
					google.maps.event.addListener(marker,'click', (function(marker,content_,infowindow){
					return function() {
					       infowindow.close()
						    infowindow.setContent('<div class="content-window" data-id="California"><a><h3 style="font-weight: bold; font-size: 16px; color: #000000; margin-bottom: 0;"><?php echo $row['JobLocationState']; ?></h3></a></div>');
						    infowindow.open(map, marker);
					};
					})(marker,content_,infowindow));			    

			    <?
			    }
		    }
		?>



		var content_ = '<div class="content-window"><a href=""><h3>Washington, DC, DC</h3><h4>1 Opening</h4></a></div>';

		var infowindow_ = new google.maps.InfoWindow();

		google.maps.event.addListener(marker_,'click', (function(marker_,content_,infowindow_){
		return function() {
			 infowindow_.setContent(content_);
			 infowindow_.open(map,marker_);
		};
		})(marker_,content_,infowindow_));
	
}
</script>
			