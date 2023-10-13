<?php
// included session check page to check if the admin is logged in
require 'SessionCheck.php';
$title = 'Messages';
require 'heading.php';

if (isset($_GET['id'])){
	$criteria = [
		'enquiry_id' => $_GET['id'],
		'staff' => $_SESSION['adminname']
	];
	// this operation was done to mark the enquiry as dealt by specific admin
	$allQueries->update('customers_enquiry', $criteria, 'enquiry_id');
	echo '<script>alert("Enquiry completed successfully!")
		  window.location.href = "contact.php";
	</script>';
}
?>
<h2><?=$title?></h2>
<div id="enquiry_field">
<?php
// selecting all enquiry data
$enqry = $allQueries->selectAll('customers_enquiry');
// executing all enquiry data?
foreach ($enqry as $enqrys){
?>
	<div id="all_enquiry">
		<!-- showing all enquiry data? -->
		<div id="date"><?php echo $enqrys['enquiry_date']; ?></div>
		<div id="username"><?php echo $enqrys['name']; ?>&nbsp;<label>(<?php echo $enqrys['email']; ?> - <?php echo $enqrys['telephone']; ?>) </label></div>
		<div id="enquiry"><?php echo $enqrys['enquiry']; ?></div>
		<?php
		if (!$enqrys['staff']){
			// if enquiry is not dealt by staff or admin, it will show complete button that will deal with the enquiry
		?>
			<div id="dealt"><a href="contact.php?id=<?php echo $enqrys['enquiry_id']; ?>">Complete</a></div>
		<?php
		} else {
			// else, it will show 'completed by staff or admin name' message
			echo '<p style="float: right; margin-top: 15px; color: green; cursor: default; text-transform: capitalize;">Completed by: '.$enqrys['staff'].'</p>';
		}
		?>
	</div>
<?php
}
?>
</section>
<!-- including footer page -->
<?php require '../footer.php' ?>
