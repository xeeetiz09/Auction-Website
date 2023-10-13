<?php
// Set a flag to indicate if an auction exists for the item.
$auction = false;

// Create a list item.
echo '<li>';

// Check if the item is associated with an auction.
if ($allQueries->countSpecRows_('auctions', 'id', $item['auction_id']) > 0) {
    // Get auction information if available.
    $event = $allQueries->find('auctions', 'id', $item['auction_id']);
    $auction = true;
}

// Display the auction title if it exists.
if ($auction) {
    echo '<h2 style="text-align: center;">' . $event['auction_title'] . '</h2>';
}

// Display the year of production.
echo '<p style="float: left;">Year of Production: ' . $item['year'] . '</p>';

// Display the auction date if it's associated with an auction.
if ($auction) {
    echo '<p style="float: right;">Auction Date: ' . $event['auction_date'] . '</p>';
}

// Create a container for images.
echo '<div style="margin-top: 40px; clear: both;">';

// Retrieve and display item images.
$images = $allQueries->select('item_images', 'item_id', $item['id']);
foreach ($images as $image) {
    if ($image['item_id'] == $item['id']) {
        if ($image['img_name']) {
            echo '<a href="/images/' . $image['img_name'] . '"><img src="/images/' . $image['img_name'] . '"></a>';
        }
    }
}

// Retrieve the category and user data.
$category = $allQueries->find('categories', 'id', $item['categoryId']);
$userData = $allQueries->find('users', 'id', $item['postedBy']);
$username = $userData['full_name'];

// Check if the current user is the owner of the item.
if (isset($_SESSION['userid'])) {
    if ($_SESSION['userid'] == $item['postedBy']) {
        $username = 'YOU';
    }
}

// Close the images container.
echo '</div>';

// Create a container for item details.
echo '<div class="details">';

// Display the artist name and item name.
echo '<p style="margin-top: 20px; float: right; text-transform: capitalize;">artist name: ' . $item['artist'] . '</p>';
echo '<h2 style="margin-top: 270px; text-transform: capitalize; clear: left;"> ' . $item['item_name'] . '<small style="margin-left: 5px; font-size: 15px;">(' . $category['name'] . ')</small></h2>';

// Display the item's posted by user.
echo '<p style="clear: right; float: right; text-transform: capitalize;">posted by: ' . $username . '</p>';

// Display the item price.
echo '<h3>Around £' . $item['price'] . '</h3>';
echo '<br>';
echo '<p>Item Lot Number: ' . $item['lot_num'] . '</p>';

// Display additional details based on the category.
if ($auction) {
    echo '<p>Location: ' . $event['location'] . '</p>';
}

if ($category['id'] === 1 || $category['id'] === 2) {
    // Display medium and frame details for certain categories.
    echo '<p>Medium: ' . $item['medium'] . '</p>';
    echo '<p>Frame: ';
    echo ($item['isFramed'] === 0) ? 'No' : 'Yes';
    echo '</p>';
    echo '<p>Height: ' . $item['height'] . 'cm &nbsp;&nbsp;Length: ' . $item['length'] . 'cm</p>';
} elseif ($category['id'] === 4 || $category['id'] === 5) {
    // Display material, dimensions, and weight for other categories.
    echo '<p>Material: ' . $item['material'] . '</p>';
    echo '<p>Height: ' . $item['height'] . 'cm &nbsp;&nbsp;Length: ' . $item['length'] . 'cm &nbsp;&nbsp;Width: ' . $item['width'] . 'cm</p>';
    echo '<p>Weight: ' . $item['weight'] . ' KG</p>';
} else {
    // Display type, dimensions for remaining categories.
    echo '<p>Type: ';
    echo ($item['type'] == 0) ? 'Black & White' : 'Color';
    echo '</p>';
    echo '<p>Height: ' . $item['height'] . 'cm &nbsp;&nbsp;Length: ' . $item['length'] . 'cm</p>';
}

if ($auction) {
    // Display auction-specific details.
    echo '<p>Auction Period: ' . $event['auction_period'] . '</p>';
}

