<?php

// checking if admin is logged in or not (admins can see all the stories available in the database, but users can see only latest 3)...

if ($allQueries->countRows('stories') > 0){
	
if ((isset($_SESSION['adminlogin']) && $_SESSION['adminlogin'] == true)) {

	// selecting all data from stories table...
	$story = $allQueries->selectAll('stories');
?>
<h2 style = "clear: both; margin-top: 80px; padding: 20px; border-top: 2px solid #ccc;">All Stories</h2>
<div id = "story_field">
<?php
	}

// selecting latest 3 data from stories table...
else{
	// selecting latest 3 stories
	$story = $allQueries->limitSelection('stories', 'post_date', 3);
?>
<h2 style = "clear: both; margin-top: 80px; padding: 20px; border-top: 2px solid #ccc;">Latest Stories</h2>
<div>
	<?php
	}
	// extracting the selected data...
	foreach ($story as $stories){
	?>
		<div id = "all_context">

		<!-- showing date when the story was posted... -->
		<div id = "date"><?php echo $stories['post_date']; ?></div>

		<!-- showing the name of staff who posted the story... -->
		<div style = "font-size: 25px; font-weight: bolder;padding-bottom:10px;"><?php echo $stories['staff_name']; ?></div>
		<?php

		// if image is posted, it is shown...
		if ($stories['image']){
			?>
				<div style = "float:left;"><img src = "/images/stories/<?php echo $stories['image']; ?>" style = "width: 300px; height: auto; border-radius: 20px;"></div>
			<?php
		}
			?>

			<!-- showing story's content... -->
		<div style = "font-size: 16px; padding-top: 20px; clear:both;"><?php echo $stories['context']; ?></div>
		</div>
	<?php
	}
}
	?>
</div>