<?php
// Include 'SessionCheck.php' to check user sessions.
include 'SessionCheck.php';

// Redirect to login page if the user is not logged in.
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
}

// Set the page title.
$title = "Notifications";

// Include 'heading.php' to include the header.
require 'heading.php';

// Initialize the main content section.
echo '<main class="home">';

// Check if 'id' is set in the GET parameters.
if(isset($_GET['id'])){
    // Retrieve item, event, and user data.
    $item = $allQueries->find('auction_items', 'id', $_GET['id']);
    $eventData = $allQueries->find('auctions', 'id', $item['auction_id']);
    $userData = $allQueries->find('users', 'id', $item['postedBy']);

    // Calculate the issue date for item withdrawal.
    $issueDate = new DateTime($eventData['date']);
    $issueDate->modify('-2 weeks');

    // Display the notification content.
    echo'<div style="float: right;">Date: <em>'.$eventData['date'].'</em></div>';
    echo '<p style="margin-top: 50px; clear: right;">Dear '.$userData['full_name'].',</p>';
    echo '<p style="margin-top: 30px;">We are pleased to inform you that your piece, 
    '.$item['item_name'].', has been scheduled for sale at our 
    auction house in '.$eventData['location'].'
    on '.date('l', strtotime($eventData['auction_date'])).' - '.$eventData['auction_date'].'.</p>';
    echo '<p>May I take this opportunity to remind you that 
    should you wish to withdraw your item from the sale, 
    you must notify this department by '. $issueDate->format('Y-m-d') .'. Any requests to 
    withdraw the piece from sale after the stated 
    deadline will result in a fee equivalent to 5% of the lower estimated 
    price for your piece, this being £ '. ($item['price'] * 5)/100 .', in line with your original sale agreement.</p>';
    echo '<p">May I also take this opportunity again to thank you for using Fotherby’s auction house, as we seek to achieve the best possible selling price for your item.</p>';
    echo '<p>Yours Sincerely,</p>';
    echo '<p style="margin-top: 20px;">Mr. M Fotherby</p>';
}
else{
    // Display the notification listing.
    echo '<h2>'.$title.'</h2>';
    // counting rows of data from auction items table
    $itemsRow = $allQueries->countRows('auction_items');
    // if data exists
    if($itemsRow > 0){
        // selecting all data from auction items table
        foreach($allQueries->selectAll('auction_items WHERE auction_id IS NOT NULL') as $item){
            // checking if the items which have auction_id set (which are listed on auction) exists in database
            if($allQueries->countRows('auction_items  WHERE auction_id IS NOT NULL') > 0){
                // checking if data is deleted or is requested for deletion or none
                if($item['delete'] == 0){
                    $qnt = 'New';
                    // checking if the item is posted by logged in user
                    if($item['postedBy'] == $_SESSION['userid']){
                        $qnt = 'Your';
                        echo '<a style="float:right; margin-left: 40px;" href="notifications.php?id='.$item['id'].'">Show Message</a>';
                    }
                    // link to show specific item
                    echo '<a style="float:right;" href="search.php?id='.$item['id'].'">Show Item</a>';
                    echo '<p>'.$qnt.' item has been listed!</p>';
                }
            }
            else{
                // showing message if no specific data is available
                echo '<p style="text-align: center;">No data to show!</p>';
            }
        }
    }
    else{
        // showing message if no data is available
        echo '<p style="text-align: center;">No data to show!</p>';
    }
}

echo '</main>';

// included the footer page.
require('footer.php');
?>
