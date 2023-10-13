<?php
// Require the 'SessionCheck.php' file to check user sessions.
require 'SessionCheck.php';

// Initialize variables for data existence and page title.
$dataExist = false;
$title = '';

// Check if 'page' is set in the GET parameters and set the title accordingly.
if(isset($_GET['page'])){
	if($_GET['page'] == 'hidden_items'){
		$title = "Pending Auction Items";
	}
	else{
		$title = "Listed Auction Items";
	}
}
// Check if 'user_id' is set in the GET parameters and set the title accordingly.
else if(isset($_GET['user_id'])){
	$title = "Your Items";
}
// Check if 'auct_id' is set in the GET parameters and retrieve auction data.
else if(isset($_GET['auct_id'])){
	$auctionData = $allQueries->find('auctions', 'id', $_GET['auct_id']);
	$title = $auctionData['auction_title'];
}

// Require the 'heading.php' file to include the header.
require 'heading.php';

// Check if 'arcId' is set in the GET parameters and archive the auction item.
if (isset($_GET['arcId'])){
	// Criteria for updating the archive column in auction items table
	$criteria = [
		'id' => $_GET['arcId'],
		'hidden' => 1
	];
	// updating the item and making it archived
	$allQueries->update('auction_items', $criteria, 'id');
	// prompting message and redirecting
	echo '<script>alert("Auction Item is Archived Successfully!");
			window.location.href = "items.php?page=hidden_items";
		  </script>';
}
// Check if 'unarcId' is set in the GET parameters and unarchive the auction item.
else if (isset($_GET['unarcId'])){
	// Criteria for updating the archive column in auction items table
	$criteria = [
		'id' => $_GET['unarcId'],
		'hidden' => 0
	];
	// updating the item and making it unarchived
	$allQueries->update('auction_items', $criteria, 'id');
	// prompting message and redirecting
	echo '<script>alert("Auction Item is Unarchived Successfully!");
			window.location.href = "items.php?page=items_on_sale";
		  </script>';
}
?>

<main class="home">
	<?php
	if (isset($_GET['id'])){
		// Retrieve and display the category name based on 'id'.
		$category = $allQueries->find('categories', 'id', $_GET['id']);
		echo "<h1 style = 'text-transform: capitalize;'>". $category['name'] ."</h1>";
	}
	else{
	?>
	<h1><?= $title ?></h1>
	<?php
	}
	?>
	
	<ul class="items">
	<?php
	if (isset($_GET['id'])){
		// Retrieve items based on 'id' and display them.
		$items = $allQueries->select('auction_items', 'categoryId', $_GET['id'].' ORDER BY postedOn');
		$itemsCount = $allQueries->countSpecRows_('auction_items', 'categoryId', $_GET['id'].' ORDER BY postedOn');
	}
	else if(isset($_GET['user_id']) || isset($_GET['page'])){
		// Retrieve items based on 'user_id' or 'page' and display them.
		$items = $allQueries->select('auction_items', 'postedBy', $_SESSION['userid'].' ORDER BY postedOn');
		$itemsCount = $allQueries->countSpecRows_('auction_items', 'postedBy', $_SESSION['userid'].' ORDER BY postedOn');
	?>
	<!-- buttons for redirecting to add item, listed item, pending items, and items graph -->
	<div style="display: flex; flex-direction: row; justify-content: space-between">
		<a href="additem.php" style="padding: 10px; background-color: #444; margin: 10px; color: white; text-decoration: none; border-radius: 10px;">Add Item</a>
		<a href="items.php?page=items_on_sale" style="padding: 10px; background-color: #444; margin: 10px; color: white; text-decoration: none; border-radius: 10px;">Listed Items</a>
		<a href="items.php?page=hidden_items" style="padding: 10px; background-color: #444; color: white; margin: 10px; text-decoration: none; border-radius: 10px;">Pending Items</a>
		<a href="graphical_representation.php" style="padding: 10px; background-color: #444; color: white; margin: 10px; text-decoration: none; border-radius: 10px;">Items Graph</a>
	</div>
	<?php
	}
	else{
		// Retrieve all items and display them.
		$itemsCount = $allQueries->countRows('auction_items ORDER BY postedOn DESC');
		$items = $allQueries->selectAll('auction_items ORDER BY postedOn DESC');
	}
	if (isset($_GET['page'])){
		if($_GET['page'] === 'items_on_sale'){
			// if auction items posted by specific user exists, it will be shown
			if($itemsCount > 0){
				foreach ($items as $item) {
					if($_SESSION['userid'] == $item['postedBy']){
						if (!$item['hidden']){
							$dataExist = true;
							include('show_items.php');
						}
					}
				}
			}
		}
		else if($_GET['page'] === 'hidden_items'){
			// if hidden (archived) items posted by specific user exists, it will be shown
			if($itemsCount > 0){
				foreach ($items as $item) {
					if($_SESSION['userid'] == $item['postedBy']){
						if ($item['hidden']){
							$dataExist = true;
							include('show_items.php');
						}
					}
				}
			}
		}
	}
	else{
		if($itemsCount > 0){
			foreach ($items as $item) {
				// all items posted by logged in user (if exists) is shown
				if((isset($_SESSION['username'])) && (isset($_GET['user_id']))){
					if($_SESSION['userid'] == $item['postedBy']){
						$dataExist = true;
						include('show_items.php');
					}
				}
				// all auction items shown to all kind of users (if the auction date have not passed!)
				if (($item['hidden'] == 0) && ($item['auction_id'])){
					if(isset($_GET['auct_id'])){
						$auctionData = $allQueries->find('auctions', 'id', $_GET['auct_id']);
							foreach($allQueries->select('auction_items', 'auction_id', $_GET['auct_id']) as $item){
								if($auctionData['auction_date'] > date('Y-m-d')){
								include('show_items.php');
							}
						}
					}
					else{
						die;
					}
				}
			}
		}
	}
	?>
	</ul>
</main>

<?php require('footer.php'); ?>
