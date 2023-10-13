<?php
// session check is included to check the specific priviliges and permissions
require 'SessionCheck.php';
$title = 'Auction(s)';
require 'heading.php';
?>
<main class="home">
    <div style="display: flex; flex-direction: row; justify-content: space-between">
    <!-- if username is set, button to show specific  logged in user's item is shown -->
		<?php if (isset($_SESSION['username'])){
			echo '<a href="items.php?user_id='.$_SESSION['userid'].'" style="padding: 10px; background-color: #444; margin: 10px; color: white; text-decoration: none; border-radius: 10px;">Your Items</a>';
		}
		?>
        <!-- button to redirect to search item page -->
		<a href="search.php" style="padding: 10px; background-color: #444; color: white; margin: 10px; text-decoration: none; border-radius: 10px;">Search Items</a>
	</div>
    <h2><?=$title?></h2>
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
    // selecting all data from auctions table
        $admins = $allQueries->selectAll('auctions');
        foreach ($admins as $admin) {
            echo '<tr style="text-align: center;">';
            // showing all auctions data (if available)
            echo '<td><a href="items.php?auct_id=' . $admin['id'] . '">' . $admin['auction_title'] . '</a></td>';
            echo '<td>' . $admin['auction_period'] . '</td>';
            echo '<td>' . $admin['location'] . '</td>';
            echo '<td>' . $admin['auction_date'] . '</td>';
            echo '</tr>';
        }
        echo '</thead>';
        echo '</table>';
    }
    // if no data exists in table, shows message
    else{
        echo '<p style="margin-top: 100px; text-align: center;">No data to show!</p>';
    }
?>
</section>
<?php require 'footer.php' ?>
