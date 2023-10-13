<?php
// Include the SessionCheck.php file for session management.
require 'SessionCheck.php';

// Check if 'id' is set in the query string.
if (isset($_GET['id'])) {
    // Set the page title to 'List Item'.
    $title = 'List Item';

    // Fetch item data from the 'auction_items' table based on the provided 'id'.
    $item = $allQueries->find('auction_items', 'id', $_GET['id']);
} else {
    // If 'id' is not set, display an error message and terminate the script.
    echo 'Error!';
    die;
}

// Include the heading.php file to render the page header.
require 'heading.php';

// Check if the 'submit' form has been submitted.
if (isset($_POST['submit'])) {
    // Get the selected auction ID from the form.
    $auction = $_POST['auctionId'];

    // Define criteria to update the item's status to 'listed' in the 'auction_items' table.
    $criteria = [
        'id' => $_GET['id'],
        'hidden' => 0,
        'auction_id' => $auction
    ];

    // Update the item's status.
    $allQueries->update('auction_items', $criteria, 'id');

    // Display a success message.
    echo 'Item is listed on Auction!';
} else {
    // Check if there are auctions available in the 'auctions' table.
    if ($allQueries->countRows('auctions') > 0) {
?>
        <!-- Display the title of the page -->
        <h2><?= $title ?></h2>
        <form method="POST">
            <?php
            // Display a label for auctions.
            echo '<label style="clear: both;">Auctions</label>';
            echo '<div style = "margin-left: 220px;clear: both; display: grid; grid-template-columns:5% 35% 5% 35%;">';

            // Loop through available auctions and display radio buttons.
            foreach ($allQueries->selectAll('auctions') as $auction) {
                echo '<input style = "width: 20px; margin-top:-10px;" type="radio" id="' . $auction['id'] . '" name="auctionId" required value="' . $auction['id'] . '"><label style = "margin-top:-10px; cursor: pointer;" for="' . $auction['id'] . '">' . $auction['auction_title'] . '</label>';
            }
            echo '</div>';
            ?>
            <!-- Submit button to list the item on an auction -->
            <input type="submit" name="submit" value="List Item">
        </form>
<?php
    } else {
        // If there are no auctions available, display a message to create one.
        echo 'No auctions available! Create one and try again!';
    }
}
?>
</section>
<?php require '../footer.php' ?>
