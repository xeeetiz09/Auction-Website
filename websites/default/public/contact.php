<?php
// including autoload class (magic function) to call all classes inside this project
require 'SessionCheck.php';
// title is set...
$title = 'Contact Us';
// requiring heading file which is consistent across all pages of website...
require 'heading.php';
?>
<main class="home">
<?php

// initializing the variables...
$invalid_name = false;
$inserted = false;
$invalid_number = false;
$invalid_enquiry = false;

$name = '';
$email = '';
$telephone = '';
$enquiry = '';

// After pressing submit button...
if (isset($_POST['submit'])){
	$name = trim($_POST['name']);
	$email = trim($_POST['email']);
	$telephone = trim($_POST['telephone']);
	$enquiry = trim($_POST['enquiry']);

	// checking if the entered name's length is greater than or equals to 3...
	if (strlen($name) >= 3){

		// the valid telephone number must be minimum of 4 and maximum of 12 digits...
		if (is_numeric($telephone) && strlen($telephone) >= 4 && strlen($telephone) <= 12){

			// the word length of the enquiry must be minimum of 20 words...
			if (strlen($enquiry) >= 20){
				// criteria to insert in customer's enquiry table
				$criteria = [
					'name' => $name,
					'email' => $email,
					'telephone' => $telephone,
					'enquiry' => $enquiry,
					'staff' => ''
				];
				// inserting above array in customers_enquiry table
				$allQueries->insert('customers_enquiry', $criteria);
				// prompting message
				echo '<script>alert("Your enquiry have been recieved. Our team will try to solve your problem as soon as possible.\nThanks!")</script>';
			}
			else{
				$invalid_enquiry = true;
			}
		}
		else{
			$invalid_number = true;
		}
	}
	else{
		$invalid_name = true;
	}
}
?>
<h2 style = "text-align: center; border-bottom: 2px solid #ccc; padding-bottom: 10px;"><?= $title ?></h2>

<!-- form for writing enquiry... -->
<form method = "POST" style = "min-height: 550px;">

	<!-- input field for writing user's name...  -->
	<label>Name</label><input type = "text" name = "name" value="<?=$name?>" required>
	<?php

	// if the name is invalid, prompts error message...
	if ($invalid_name == true){
		echo"<p style = 'clear: both; margin-left: 220px; color: red;'>Please enter your full name!</p>";
	}
	?>

	<!-- input field for writing user's email...  -->
	<label>Email</label><input type = "email" name = "email" value="<?=$email?>" required>

	<!-- input field for writing user's telephone number...  -->
	<label>Telephone</label><input type = "text" name = "telephone" value="<?=$telephone?>" required>
	<?php

	// if the number is invalid, prompts error message...
	if ($invalid_number == true){
		echo"<p style = 'clear: both; margin-left: 220px; color: red;'>Invalid telephone number. Please try again!</p>";
	}
	?>

	<!-- input field for writing enquiry...  -->
	<label>Enquiry</label><textarea required name = "enquiry" style = "width: 50%;"><?=$enquiry?></textarea>
	<?php

	// if the enquiry is invalid, prompts error message...
	if ($invalid_enquiry == true){
		echo"<p style = 'clear: both; margin-left: 220px; color: red;'>Please write the enquiry clearly and conviniently so that it would be easier for our team to interact with you and solve your problem efficiently.</p>";
	}
	?>

	<!-- submit button -->
	<input type = "submit" name = "submit" value = "Submit">
</form>
</main>

<!-- including footer page which is consistent across all pages of website for users -->
<?php require('footer.php'); ?>
