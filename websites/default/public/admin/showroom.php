<?php
require 'SessionCheck.php';

// check if 'del_id' is set in the query parameters to delete an item.
if(isset($_GET['del_id'])){
	$allQueries->delete('auction_items', 'id', $_GET['del_id']);
	$allQueries->delete('item_images', 'item_id', $_GET['del_id']);
	echo '<script>alert("Item Deleted Successfully!")</script>';
}

// determine the title based on query parameters or defaults.
if(isset($_GET['page'])){
	if ($_GET['page'] === 'items_on_sale'){
		$title = 'Visible';
	}
	else if ($_GET['page'] === 'hidden_items'){
		$title = 'Hidden';
	}
	else{
		$title = 'Pending';
	}
}

else if(isset($_GET['id'])){
	// finding category data based on fetched id via url
	$category = $allQueries->find('categories', 'id', $_GET['id']);
	$title = $category['name'];
}
else if(isset($_GET['auct_id'])){
	// finding auctions data based on fetched id via url
	$auction = $allQueries->find('auctions', 'id', $_GET['auct_id']);
	$title = $auction['auction_title'];
}
else{
	$title = 'All';
}
$title .= ' Items';

require 'heading.php';
echo '<h1>'.$title.'</h1>';
echo '<br>';

// Display filter links for item categories.
if(!isset($_GET['id']) && !isset($_GET['auct_id'])){
?>
<div style="display: flex; flex-direction: row; justify-content: space-between;">
	<!-- link to show all items -->
	<a href="showroom.php" style="padding: 10px; background-color: #444; color: white; margin: 10px; text-decoration: none; border-radius: 10px;">All Items</a>
	<!-- link to show visible auction items -->
	<a href="showroom.php?page=items_on_sale" style="padding: 10px; background-color: #444; margin: 10px; color: white; text-decoration: none; border-radius: 10px;">Visible Items</a>
	<!-- link to show pending items -->
	<a href="showroom.php?page=hidden_items" style="padding: 10px; background-color: #444; color: white; margin: 10px; text-decoration: none; border-radius: 10px;">Hidden Items</a>
	<!-- link to show hidden auction items -->
	<a href="showroom.php?page=pending_items" style="padding: 10px; background-color: #444; color: white; margin: 10px; text-decoration: none; border-radius: 10px;">Pending Items</a>
</div>
<br><br>
<?php
}

// Display items based on the filter conditions.
$dataExist = false;
echo '<ul class="items">';
// selecting all rows from auction items table
$items = $allQueries->selectAll('auction_items');
// counting rows from auction items table
$itemsCount = $allQueries->countRows('auction_items');

if(isset($_GET['id'])){
	// counting specific rows from auction items table based on category's id provided via url
	$itemsCount = $allQueries->countSpecRows_('auction_items', 'categoryId', $_GET['id']);
	// selecting specific rows from auction items table based on category's id provided via url
	$items = $allQueries->select('auction_items', 'categoryId', $_GET['id']);
}

if (isset($_GET['page'])){
	// if page is set and page is items on sale
	if($_GET['page'] === 'items_on_sale'){
		foreach ($items as $item) {
			// if the item is not hidden and auction id is set
			if (($item['hidden'] == 0) && $item['auction_id']){
				include('../show_items.php');
				$dataExist = true;
			}
		}
	}
	else if($_GET['page'] === 'hidden_items'){
		// if page is set and page is hidden items
		foreach ($items as $item) {
			// if the item is hidden and auction id is set
			if (($item['hidden'] == 1) && $item['auction_id']){
				include('../show_items.php');
				$dataExist = true;
			}
		}
	}
	else if($_GET['page'] === 'pending_items'){
		// if page is set and page is pending items
		foreach ($items as $item) {
			// if the item is hidden and auction id is not set
			if (($item['hidden'] == 1) && !$item['auction_id']){
				include('../show_items.php');
				$dataExist = true;
			}
		}
	}
}
else if(isset($_GET['auct_id'])){
	// if auction id is get via url
	foreach ($items as $item) {
		// if auction id is equal to auction id column from auction_items table
		if ($item['auction_id'] == $_GET['auct_id']){
			include('../show_items.php');
			$dataExist = true;
		}
	}
}
else{
	foreach ($items as $item) {
		// if item exists
		if($itemsCount > 0){
			$dataExist = true;
			include('../show_items.php');
		}
	}
}

// if data do not exists, show message
if(!$dataExist){
	echo '<p style="text-align: center; margin: 100px 0;">No data to show!</p>';
}
?>
</ul>
</section>
<?php require '../footer.php' ?>
