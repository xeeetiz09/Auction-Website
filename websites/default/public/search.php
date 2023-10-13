<?php
// requiring AutoloadClass to load the required classes automatically (autoloading class)
require 'SessionCheck.php';
// calling AllQueries class with autoload function...
$allQueries = new AllQueries;
// title is set...
$title = "Search Items";

// requiring heading file which is consistent across all pages of website...
require 'heading.php';
?>
<main class="home">
	<?php
    if (isset($_GET['id'])){
        $item = $allQueries->find('auction_items', 'id', $_GET['id']);
        echo '<h2>'.$item['item_name'].'</h2>';
    }
    else{
        echo '<h2>Search Auction Item</h2>';
    }
	?>
	<ul class="items">
	<?php
	if (isset($_GET['id'])){
        include('show_items.php');
    }
	else{
		?>
		<div style="display: flex; flex-direction: row; justify-content: space-between; flex-wrap: wrap;">
			<input type="search" id="artist_search" oninput="searchAuctionItems()" placeholder="Artist" style="margin: 15px; padding: 10px;">
			<input type="search" id="category_search" oninput="searchAuctionItems()" placeholder="Category" style="margin: 15px; padding: 10px;">
			<input type="search" id="price_search" oninput="searchAuctionItems()" placeholder="Price" style="margin: 15px; padding: 10px;">
			<input type="search" id="date_search" oninput="searchAuctionItems()" placeholder="Auction Date" style="margin: 15px; padding: 10px;">
			<input type="search" id="classifict_search" oninput="searchAuctionItems()" placeholder="Subject Classification" style="margin: 15px; padding: 10px;">
		</div>
		<p id="results"></p>
		<ul id="auctionItemList">
		</ul>
		<?php
	}
	?>
	</ul>
</main>
<?php
	$items = $allQueries->selectAll('auction_items');
    // Initialize an empty array to store the transformed data
    $jsData = [];
    
    // Loop through the PHP data and transform it into JavaScript format
    foreach ($items as $item) {
		$categoryName = $allQueries->find('categories', 'id', $item['categoryId']);
		if($allQueries->countRows('auction_items WHERE auction_id IS NOT NULL') > 0){
			if($allQueries->countRows('sales WHERE item_id = '. $item['id']) == 0){
				$event = $allQueries->find('auctions', 'id', $item['auction_id']);
				$jsItem = [
					'id' => $item['id'],
					'lot_num' => $item['lot_num'],
					'name' => $item['item_name'],
					'price' => $item['price'],
					'artist' => $item['artist'],
					'category' => $categoryName['name'],
					'date' => $event['auction_date'],
					'classification' => $item['sub_classification'],
				];
			
				// Add the transformed item to the JavaScript data array
				$jsData[] = $jsItem;
			}
		}
    }
    
    // Encode the JavaScript data as JSON
    $jsDataJSON = json_encode($jsData);
    
	// script for searching items based on user's input and highlighting the matched words in respective fields
	// shows empty when no item matches
	echo '
	<script>
		const auctionItems = ' . $jsDataJSON . ';
		function searchAuctionItems() {
			const artistSearchInput = document.getElementById(\'artist_search\');
			const categorySearchInput = document.getElementById(\'category_search\');
			const priceSearchInput = document.getElementById(\'price_search\');
			const dateSearchInput = document.getElementById(\'date_search\');
			const classifictSearchInput = document.getElementById(\'classifict_search\');
			const result = document.getElementById(\'results\');

			const artistSearchText = artistSearchInput.value.toLowerCase().trim();
			const categorySearchText = categorySearchInput.value.toLowerCase().trim();
			const priceSearchText = priceSearchInput.value;
			const dateSearchText = dateSearchInput.value;
			const classifictSearchText = classifictSearchInput.value.toLowerCase().trim();

			const auctionItemList = document.getElementById(\'auctionItemList\');

	
			// Clear previous results
			auctionItemList.innerHTML = \'\';
	
			if (artistSearchText === \'\' && classifictSearchText === \'\' && categorySearchText === \'\' && priceSearchText === \'\' && dateSearchText === \'\') {
				return;
			}
	
			// Filter items that match the search text
			const matchingItems = auctionItems.filter(item =>
				item.artist.toLowerCase().includes(artistSearchText) && 
				item.category.toLowerCase().includes(categorySearchText) && 
				item.price.toString().includes(priceSearchText) && 
				item.classification.toLowerCase().includes(classifictSearchText) && 
				item.date.includes(dateSearchText)
			);
			
			result.innerHTML = \'Total Results: \'+ matchingItems.length;

			if (matchingItems.length === 0) {
				// If no matching items are found, display "No results found!"
				const listItem = document.createElement(\'li\');
				listItem.textContent = \'No results found!\';
				auctionItemList.appendChild(listItem);
			} else {
				// Create list items for matching items
				matchingItems.forEach(item => {
					const anchor = document.createElement(\'a\');
        			anchor.href = \'search.php?id=\' + item.id; // Correctly concatenate the item.id
					anchor.textContent = \'Show Item\';
					const listItem = document.createElement(\'li\');
					const artistWithHighlight = item.artist.replace(new RegExp(artistSearchText, \'gi\'), match => `<span class="highlight">${match}</span>`);
					const categoryWithHighlight = item.category.replace(new RegExp(categorySearchText, \'gi\'), match => `<span class="highlight">${match}</span>`);
					const dateWithHighlight = item.date.replace(new RegExp(dateSearchText, \'gi\'), match => `<span class="highlight">${match}</span>`);
					const classifictWithHighlight = item.classification.replace(new RegExp(classifictSearchText, \'gi\'), match => `<span class="highlight">${match}</span>`);
					const priceWithHighlight = item.price.toString().replace(new RegExp(priceSearchText, \'gi\'), match => `<span class="highlight">${match}</span>`);
					listItem.innerHTML = `
						Lot Number: ${item.lot_num}<br>
						Item Name: ${item.name}<br>
						Artist: ${artistWithHighlight}<br>
						Category: ${categoryWithHighlight}<br>
						Auction Date: ${dateWithHighlight}<br>
						Classification: ${classifictWithHighlight}<br>
						Price: ${priceWithHighlight}
						<br>
					`;
					// Append the anchor element to the list item
					listItem.appendChild(anchor);
					auctionItemList.appendChild(listItem);
				});
			}
		}
	</script>
	';
	
?>

<!-- including footer page which is consistent across all pages of website for users -->
<?php require('footer.php'); ?>