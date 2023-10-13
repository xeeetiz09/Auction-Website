<?php
// Include the SessionCheck.php file for session management.
require 'SessionCheck.php';

// Set the default page title to 'Create Auction'.
$title = 'Create Auction';

// Check if 'id' is set in the query string, indicating an update operation.
if(isset($_GET['id'])){
    // If 'id' is set, change the title to 'Update Auction'.
    $title = 'Update Auction';
}

// Include the 'heading.php' file to render the page header.
require 'heading.php';

// Initialize variables to hold auction data.
$auctTitle = '';
$auctPeriod = '';
$auctLocat = '';
$auctDate = '';

// Check if 'id' is set in the query string to load existing data for an update.
if (isset($_GET['id'])){
    // Fetch auction data using the 'find' method from the 'allQueries' object.
    $auctionData = $allQueries->find('auctions', 'id', $_GET['id']);
    $auctTitle = $auctionData['auction_title'];
    $auctPeriod = $auctionData['auction_period'];
    $auctLocat = $auctionData['location'];
    $auctDate = $auctionData['auction_date'];
}

// Check if the form is submitted.
if (isset($_POST['submit'])) {
    // Retrieve and sanitize form input values.
    $auctTitle = trim($_POST['auction_title']);
    $auctLocat = trim($_POST['location']);
    $auctPeriod = trim($_POST['auction_period']);
    $auctDate = trim($_POST['auction_date']);

    // Create an array to hold criteria for database operations.
    $criteria = [
        'auction_period' => trim($_POST['auction_period']),
        'auction_date' => $_POST['auction_date'],
        'auction_title' => trim($_POST['auction_title']),
        'location' => trim($_POST['location'])
    ];

    // Initialize a flag to check for existing auction titles.
    $auctionTitleExist = false;

    // Loop through all auctions to check if the title already exists.
    foreach($allQueries->selectAll('auctions') as $auction){
        if($auction['auction_title'] == $auctTitle){
            $auctionTitleExist = true;
        }
    }

    // Check if it's an update operation.
    if (isset($_GET['id'])){
        // If the auction title already exists and it's not the same as the current title, show an alert.
        if ($auctionTitleExist && ($auctionData['auction_title'] != $auctTitle)){
            echo '<script>
            alert("Auction Title Already Exists");
            </script>';
        }
        else{
            // if the auction date is more than todays date, then only execute further code
            if($_POST['auction_date'] > date('Y-m-d')){
                // Update the auction data in the database.
                $auction_id = $_GET['id'];
                $allQueries->update('auctions', array_merge($criteria, ['id' => $auction_id]), 'id');
                
                // Fetch specific auction items associated with this auction.
                $specAucData = $allQueries->select('auction_items', 'auction_id', $auction_id);

                // Iterate through specific auction items and clear their auction association.
                foreach($specAucData as $specData){
                    $auctionCrt = [
                        'id' => $specData['id'],
                        'auction_id' => null
                    ];
                    $allQueries->update('auction_items', $auctionCrt, 'id');
                }

                // Check if items were selected for this auction and associate them.
                if(isset($_POST['items'])){
                    foreach($_POST['items'] as $item){
                        $criteria = [
                            'id' => $item,
                            'hidden' => 0,
                            'auction_id' => $auction_id
                        ];
                        $allQueries->update('auction_items', $criteria, 'id');
                    }
                }
                echo '<script>
                alert("Auction Updated Successfully!");
                </script>';
            }
        else{
            echo '<script>
            alert("Auction date is invalid! Please try again!");
            </script>';
        }
        }
    }
    else{
        // If it's a new auction and the title already exists, show an alert.
        if ($auctionTitleExist){
            echo '<script>
            alert("Auction Title Already Exists");
            </script>';
        }
        else{
            // if the auction date is more than todays date, then only execute further code
            if($_POST['auction_date'] > date('Y-m-d')){
                // Insert a new auction into the database.
                $allQueries->insert('auctions', $criteria);
                $auction_id = $allQueries->getPdo()->lastInsertId();

                // Check if items were selected for this auction and associate them.
                if(isset($_POST['items'])){
                    foreach($_POST['items'] as $item){
                        $criteria = [
                            'id' => $item,
                            'auction_id' => $auction_id
                        ];
                        $allQueries->update('auction_items', $criteria, 'id');
                    }
                }
                echo '<script>
                alert("Auction Created Successfully");
                </script>';
            }
            else{
                echo '<script>
                alert("Auction date is invalid! Please try again!");
                </script>';
            }
        }
    }
}
   
