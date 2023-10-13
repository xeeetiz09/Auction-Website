<?php
// Include the SessionCheck.php file for session management.
require 'SessionCheck.php';

// Fetch all users from the 'users' table.
$users = $allQueries->selectAll('users');

// Get the total count of user data.
$userDataCount = $allQueries->countRows('users');

// Set the page title to 'Registered User's Data'.
$title = 'Registered User\'s Data';

// Check if 'page' is set in the query string.
if (isset($_GET['page'])) {
    if ($_GET['page'] == 'verified_users') {
        // If 'page' is 'verified_users', update the title and fetch verified users.
        $title = 'Verified Users Record';
        $userDataCount = $allQueries->countSpecRows_('users', 'verified', 1);
        $users = $allQueries->select('users', 'verified', 1);
    } else {
        // If 'page' is 'unverified_users', update the title and fetch unverified users.
        $title = 'Unverified Users Record';
        $userDataCount = $allQueries->countSpecRows_('users', 'verified', 0);
        $users = $allQueries->select('users', 'verified', 0);
    }
}

// Check if 'id' is set in the query string.
if (isset($_GET['id'])) {
    if (isset($_POST['submit'])) {
        // If 'id' is set and 'submit' is posted, update the user's status.
        $criteria = [
            'id' => $_GET['id'],
            'verified' => $_POST['status'],
        ];
        $allQueries->update('users', $criteria, 'id');
        echo '<script>
                alert("User Verified!");
                window.location.href = "registered_users.php";
            </script>';
    } else {
        // If 'id' is set but 'submit' is not posted, update the title and render the update status form.
        $title = 'Update User\'s Status';
        require 'heading.php';
        echo '<h2>Update Status</h2>';
        echo '<form method="POST">';
        echo '<label>Status</label><select name="status">';
        echo '<option value="0" selected>Pending</option>';
        echo '<option value="1">Approve</option>';
        echo '</select>';
        echo '<input type="submit" name="submit" value="Update Status">';
        echo '</form>';
    }
} elseif (isset($_GET['del_id'])) {
    // Check if 'del_id' is set in the query string.
    $title = 'Delete User\'s Data';
    require 'heading.php';
    echo '<h2 style="text-align: center;">Confirm User Data Deletion!</h2>';
    echo '<div style="clear:both; display:flex; flex-direction: row; padding-top:20px;">';
    echo '<input style="cursor: pointer; height: 15px; margin: 10px;" type="checkbox" id="showMessagebox">';
    echo '<label style="cursor: pointer; margin-top: 7px;" id="showMessageboxLabel" for="showMessagebox">Send Message</label>';
    echo '</div>';
    echo '<form method="POST">';
    echo '<div id="message-box" style="display: none;">';
    echo '<label>Message</label>';
    echo '<textarea name="del_msg" style="width: 50%;"></textarea>';
    echo '</div>';
    echo '<input type="submit" name="delete" value="Confirm Delete">';
    echo '</form>';
    // javascript code to check if the checkbox is checked or not and if it is checked, the message box for writing
    // user's deletion message is shown and is unchecked, it will be hidden.
    echo '<script> 
            var showMessageboxCheckbox = document.getElementById("showMessagebox");
            var messageBox = document.getElementById("message-box");
            
            showMessageboxCheckbox.addEventListener("change", function() {
                if (this.checked) {
                    messageBox.style.display = "block";
                } else {
                    messageBox.style.display = "none";
                }
            });
        </script>';
    // If 'delete' is posted, handle user data deletion.
    if (isset($_POST['delete'])) {
        // if delete message is written by admin, then it will be saved in database
        if ($_POST['del_msg'] != '') {
            $delete_message = 'Account deletion message by Admin: ' . $_POST['del_msg'];
        } 
        // else, by default, below given string is stored.
        else {
            $delete_message = 'Deleted by Admin';
        }
        $delcrt = [
            'id' => $_GET['del_id'],
            'del_msg' => $delete_message,
            'verified' => null
        ];
        $allQueries->update('users', $delcrt, 'id');
        echo '<script>alert("User Deleted!")
                window.location.href = "registered_users.php"
            </script>';
    }
} else {
    // If none of the above conditions are met, render the user data table.
    require 'heading.php';

    // Check if the admin has super admin privileges.
    if ($_SESSION['adminid'] === 1) {
?>
        <h2><?= $title ?></h2>
        <?php
        echo '<div style="display: flex; justify-content: space-evenly;">';
        // button to show verified users
        echo '<a href = "registered_users.php?page=verified_users">Verified Users</a>';
        // button to show unverified users
        echo '<a href = "registered_users.php?page=unverified_users">Unverified Users</a>';
        echo '</div>';
        // if data exists
        if ($userDataCount > 0) {
            echo '<table style="margin-top: 50px;">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Full Name</th>';
            echo '<th>Email</th>';
            echo '<th>Address</th>';
            echo '<th>Telephone</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            // setting title as per integer stored in database
            foreach ($users as $user) {
                if ($user['title'] == 1) {
                    $userTitle = 'Mr.';
                } elseif ($user['title'] == 2) {
                    $userTitle = 'Mrs.';
                } else {
                    $userTitle = 'Miss.';
                }
                echo '<tr>';
                // showing user's data with specific client redirection link
                echo '<td><a href="client_details.php?id=' . $user['id'] . '">' . $userTitle . ' ' . $user['full_name'] . '</a></td>';
                echo '<td>' . $user['email'] . '</td>';
                echo '<td>' . $user['address'] . '</td>';
                echo '<td>' . $user['telephone'] . '</td>';
                // if the user is not verified, it will show pending
                if ($user['verified'] === 0) {
                    $status = '<a href="registered_users.php?id=' . $user['id'] . '">Pending</a>';
                } 
                // if the user is verified, it will show approved
                else if ($user['verified'] === 1) {
                    $status = 'Approved';
                }
                // if user's deleted, it will show deleted
                if ($user['del_msg']) {
                    $status = 'Deleted';
                }
                echo '<td>' .  $status . '</td>';
                // if user is not deleted, it will show button to delete user
                if (!$user['del_msg']) {
                    echo '<td>
                            <a href="registered_users.php?del_id=' . $user['id'] . '">Delete</a>
                        </td>';
                }
                echo '</tr>';
            }
            echo '</thead>';
            echo '</table>';
        } 
        // if not data is available, the below message is shown
        else {
            echo '<p style="text-align: center; font-size: 20px; margin-top: 100px;">No data to show!</p>';
        }
    } 
    // if necessary priviliges is not obtained, it will show message below
    else {
        echo "You don't have permission to access this resource!";
    }
}
?>
</section>
<?php require '../footer.php' ?>
