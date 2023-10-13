<?php
// Require 'SessionCheck.php' for session management.
include 'SessionCheck.php';

// Redirect to login if the user is not logged in.
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
}

// Set the title based on GET parameters.
if (isset($_GET['page'])) {
    if ($_GET['page'] === 'sold_items_data') {
        $title = "Sold Items Data";
    } else {
        $title = "Bought Items Data";
    }
} elseif (isset($_GET['change_password'])) {
    $title = "Change Password";
} else {
    $title = "My Profile";
}

// Require 'heading.php' to include the header.
require 'heading.php';
echo '<main class="home">';

if (isset($_GET['page'])) {
    $page = $_GET['page'];

    // Handle 'Sold Items Data' and 'Bought Items Data' pages.
    if ($page == 'sold_items_data') {
        // selecting all data and counting rows from sales table associated with seller
        $userSalesData = $allQueries->select('sales', 'sold_by', $_SESSION['userid']);
        $salesRows = $allQueries->countSpecRows_('sales', 'sold_by', $_SESSION['userid']);
        echo '<h2>Sold Items Data</h2>';
    } else {
        echo '<h2>Bought Items Data</h2>';
        // selecting all data and counting rows from sales table associated with buyer
        $userSalesData = $allQueries->select('sales', 'sold_to', $_SESSION['userid']);
        $salesRows = $allQueries->countSpecRows_('sales', 'sold_to', $_SESSION['userid']);
    }

    // Display user sales data if available.
    if ($salesRows > 0) {
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

        // Extract and display user sales data.
        foreach ($userSalesData as $sale) {
            // finding item data based on item_id from sales  table
            $item = $allQueries->find('auction_items', 'id', $sale['item_id']);
            // finding users data based on sold_to from sales  table
            $buyer = $allQueries->find('users', 'id', $sale['sold_to']);
            // finding users data based on sold_by from sales  table
            $seller = $allQueries->find('users', 'id', $sale['sold_by']);

            // if buyer id matches with logged in user id, then 'You' is shown in buyer instead of their name
            if ($sale['sold_to'] == $_SESSION['userid']) {
                $buyerUser = 'YOU';
            }
            // else buyer's name is set as 'Anonymous'
            else {
                $buyerUser = 'Anonymous';
            }

            // if seller id matches with logged in user id, then 'You' is shown in buyer instead of their name
            if ($sale['sold_by'] == $_SESSION['userid']) {
                $sellerUser = 'YOU';
            } 
            // else sellers actual name is shown
            else {
                $sellerUser = $seller['full_name'];
            }

            echo '<tr>';
            // link to redirection in search page with id which will show specific item
            echo '<td><a href="search.php?id=' . $item['id'] . '">' . $item['lot_num'] . '</a></td>';
            // showing item name
            echo '<td>' . $item['item_name'] . '</td>';
            // showing buyer name (Anonymous) by default (but name is stored in database for security and policy purposes)
            echo '<td>' . $buyerUser . '</td>';
            // seller's actual name is shown
            echo '<td>' . $sellerUser . '</td>';
            // price in which item was sold is shown
            echo '<td>£' . $sale['price'] . '</td>';

            // status is shown as per database
            if ($sale['status'] === 0) {
                $status = 'Pending';
            } else {
                $status = 'Approved';
            }

            echo '<td>' . $status . '</td>';
            echo '</tr>';
        }

        echo '</thead>';
        echo '</table>';
    } else {
        // if no data is available, the message is shown
        echo '<p style="text-align: center; font-size: 20px;">No data to show!</p>';
    }
} elseif (isset($_GET['change_password'])) {
    // Handle the 'Change Password' page.
    if (isset($_POST['change'])) {
        // finding users data on the basis of id set on session
        $userData = $allQueries->find('users', 'id', $_SESSION['userid']);
        $old_password = trim($_POST['old_password']);
        $new_password = trim($_POST['new_password']);
        $cnf_new_password = trim($_POST['cnf_new_password']);
        $uppercase = preg_match('@[A-Z]@', $new_password);
        $lowercase = preg_match('@[a-z]@', $new_password);
        $number    = preg_match('@[0-9]@', $new_password);
        $specialchars = preg_match('@[^\w]@', $new_password);

        // if the old password matches with database
        if ($userData['password'] === sha1($old_password)) {
            // if the password does not contain at least one either uppercase or lowercase or number or special character or is less than 6 words, will show the alert message 
            if (!$uppercase || !$lowercase || !$number || !$specialchars || strlen($new_password) < 6) {
                echo '<script>alert("New Password is too weak!\nHint: Use a combination of at least one uppercase, lowercase, numeric value, special character, and a minimum of 6 characters")</script>';
            } else {
                // if new password and confirm new password matches
                if ($new_password === $cnf_new_password) {
                    // array for changing password
                    $criteria = [
                        'id' => $_SESSION['userid'],
                        'password' => sha1($new_password)
                    ];
                    // function to update users table data (update password)
                    $allQueries->update('users', $criteria, 'id');
                    // message prompts and redirection
                    echo '<script>alert("Your password has been changed!")
                    window.location.href="profile.php"</script>';
                } else {
                    // if password and confirm password donot match
                    echo '<script>alert("Password and Confirm Password do not match!\nPlease try again")</script>';
                }
            }
        } else {
            // if old password do not match to the database
            echo '<script>alert("Your old password is incorrect!\nPlease try again!")</script>';
        }
    }

    // form to change the password
    echo '<h2>Change Password</h2>';
    echo '<form method="POST">';
    // input for old password
    echo '<label>Old Password</label><input type="password" name="old_password" required>';
    // input for new password
    echo '<label>New Password</label><input type="password" name="new_password" required>';
    // input for confirm new password
    echo '<label>Confirm New Password</label><input type="password" name="cnf_new_password" required>';
    // checkbox for either showing or hiding passwords
    echo '<div style="clear:both; display:flex; flex-direction: row; padding-top:20px;">';
    echo '<input style="cursor: pointer; float:left; height: 15px; margin-top:;" type="checkbox" id="showPassword">';
    echo '<label style="cursor: pointer; margin-top:7px; margin-left: -90px;" id="showPasswordLabel" for="showPassword">Show Password</label>';
    echo '</div>';
    // button to change the password
    echo '<input type="submit" value="Change Password" name="change">';
    echo '</form>';
    // javascript that handles the checkbox check/uncheck function (when user checks the checkbox, the  
    // password fields are shown else hidden)
    echo '<script> 
        var showPasswordCheckbox = document.getElementById("showPassword");
        var passwordFields = document.querySelectorAll("input[type=password]");
        var showPasswordLabel = document.getElementById("showPasswordLabel");
        showPasswordCheckbox.addEventListener("change", function() {
            for (var i = 0; i < passwordFields.length; i++) {
                if (this.checked) {
                    passwordFields[i].type = "text";
                    showPasswordLabel.textContent = "Hide Password";
                } else {
                    passwordFields[i].type = "password";
                    showPasswordLabel.textContent = "Show Password";
                }
            }
        });
    </script>';
} else {
    // Display user profile information.
    echo '<h2>My Profile</h2>';
    // finding user by id set on session
    $user = $allQueries->find('users', 'id', $_SESSION['userid']);

    // setting title of user
    if ($user['title']) {
        if ($user['title'] === 1) {
            $userTitle = 'Mr.';
        } else if ($user['title'] === 2) {
            $userTitle = 'Mrs.';
        } else if ($user['title'] === 3) {
            $userTitle = 'Miss.';
        }

        echo '<p>Title: ' . $userTitle . '</p>';
    }
    // showing user's full name
    echo '<p style="text-transform: capitalize;">Full Name: ' . $user['full_name'] . '</p>';
    // showing user's email
    echo '<p>Email: ' . $user['email'] . '</p>';
    // showing date joined by user
    echo '<p>Date Joined: ' . $user['date'] . '</p>';

    // if address, telephone, account number, and bank sort code are set, show, else show link for redirecting to 'Complete your profile' page
    if ($user['address'] && $user['telephone'] && $user['acc_num'] && $user['bank_sort']) {
        echo '<p>Address: ' . $user['address'] . '</p>';
        echo '<p>Telephone Number: ' . $user['telephone'] . '</p>';
        echo '<p>Bank Account Number: ' . $user['acc_num'] . '</p>';
        echo '<p>Bank Sort Code: ' . $user['bank_sort'] . '</p>';
        echo '<p><a style="float: left;" href="complete_profile.php?edit_profile=true">Edit Your Profile</a></p>';
    } else {
        echo '<p><a style="float: left;" href="complete_profile.php?id=' . $user['id'] . '">Complete Your Profile</a></p>';
    }

    // link to change the password
    echo '<p><a style="float: right;" href="profile.php?change_password=true">Change Password</a></p>';
    ?>
    <!-- user's sold and bought items data page redirecting links -->
    <div style="display: flex; flex-direction: row; justify-content: space-evenly; clear: both; margin-top: 70px;">
        <a href="profile.php?page=sold_items_data" style="padding: 10px; background-color: #444; color: white; margin: 10px; text-decoration: none; border-radius: 10px;">Sold Items Data</a>
        <a href="profile.php?page=bought_items_data" style="padding: 10px; background-color: #444; color: white; margin: 10px; text-decoration: none; border-radius: 10px;">Bought Items Data</a>
    </div>
<?php } ?>
</main>
<?php require('footer.php') ?>
