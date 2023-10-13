<?php
// Included session check
require 'SessionCheck.php';

// Checked if 'id' was set in the query parameters
if (isset($_GET['id'])) {
    // If 'id' was set, updated admin mode, so the title was set to 'Update Admin'
    $title = 'Update Admin';
    // Found the admin by 'id' using a custom function
    $admin = $allQueries->find('admins', 'id', $_GET['id']);
} else {
    // If 'id' was not set, added admin mode, so the title was set to 'Add Admin'
    $title = 'Add Admin';
}

// Included the heading file
require 'heading.php';

// Checked if the form was submitted (submit button was clicked)
if (isset($_POST['submit'])) {
    // Trimmed and retrieved form input values
    $full_name = trim($_POST['full_name']);
    $password = trim($_POST['password']);
    // Retrieved all admins and users from the database
    $admins = $allQueries->selectAll('admins');
    $users = $allQueries->selectAll('users');
    // Converted email to lowercase and trimmed
    $email = strtolower(trim($_POST['email']));
    $exist = false; // Initialized 'exist' flag as false

    // Checked if the email already existed in admins or users
    foreach ($admins as $admin) {
        if ($admin['email'] == $email) {
            $exist = true; // Email existed in admins
        }
    }
    foreach ($users as $user) {
        if ($user['email'] == $email) {
            $exist = true; // Email existed in users
        }
    }

    // Created an array with user data
    $criteria = [
        'full_name' => trim($_POST['full_name']),
        'email' => $email,
        'password' => sha1(trim($_POST['password']))
    ];

    // Checked if 'id' was set (update mode)
    if (isset($_GET['id'])) {
        // If 'id' was set and email already existed, showed an alert and redirected
        if ($exist && $admin['email'] != $email) {
            echo '<script>alert("Unable to update!\nAdmin account already existed!")';
            echo 'window.location.href = "admins.php";</script>';
        }
        // Otherwise, updated the admin record and showed a success alert
        else {
            $idArr = ['id' => $_GET['id']];
            $updateCrt = array_merge($criteria, $idArr);
            $allQueries->update('admins', $updateCrt, 'id');
            echo '<script>alert("Admin account updated successfully!")';
            echo 'window.location.href = "admins.php";</script>';
        }
    }
    // If 'id' was not set (add mode)
    else {
        // If the email did not exist, inserted the new admin and showed a success alert
        if (!$exist) {
            $allQueries->insert('admins', $criteria);
            echo '<script>alert("Admin account added successfully!")';
            echo 'window.location.href = "admins.php";</script>';
        }
        // If the email already existed, showed an alert
        else {
            echo '<script>alert("Admin account already existed!")';
            echo 'window.location.href = "admins.php";</script>';
        }
    }
}
// If the form was not submitted, displayed the form or a permission message
else {
    // Checked if the user had admin permission (adminid 1)
    if ($_SESSION['adminid'] === 1) {
?>
        <!-- Displayed the form -->
        <h2><?= $title ?></h2>
        <form method="POST">
            <label>Full Name</label>
            <input autofocus type="text" name="full_name" <?php if (isset($_GET['id'])) {
                echo 'value = "' . $admin['full_name'] . '"';
            } ?> required />
            <label>Email</label>
            <input type="email" name="email" <?php if (isset($_GET['id'])) {
                echo 'value = "' . $admin['email'] . '"';
            } ?> required />
            <label>Set <?= (isset($_GET['id']) ? 'New' : '') ?> Password</label>
            <input type="password" name="password" required />
            <input type="submit" name="submit" value="<?php if (isset($_GET['id'])) {
                echo "Update";
            } else {
                echo "Add";
            } ?>" />
        </form>
<?php
    } 
    else {
        // Displayed a permission message if the user did not have admin permission
        echo "You did not have permission to access this resource!";
    }
}
?>
</section>
<?php require '../footer.php' ?>
