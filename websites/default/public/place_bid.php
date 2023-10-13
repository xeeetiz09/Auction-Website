<?php
// Include 'SessionCheck.php' for session management.
require 'SessionCheck.php';

// Set the title based on GET parameters.
if(isset($_GET['id'])){
    $item_id = $_GET['id'];
    $title = "Place Bid";
    $amount = '';
}
else{
    $title = 'Error';
}

// including the header.
require 'heading.php';
?>

<main class="admin">
    <section class="left">
        <ul>
        <?php
        // selecting all data from the 'categories' table.
        $categories = $allQueries->selectAll('categories');

        // extracting and displaying the selected data from the 'categories' table.
        foreach ($categories as $category){
            // displaying category in the left navigation menu.
            echo '<li><a style="text-transform: capitalize;" href="cars.php?id='.$category['id'].'">'.$category['name'].'</a></li>';
        }
        ?>
        </ul>
    </section>

    <section class="right">
    <?php
    // handling bid placement or update when the form is submitted.
    if (isset($_POST['submit'])){
        $bidAmount = $_POST['bid_amt'];
        if (isset($_GET['id'])){
            $id = $_GET['id'];
            $clientId = $_SESSION['userid'];
            $criteria = [
                'item_id' => $id,
                'client_id' => $clientId,
                'bid_amount' => $bidAmount,
            ];
            $allQueries->insert('bid', $criteria);
            echo '<script>alert("Bid placed successfully!")
                window.location.href="place_bid.php?id='.$id.'"
                </script>';
        }
    }

    // Display the bid placement/update form.
    if ((isset($_GET['id']))){
        if (isset($_SESSION['username'])){
	    ?>
        <h2>Place Bid</h2>
        <form method="POST" style="margin-bottom: 200px;">
            <label for="bid_amt">Enter Amount</label><span style="float: left; margin: 30px 0 0 -20px;">£</span><input type="number" step="0.01" id="bid_amt" name="bid_amt" required>
            <input type="submit" name="submit" value="OK">
        </form>
        <?php
        }

        // Display bid information if bids exist for the item.
        if($allQueries->countSpecRows_('bid', 'item_id', $item_id) > 0){
            $bidData = $allQueries->select('bid', 'item_id', $item_id);
            foreach ($bidData as $bidDt) {
                $userData = $allQueries->find('users', 'id', $bidDt['client_id']);
                echo "<div style='clear: left; margin-top: 20px; padding: 20px; border-top: 1px solid wheat;'>";
                echo "User's Name: " . $userData['full_name'] . "<br>";
                echo "Bid Amount: $" . $bidDt['bid_amount'] . "<br>";
                echo '</div>';
            }
        }
    }
    else{
        // displaying an error message.
        echo 'Error!<br>';
        echo '<a href="/">Back to Homepage</a>';
    }
    ?>
    </section>
</main>

<!-- included the footer section, consistent across all website pages for users. -->
<?php require('footer.php'); ?>
