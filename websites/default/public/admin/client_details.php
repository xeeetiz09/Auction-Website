<?php
// included session check page to check if the admin is logged in
require 'SessionCheck.php';
$title = 'Client Data';
require 'heading.php';
if(isset($_GET['id'])){
    // finding specific user with fetched id
    $userData = $allQueries->find('users', 'id', $_GET['id']);
}
else{
    // if no id is fetched, nothing will be shown
    echo 'Error!';
    die;
}
// setting title for specific value stored in database
if($userData['title'] == 1){
    $userTitle = 'Mr.';
}
else if($userData['title'] == 2){
    $userTitle = 'Mrs.';
}
else if($userData['title'] == 3){
    $userTitle = 'Miss.';
}

// selecting all data from sales table
$salesData = $allQueries->selectAll('sales');
// initializing status to no by default
$status = 'No';
// executing sales data from database
foreach($salesData as $sale){
    // if buyer id (sold_to) matches with specific user's id from users table
    if($userData['id'] == $sale['sold_to']){
        // if admin have approved the user's request to buy item then they
        //  are approved buyers as they have bought at least one item from fotheby's auction house
        if($sale['status'] == 1){
            $status = 'Yes';
        }
    }
}

if(isset($_GET['id'])){
    // function that returns rows count from sales table where sold_by column (Seller) is equal to client detail
    //  in short, checking if the client is seller
    $salesDataRow = $allQueries->countSpecRows_('sales', 'sold_by', $_GET['id']);
    // function that returns rows count from sales table where sold_to column (Buyer) is equal to client detail
    //  in short, checking if the client is buyer
    $buysDataRow = $allQueries->countSpecRows_('sales', 'sold_to', $_GET['id']);
    if($salesDataRow > 0){
        $resultRole = 'Seller';
        // if user have records of both buying and selling that are approved, then they are Joint.
        if($buysDataRow > 0){
            $resultRole = 'Joint';
        }
    }
    else if($buysDataRow > 0){
        $resultRole = 'Buyer';
    }
    else{
        $resultRole = '';
    }
    if(isset($_GET['page'])){
        $page = $_GET['page'];
        if($page == 'bought_items_data'){
            // selecting buyer data (of specific client) from sales table 
            $userSalesData = $allQueries->select('sales', 'sold_to', $_GET['id']);
            // counting rows from sales table (of specific client)
            $salesRows = $allQueries->countSpecRows_('sales', 'sold_to', $_GET['id']);
            echo '<h2 style="text-transform: capitalize;">'. $userData['full_name'] .'\'s Bought Items Data</h2>';
        }
        else{
            // selecting seller data (of specific client) from sales table 
            $userSalesData = $allQueries->select('sales', 'sold_by', $_GET['id']);
            // counting rows from sales table (of specific client)
            $salesRows = $allQueries->countSpecRows_('sales', 'sold_by', $_GET['id']);
            echo '<h2 style="text-transform: capitalize;">'. $userData['full_name'] .'\'s Sold Items Data</h2>';
        }
        if($salesRows > 0){
            echo '<table style="margin-top: 50px;">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Item Lot No.</th>';
                    echo '<th>Item Name</th>';
                    echo '<th>Buyer</th>';
                    echo '<th>Seller</th>';
                    echo '<th>Sold at Price</th>';
                    echo '<th>Status</th>';
                echo '</tr>';
                // executing sales data from specific client
                foreach ($userSalesData as $sale) {
                    // finding one auction item based on item_id from sales table
                    $item = $allQueries->find('auction_items', 'id', $sale['item_id']);
                    // finding one user data based on sold_to from sales table
                    $buyer = $allQueries->find('users', 'id', $sale['sold_to']);
                    // finding one user data based on sold_by from sales table
                    $seller = $allQueries->find('users', 'id', $sale['sold_by']);
                    echo '<tr>';
                    // showing sales data
                        echo '<td><a href="search.php?id='.$item['id'].'">' . $item['lot_num'] . '</a></td>';
                        echo '<td>' . $item['item_name'] . '</td>';
                        echo '<td>' . $buyer['full_name'] . '</td>';
                        echo '<td>' . $seller['full_name'] . '</td>';
                        echo '<td>£' . $sale['price'] . '</td>';
                        // if the status is 0 it will be pending else approved
                        if($sale['status'] === 0){
                            $status = 'Pending';
                        }
                        else{
                            $status = 'Approved';
                        }
                        echo '<td>' .  $status . '</td>';
                    echo '</tr>';
                }
                echo '</thead>';
            echo '</table>';
        }
        else{
            echo '<p style="text-align: center; font-size: 20px;">No data to show!</p>';
        }
    }
    else{
        // showing specific client's detail
        echo '<h2 style="text-transform: capitalize;">'. $userData['full_name'] .'\'s Data</h2>';
        echo '<p>Title: '. $userTitle .'</p>';
        echo '<p>Full Name: '. $userData['full_name'] .'</p>';
        echo '<p>Contact Address: '. $userData['address'] .'</p>';
        echo '<p>Contact Telephone Number: '. $userData['telephone'] .'</p>';
        echo '<p>Contact E-mail: '. $userData['email'] .'</p>';
        if($resultRole != ''){
            echo '<p>Client Status: '. $resultRole .'</p>';
            if($resultRole !== 'Seller'){
                // showing specific status ie buyer, seller, joint
                echo '<p>Buyer Approved Status: '.$status.'</p>';
            }
        }
        echo '<p>Bank Account Number: '. $userData['acc_num'] .'</p>';
        echo '<p>Bank Sort Code: '. $userData['bank_sort'] .'</p>';
        echo '<p>Date Registered: '. $userData['date'] .'</p>';
        echo '<div style="display: flex; flex-direction: row; justify-content: space-evenly; clear: both; margin-top: 70px;">';
        // if the client is either seller or joint, show sold items data button
        if ($resultRole == 'Joint' || $resultRole == 'Seller') {
            echo '<a href="client_details.php?id=' . $_GET['id'] . '&page=sold_items_data" style="padding: 10px; background-color: #444; color: white; margin: 10px; text-decoration: none; border-radius: 10px;">Sold Items Data</a>';
        }
        // if the client is either buyer or joint, show bought items data button
        if ($resultRole == 'Joint' || $resultRole == 'Buyer') {
            echo '<a href="client_details.php?id=' . $_GET['id'] . '&page=bought_items_data" style="padding: 10px; background-color: #444; color: white; margin: 10px; text-decoration: none; border-radius: 10px;">Bought Items Data</a>';
        }
        echo '</div>';
    }
}
else{
    echo 'error!<br>';
    echo '<a href="index.php">Back to Homepage!</a>';
    die;
}
?>
</section>
<?php require '../footer.php' ?>
