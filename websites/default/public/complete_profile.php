<?php
session_start();
// including autoload class (magic function) to call all classes inside this project
require 'AutoloadClass.php';
// AllQueries class called through magic function and initialized it
$allQueries = new AllQueries;
// if username is not set in session, die!
if(!isset($_SESSION['username'])){
	echo 'Error!';
	die;
}
// title is set...
$title = 'Complete Your Profile';
// if edit_profile is fetched from url
if (isset($_GET['edit_profile'])) {
	$title = 'Edit Your Profile';
}

// requiring heading file which is consistent across all pages of website...
require 'heading.php';

// function to show the current location (Taken from stack overflow: Reference: https://stackoverflow.com/questions/5398674/get-a-users-current-location)
$getGeoLocation = @unserialize(file_get_contents('http://ip-api.com/php/'));
$userTitle = '';
// setting current location by default
$address = $getGeoLocation['city'] . ', ' . $getGeoLocation['country'];
$bank_acc = '';
$sort_code = '';
$invalid_number = false;
if (isset($_GET['edit_profile'])) {
	$userData = $allQueries->find('users', 'id', $_SESSION['userid']);
	$full_name = $userData['full_name'];
	$email = $userData['email'];
	$userTitle = $userData['title'];
	$address = $userData['address'];
	$bank_acc = $userData['acc_num'];
	$sort_code = $userData['bank_sort'];
	$telephone = $userData['telephone'];
}
echo '<main class="home">';
// After pressing submit button...
if (isset($_POST['submit'])) {
	$userTitle = $_POST['title'];
	$address = trim($_POST['address']);
	$bank_acc = $_POST['bank_acc'];
	$sort_code = $_POST['sort_code'];
	$telephone = $_POST['telephone'];
	// checking the telephone number if it is greater than 4 digit and less than 12 digit
	if (strlen($telephone) >= 4 && strlen($telephone) <= 12) {
		// array for updating user's table and inserting complete data
		$completeCriteria = [
			'id' => $_SESSION['userid'],
			'title' => $userTitle,
			'address' => $address,
			'acc_num' => $bank_acc,
			'bank_sort' => $sort_code,
			'telephone' => $telephone,
		];
		// if user clicks on edit profile button
		if (isset($_GET['edit_profile'])) {
			$emailDontExst = false;
			$full_name = $_POST['full_name'];
			// selecting all data from users table
			$users = $allQueries->selectAll('users');
			// selecting all data from admins table
			$admins = $allQueries->selectAll('admins');
			// selecting specific data from users table with session's user id
			$myProfile = $allQueries->find('users', 'id', $_SESSION['userid']);
			// trimming user's inputted email to prevent sql injection
			$email = strtolower(trim($_POST['email']));
			foreach($users as $user){
				// if the email matches with current user but not with other user's email
				if($user['email'] == $email && $myProfile['email'] != $email){
					$emailDontExst = true;
				}
			}
			foreach($admins as $admin){
				// if the email matches with current user but not with other admin's email
				if($admin['email'] == $email && $myProfile['email'] != $email){
					$emailDontExst = true;
				}
			}
			// checking whitespaces between first name and surname to ensure users put their valid full name
			if (preg_match('/\s/', $full_name) === 1) {
				if(!$emailDontExst){
					// array to update user's data
					$editCriteria = [
						'full_name' => $full_name,
						'email' => $email
					];
					$updateCriteria = array_merge($completeCriteria, $editCriteria);
					// updating user's email and name
					$allQueries->update('users', $updateCriteria, 'id');
					// prompting message and redirecting to profile page
					echo '<script>alert("Your Profile Have Been Updated!")
					window.location.href="profile.php"</script>';
					exit;
				}
				else{
					// prompting message if email exists
					echo '<script>alert("Email already exists!")</script>';
				}
			}
			else{
				// prompting message if name is invalid
				echo '<script>alert("Name is invalid!\nPlease enter valid name and try again")</script>';
			}
		}
		else{
			// updating user's data (completing user's profile by updating all empty columns like title, bank code, account number, etc)
			$allQueries->update('users', array_merge($completeCriteria, ['verified' => 0]), 'id');
			session_destroy();
			// prompting message after completing profile
			echo '<script>alert("Your Profile Have Been Completed!\nYou\'ll be able to login once admin verifies your account!")
			window.location.href="/"</script>';
			exit();
		}
	}
	else{
		$invalid_number = true;
	}	
}
?>
<h2 style="text-align: center; border-bottom: 2px solid #ccc; padding-bottom: 10px;"><?= $title ?></h2>
<form method="POST" style="min-height: 550px;">

<!-- showing full name and email input when user clicks on edit profile button -->
	<?php if (isset($_GET['edit_profile'])) {
	?>
		<label>Full Name</label><input type="text" name="full_name" value="<?= $full_name ?>" required>
		<label>Email</label><input type="email" name="email" value="<?= $email ?>" required>
	<?php }
	?>
	<!-- these inputs are shown by default -->
	<label>Title</label>
	<select name="title" required>
		<option disabled selected value="">Choose</option>
		<?php
		$titles = array(
			"Mr.",
			"Mrs.",
			"Miss."
		);

		foreach ($titles as $key => $title) {
			// selecting title of specific user either got from url id or from form post
			$selected = $userTitle == $key + 1 ? 'selected="selected"' : '';
			echo '<option ' . $selected . ' value="' . ($key + 1) . '">' . $title . '</option>';
		}

		?>
	</select>
	<label>Address</label><input type="text" name="address" value="<?= $address ?>" required>

	<!-- input field for writing user's telephone number...  -->
	<label>Telephone</label><input type="number" name="telephone" value="<?= $telephone ?>" required>
	<?php

	// if the number is invalid, prompts error message...
	if ($invalid_number == true) {
		echo "<p style = 'clear: both; margin-left: 220px; color: red;'>Invalid telephone number. Please try again!</p>";
	}
	?>

	<label>Bank Account Number</label><input type="number" name="bank_acc" value="<?= $bank_acc ?>" required>
	<label>Bank Sort Code</label><input type="number" name="sort_code" value="<?= $sort_code ?>" required>

	<!-- submit button -->
	<input type="submit" name="submit" value="Done">
</form>
</main>

<!-- including footer page which is consistent across all pages of website for users -->
<?php require('footer.php'); ?>
