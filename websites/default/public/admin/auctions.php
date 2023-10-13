<?php
// included session check page to check if the admin is logged in
require 'SessionCheck.php';
if (isset($_POST['submit'])) {
    $title = 'Delete Auction';
    require 'heading.php';
    // function to delete auctions with id fetched from form.
    $allQueries->delete('auctions', 'id', $_POST['id']);
    // function to delete auction items with id fetched from form.
    $allQueries->delete('auction_items', 'auction_id', $_POST['id']);
    // prompting message
    echo '<p style="text-align: center;">Auction and auction-related items deleted successfully!</p>';
} else {
    $title = 'Auction(s)';
    require 'heading.php';
?>
    <h2><?=$title?></h2>
    <a class="new" href="create_auction.php">Create Auction</a>
    <?php
    // checking if data exists in auctions table
    if($allQueries->countRows('auctions') > 0){
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th style="width: 40%">Title</th>';
    echo '<th style="width: 15%">Period</th>';
    echo '<th style="width: 15%">Location</th>';
    echo '<th style="width: 15%">Date</th>';
    echo '</tr>';
        // fetching all data from auctions table
        $auctions = $allQueries->selectAll('auctions');
        foreach ($auctions as $auction) {
            // showing all data from auctions table
            echo '<tr style="text-align: center;">';
            echo '<td><a href="showroom.php?auct_id=' . $auction['id'] . '">' . $auction['auction_title'] . '</a></td>';
            echo '<td>' . $auction['auction_period'] . '</td>';
            echo '<td>' . $auction['location'] . '</td>';
            echo '<td>' . $auction['auction_date'] . '</td>';
            // button to edit auction data
            echo '<td><a style="float: right" href="create_auction.php?id=' . $auction['id'] . '">Edit</a></td>';
            // button to delete auction data
            echo '<td>
                <form method="post">
                    <input type="hidden" name="id" value="' . $auction['id'] . '" />
                    <input type="submit" name="submit" value="Delete" />
                </form>
                </td>';
            echo '</tr>';
        }
        echo '</thead>';
        echo '</table>';
    }
    else{
        echo '<p style="margin-top: 100px; text-align: center;">No data to show!</p>';
    }
}
?>
</section>
<?php require '../footer.php' ?>
