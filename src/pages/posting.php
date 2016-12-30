<?php
    include('assets/ajax/include/db.php');
    include('assets/ajax/include/config.php');
    
    $et = new Config();
    $config = $et->connect();
    $db = new MysqliDb ($config[0],$config[1],$config[2],$config[3]);
    
    $get = $_GET;
    
    $post = $db->where('ReqGuid',$get['ReqGuid'])->getOne('postings');
?>
<div class="posting-mast wow fadeIn">
</div>

<div class="row expanded collapse quick">
	<div class="small-12 medium-6 columns">
		<a href="search.php" id="listings">
			<div class="row align-middle" style="margin: 0;">
				<div class="small-12 columns wow fadeIn">
					BACK TO ALL LISTINGS
				</div>
			</div>
		</a>
	</div>
	<div class="small-12 medium-6 columns">
		<a href="<?php echo $post['JobLink']; ?>" id="apply">
			<div class="row align-middle" style="margin: 0;">
				<div class="small-12 columns wow fadeIn">
					APPLY
				</div>
			</div>
		</a>
	</div>
</div>

<div class="row align-middle align-center overview">
	<div class="small-12 medium-9 columns wow fadeIn">
		<h1><?php echo $post['JobTitle']; ?></h1>
	</div>
	<div class="small-9 medium-3 columns">
		<a target="_blank" href="https://www.linkedin.com/cws/share?url=<?php echo $post['JobLink']; ?>" class="button share wow fadeIn">SHARE/REFER <i class="fa fa-share-alt" aria-hidden="true"></i></a>
	</div>
</div>

<div class="row hr">
	<div class="small-12 columns wow fadeIn">
		<hr>
	</div>
</div>

<div class="row align-center">
	<div class="small-11 medium-9 columns">
		<div class="wow fadeIn">
			<?php echo $post['JobDescription']; ?>
		</div>
	</div>
</div>

<div class="row align-left posting-section">
	<div class="small-10 small-offset-1 medium-4 large-3 columns">
		<h4 class="wow fadeIn">RESPONSIBILITIES & QUALIFICATIONS</h4>
	</div>
	<div class="small-10 small-offset-1 medium-8 large-7 columns">
		<div class="wow fadeIn">
			<?php echo $post['ReqSkills']; ?>
		</div>
	</div>
</div>

<!--
<div class="row align-left posting-section">
	<div class="small-10 small-offset-1 medium-4 large-3 columns">
		<h4 class="wow fadeIn">QUALIFICATION</h4>
	</div>
	<div class="small-10 small-offset-1 medium-8 large-6 columns">
		<ul>
			<li class="wow fadeIn">Bachelor’s degree in a business or finance related field</li>

			<li class="wow fadeIn">Minimum 3 years experience in a related area</li>
			
			<li class="wow fadeIn">Strong background in financial reporting and analysis</li>
			
			<li class="wow fadeIn">Ability to complete tasks within a given time in high-pressure and fast moving environment</li>
			
			<li class="wow fadeIn">Strong communication, administrative, and organization skills</li>
			
			<li class="wow fadeIn">Advanced level of understanding in Microsoft Excel and PowerPoint</li>
			
			<li class="wow fadeIn">Knowledge of SQL desirable</li>
		</ul>
	</div>
</div>
-->