// Display classification and location.
echo '<p>Classification: ' . $item['sub_classification'] . '</p>';

if ($auction) {
    echo '<p>Location: ' . $event['location'] . '</p>';
}

// Display item description.
echo '<p>Description: ' . $item['description'] . '</p>';

if (isset($_SESSION['username'])) {
    if ($allQueries->countSpecRows('bid', 'item_id', $item['id'], 'client_id', $_SESSION['userid']) > 0) {
        // If the user has placed a bid on the item, provide an option to update the bid.
        $bidData = $allQueries->find('bid', 'item_id', $item['id']);
        echo '<a href="place_bid.php?update_id=' . $bidData['id'] . '&item_id=' . $item['id'] . '">Update Bid Price</a>';
    } else {
        if ($item['postedBy'] !== $_SESSION['userid']) {
            // If the user is not the owner of the item, provide an option to place a bid.
            echo '<a href="place_bid.php?id=' . $item['id'] . '">Place Bid</a>';
        } else {
            if ($item['delete'] == 1) {
                // If the item is marked for deletion, display a message.
                echo '<a style="color: red;">Item is proceeded for deletion!</a>';
            } else {
                if ($auction) {
                    if (!$item['hidden']) {
                        // Display an option to hide the item in an auction.
                        echo '<a href="items.php?arcId=' . $item['id'] . '">Hide</a>';
                    } else {
                        // Display an option to show the item in an auction.
                        echo '<a href="items.php?unarcId=' . $item['id'] . '">Show</a>';
                    }
                    echo '&nbsp;&nbsp;&nbsp;';
                }
                // Display options to edit or delete the item.
                echo '<a href="additem.php?id=' . $item['id'] . '">Edit</a>';
                echo '&nbsp;&nbsp;&nbsp;';
                echo '<a href="additem.php?del_id=' . $item['id'] . '">Delete</a>';
            }
        }
    }
} else {
    if ($allQueries->countSpecRows_('bid', 'item_id', $item['id']) > 0) {
        // If there are bids on the item, provide a link to view bid amounts.
        echo '<br><br><br><a href="/place_bid.php?id=' . $item['id'] . '">Show Bid Amount(s)</a>';
    }
}

if (!$auction) {
    echo '<div style="float: right;">';
    echo '<strong>Item not listed in auction!</strong>';
    if (isset($_SESSION['adminname'])) {
        // If an admin is logged in, provide an option to list the item.
        echo '&nbsp;&nbsp;<a href="list_item.php?id=' . $item['id'] . '">List Item</a>';
    }
    echo '</div>';
}

if ($allQueries->countRows('bid') > 0) {
    if ($allQueries->countSpecRows_('bid', 'item_id', $item['id']) > 0) {
        // If there are bids on the item, retrieve the highest bid amount.
        $bidAmtQuery = $allQueries->getPdo()->prepare("SELECT * FROM bid WHERE item_id = " . $item['id'] . " ORDER BY bid_amount DESC LIMIT 1");
        $bidAmtQuery->execute();
        $highBidAmt = $bidAmtQuery->fetch();
        $auctionData = $allQueries->find('auctions', 'id', $item['auction_id']);

        // Display the highest bid amount or a "Sold" message if the auction has ended.
        echo '<p style="float: right;">';
        if ($auctionData['auction_date'] < date('Y-m-d')) {
            echo 'Sold!';
        } else {
            echo 'Highest Bid Amount: ' . $highBidAmt['bid_amount'];
        }
        echo  '</p>';
    }
}

if (isset($_SESSION['adminname'])) {
    if ($item['delete'] == 1) {
        // Display a message if the user has requested to delete the item.
        echo '<p style="color: red;">User has requested to delete this item!</p>';
    }

    // Provide an option to delete the item for admin users.
    echo '<br><br><a href="showroom.php?del_id=' . $item['id'] . '">Delete</a><br>';
}

echo '</div>';
echo '</li>';
?>
