<?php
// including session check for not giving access to non-logged in user
require 'SessionCheck.php';
// checking if auction items with auction id not set exists
if($allQueries->countRows('auction_items WHERE auction_id IS NOT NULL')){
	// if item exists, selecting them all and executing
	foreach($allQueries->selectAll('auction_items WHERE auction_id IS NOT NULL') as $item){
		// if item is not set to delete or deleted...
		if($item['delete'] == 0){
			// finding auction data based on item's auction id
			$auctionData = $allQueries->find('auctions', 'id', $item['auction_id']);
			// if the auction date exceeds today's date
			if($auctionData['auction_date'] < date('Y-m-d')){
				// if the data in bid table exists
				if($allQueries->countRows('bid') > 0){
					// if the specific data (with provided item's id) in bid table exists
					if($allQueries->countSpecRows_('bid', 'item_id', $item['id']) > 0){
						// query for finding highest bid amount from bid table
						$bidAmtQuery = $allQueries->getPdo()->prepare("SELECT * FROM bid WHERE item_id = ".$item['id']." ORDER BY bid_amount DESC LIMIT 1");
						// executing the query
						$bidAmtQuery->execute();
						// fetching the query
						$highBidAmt = $bidAmtQuery->fetch();
						// array for updating 'delete' to 2 which indicates the item is sold as necessary conditions are given
						$criteriaItem = [
							'id' => $item['id'],
							'delete' => 2
						];
						// updating the auction items based on array above
						$allQueries->update('auction_items', $criteriaItem, 'id');
						// array for storing sales data if all the conditions above are fullfilled
						$criteria = [
							'item_id' => $item['id'],
							'sold_by' => $item['postedBy'],
							'sold_to' => $highBidAmt['client_id'],
							'price' => $highBidAmt['bid_amount']
						];
						// inserting sales data in database
						$allQueries->insert('sales', $criteria);
					}
				}
			}
		}
	}
}
// if id is get via url
if(isset($_GET['id'])){
	// if submit button is pressed
	if(isset($_POST['submit'])){
		// the array to save the current status of sales data set by admin is set
		$criteria = [
			'id' => $_GET['id'],
			'status' => $_POST['status'],
		];
		// update query to update date in sales table
		$allQueries->update('sales', $criteria, 'id');
		echo '<script>
				alert("Status Updated!");
				window.location.href = "sales_record.php";
			</script>';
	}
	$title = 'Update Status';
	// finding sales data by id
	$saleData = $allQueries->find('sales', 'id', $_GET['id']);
	require 'heading.php';
	echo '<h2>Update Status</h2>';
	// form to set status of the sales data
	echo '<form method="POST">';
	echo '<label>Status</label><select name="status">';
	echo '<option value="0" selected>Pending</option>';
	echo '<option value="1">Approved</option>';
	echo '</select>';
	echo '<input type="submit" name="submit" value="Update Status">';
	echo '</form>';
}
else{
	// when submit button is pressed without fetching id via url, the sales data will be deleted under necessary conditions
	if (isset($_POST['submit'])){
		$title = 'Delete Sales Data';
		require 'heading.php';
		$id = $_POST['id'];
		// function to deletee sales data
		$allQueries->delete('sales', 'id', $id);
		echo '<h2 style = "text-align: center;">Sales Data deleted</h2>';
	}
	else{
		$title = 'Sales Record';
		require 'heading.php';
	?>
		<h2><?= $title ?></h2>
		<?php
		// counting rows from sales table
		if($allQueries->countRows('sales') > 0){
		echo '<table style="margin-top: 50px;">';
			echo '<thead>';
			echo '<tr>';
				echo '<th>Item Lot No.</th>';
				echo '<th>Item Name</th>';
				echo '<th>Seller</th>';
				echo '<th>Buyer</th>';
				echo '<th>Sold at Price</th>';
				echo '<th>Status</th>';
				echo '<th>Commission (10%)</th>';
			echo '</tr>';
			// selecting all data from sales table
			$sales = $allQueries->selectAll('sales');
			// executing sales data
			foreach ($sales as $sale) {
				// finding auction data based on item id from sales data
				$item = $allQueries->find('auction_items', 'id', $sale['item_id']);
				// finding users data from users table based on sold by column (seller) from sales data
				$seller = $allQueries->find('users', 'id', $sale['sold_by']);
				echo '<tr>';
				echo '<td>' . $item['lot_num'] . '</td>';
				echo '<td>' . $item['item_name'] . '</td>';
				echo '<td><a href="client_details.php?id='.$seller['id'].'">' . $seller['full_name'] . '</a></td>';
				// the buyer name must be kept anonymous
				echo '<td>Anonymous</td>';
				// showing the final price on which item was sold, this was set with the logic that when the auction date ends and the highest bidder will get the item.
				echo '<td>£' . $sale['price'] . '</td>';
				// if status is 0, it will show pending
				if($sale['status'] === 0){
					$status = '<a href="sales_record.php?id='.$sale['id'].'">Pending';
				}
				// if status is 1, it will show approved
				else{
					$status = 'Approved';
				}
				echo '<td>' .  $status . '</td>';
				// calculating 10% commission
				echo '<td>£' . sprintf("%.2f", (($sale['price']/100) * 10)) . '</td>';
				// form to delete sales record
				echo '<td>
						<form method="post">
								<input type="hidden" name="id" value="' . $sale['id'] . '" />
								<input type="submit" name="submit" value="Delete" />
						</form>
					</td>';
				echo '</tr>';
			}
			echo '</thead>';
		echo '</table>';
		}
		else{
			// if no data is available in database
			echo '<p style="text-align: center; font-size: 20px;">No data to show!</p>';
		}
	}
}
?>
</section>
<?php require '../footer.php' ?>