?>
<h2><?= $title ?></h2>
<form method="POST">
    <!-- Auction Title Input -->
    <label>Auction Title</label><input type="text" value="<?= $auctTitle ?>" autofocus name="auction_title" required />

    <!-- Location Dropdown -->
    <label>Location</label>
    <select name="location" required>
        <option disabled selected value="">Choose</option>
        <?php
        // Define an array of location options.
        $locations = array(
            "London",
            "New York",
            "Paris"
        );

        // Generate location options based on the array.
        foreach ($locations as $location) {
            $selected = isset($_GET['id']) && $auctLocat == $location ? 'selected="selected"' : '';
            echo '<option ' . $selected . ' value="' . $location . '">' . $location . '</option>';
        }
        ?>
    </select>

    <!-- Auction Period Dropdown -->
    <label>Auction Period</label>
    <select name="auction_period" required>
        <option disabled selected value="">Choose</option>
        <?php
        // Define an array of auction period options.
        $auc_periods = array(
            "Morning",
            "Afternoon",
            "Evening"
        );

        // Generate auction period options based on the array.
        foreach ($auc_periods as $auc_period) {
            $selected = isset($_GET['id']) && $auctPeriod == $auc_period ? 'selected="selected"' : '';
            echo '<option ' . $selected . ' value="' . $auc_period . '">' . $auc_period . '</option>';
        }
        ?>
    </select>

    <!-- Auction Date Input -->
    <label>Auction Date</label><input type="date" name="auction_date" value="<?= $auctDate ?>" required />

    <?php
    // Initialize flags to check for the existence of items and specific auction items.
    $itemsExist = false;
    $auctionItemExist = false;

    // Check if there are items in the 'auction_items' table.
    if($allQueries->countRows('auction_items') > 0){
        foreach ($allQueries->selectAll('auction_items') as $item) {
            if (isset($_GET['id'])){
                // If 'id' is set, check if the item is associated with the current auction.
                if($item['auction_id'] == $_GET['id']){
                    $auctionItemExist = true;
                }
            }
            // Check if the item is not marked for deletion and is not associated with any auction.
            if(($item['delete'] == 0) && !$item['auction_id']){
                $itemsExist = true;
            }
        }
    }
    
    // Check if it's an update operation.
    if(isset($_GET['id'])){
        echo '<label style="clear: both;">Items</label>';
        echo '<div style = "margin-left: 220px;clear: both; display: grid; grid-template-columns:5% 35% 5% 35%;">';

        // If specific auction items exist, display them.
        if($auctionItemExist){
            foreach ($allQueries->selectAll('auction_items') as $item) {
                if($item['auction_id'] == $_GET['id']){
                    echo '<input style = "width: 20px; margin-top:-10px;" type="checkbox" checked id="'. $item['id'] .'" name="items[]" value="' . $item['id'] . '"><label style = "margin-top:-10px; cursor: pointer;" for="'. $item['id'] .'">' . $item['item_name'] . '</label>';
                }
            }
        }

        // If there are unassociated items, display them for selection.
        if($itemsExist){
            foreach ($allQueries->selectAll('auction_items') as $item) {
                if(($item['delete'] == 0) && !$item['auction_id']){
                    echo '<input style = "width: 20px; margin-top:-10px;" type="checkbox" id="'. $item['id'] .'" name="items[]" value="' . $item['id'] . '"><label style = "margin-top:-10px; cursor: pointer;" for="'. $item['id'] .'">' . $item['item_name'] . '</label>';
                }
            }
        }
        echo '</div>';
    }
    else{
        // If it's a new auction and there are unassociated items, display them for selection.
        if($itemsExist){
            echo '<label style="clear: both;">Items</label>';
            echo '<div style = "margin-left: 220px;clear: both; display: grid; grid-template-columns:5% 35% 5% 35%;">';
            foreach ($allQueries->selectAll('auction_items') as $item) {
                if(($item['delete'] == 0) && !$item['auction_id']){
                    echo '<input style = "width: 20px; margin-top:-10px;" type="checkbox" id="'. $item['id'] .'" name="items[]" value="' . $item['id'] . '"><label style = "margin-top:-10px; cursor: pointer;" for="'. $item['id'] .'">' . $item['item_name'] . '</label>';
                }
            }
            echo '</div>';
        }
    }
    ?>

    <!-- Submit Button -->
    <input type="submit" name="submit" value="<?=$title?>">
</form>
</section>

<?php require '../footer.php' ?>
