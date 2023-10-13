<!DOCTYPE html>
<html>

<head>
	<?php 
	// Check if the current page is not 'login.php' or 'register.php' and include the appropriate stylesheet.
	if (!((basename($_SERVER['PHP_SELF']) === 'login.php') || (basename($_SERVER['PHP_SELF']) === 'register.php'))) { 
		echo '<link rel="stylesheet" href="./styles.css" />';
	}
	else{
		echo '<link rel="stylesheet" href="./reg_log.css" />';
	}
	?>

	<title>FAH - <?=$title?></title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>

<body>
<?php 
	// Check if the current page is not 'login.php' or 'register.php' and display header and navigation.
	if (!((basename($_SERVER['PHP_SELF']) === 'login.php') || (basename($_SERVER['PHP_SELF']) === 'register.php'))) { ?>
	<header>
		<section>
			<img src="./images/logo.jpg" />
			<nav>
				<ul>
					<li><a href="/">Home</a></li>
					<li><a href="auctions.php">Auction(s)</a></li>
					<?php if (isset($_SESSION['username'])){
						echo '<li><a href="profile.php">Profile</a></li>';
						$notifCount = '';
						$style = '';
						if($allQueries->countRows('auction_items WHERE auction_id IS NOT NULL') > 0){
							$notifCount = '<sup style="border-radius: 60px; padding: 3px 8px; background-color: #ccc; color: #000;">'.$allQueries->countRows('auction_items WHERE auction_id IS NOT NULL').'</sup>';
							$style = 'style="margin-top: -4px;"';
						}
						echo '<li><a href="notifications.php" '. $style .'>Notifications'.$notifCount.'</a></li>';
					}
					?>
					<li><a href="contact.php">Contact Us</a></li>
					<?php if (isset($_SESSION['username'])){
						echo '<li><a href="logout.php">Logout</a></li>';
						}else{ ?>
						<li><a href="register.php">Register</a></li>
						<li><a href="login.php">Login</a></li>
					<?php } ?>
				</ul>
			</nav>
		</section>
	</header>
	<img src="./images/randombanner.php" />

	<?php } ?>
