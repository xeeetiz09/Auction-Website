<?php
// Start a PHP session.
session_start();

// Require the 'AutoloadClass.php' for class autoloading.
require 'AutoloadClass.php';

// Set the page title.
$title = 'Login';

// Require the 'heading.php' file to include the header.
require 'heading.php';

// Create an instance of the 'AllQueries' class.
$allQueries = new AllQueries;

// Check if the user is already logged in as an admin and redirect if true.
if(isset($_SESSION['adminlogin']) && $_SESSION['adminlogin'] == true){
    echo '<script>window.location.href="/admin/"</script>';
}
// Check if the user is already logged in as a regular user and redirect if true.
else if(isset($_SESSION['userLogin']) && $_SESSION['userLogin'] == true){
    echo '<script>window.location.href="/"</script>';
}

// Initialize the 'email' variable.
$email = '';

// Check if the login form is submitted.
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if both email and password fields are not empty.
    if (!empty($email) && !empty($password)) {
        // Retrieve admin and user data from the database.
        $admins = $allQueries->selectAll('admins');
        $users = $allQueries->selectAll('users WHERE verified IS NOT NULL');

        // Check if the login credentials match any admin's credentials.
        foreach ($admins as $admin) {
            // checking if email and password of admin matches with database
            if (($admin['email'] == $email) && (sha1($password) === $admin['password'])) {
                // setting sessions for user authorization and priviliges
                $_SESSION['adminid'] = $admin['id']; 
                $_SESSION['adminname'] = $admin['full_name'];
                $_SESSION['login'] = true;
                // prompting success message and redirecting to admin page if inputs matches with database credentials
                echo '<script>alert("You are logged in as admin!")
                        window.location.href = "/admin/";
                    </script>';
                exit();
            }
        }

        // Check if the login credentials match any user's credentials.
        foreach ($users as $user) {
            if (($user['email'] == $email) && (sha1($password) === $user['password'])) {
                // setting sessions for user authorization and priviliges
                $_SESSION['userid'] = $user['id']; 
                $_SESSION['username'] = $user['full_name'];
                $_SESSION['login'] = true;

                // check user verification status and redirect accordingly with message prompt.
                if($user['verified'] == 1){
                    echo '<script>alert("You are logged in as user!")
                        window.location.href = "index.php";
                        </script>';
                    exit();
                }
                else if($user['verified'] == 2){
                    echo '<script>alert("Complete your profile before proceeding!")
                        window.location.href = "complete_profile.php";
                    </script>';
                    exit();
                }
                else if($user['verified'] == 0){
                    session_destroy();
                    echo '<script>alert("Your account is not verified by admin.\nHave patience!")
                        window.location.href = "/";
                        </script>';
                    exit();
                }
            }
        }

        // If no matching credentials are found, show login failed alert.
        echo '<script>alert("Login failed. Please check your credentials.")</script>';
    } else {
        // If email or password is empty, show alert to fill in both fields.
        echo '<script>alert("Please fill in both email and password fields.")</script>';
    }
}
?>

<div class="login-body">
    <div class="form_login" id="login">
        <form method="POST">
            <h2>Login</h2>
            <div class="cred_input">
                <!-- email icon  -->
                <span class="form_icon">
                    <ion-icon name="mail"></ion-icon>
                </span>
                <!-- email input -->
                <input type="email" id="email" name="email"  autocomplete="off" autofocus value="<?= $email ?>">
                <label for="email">Email</label>
            </div>
            <div class="cred_input">
                <span class="form_icon">
                    <!-- eye button which when clicked, will show the password -->
                    <ion-icon name="eye-outline" class="show-password1"></ion-icon>
                    <!-- eye close button which when clicked, will hide the password -->
                    <ion-icon name="eye-off-outline" class="hide-password1" style="display: none;"></ion-icon>
                    <ion-icon name="lock-closed"></ion-icon> 
                </span>
                <!-- input for password -->
                <input type="password" id="password" name="password" >
                <label for="password">Password</label>
            </div>
            <!-- login button -->
            <button type="submit" name="submit">Login</button>
            <div class="reg_link">
                <!-- register link -->
                <p>Don't have an account? <a href="register.php">Register</a>
            </div>
            <!-- home page link -->
            <div class="home_pg">
                <a href="/">Back to Home</a>
            </div>
        </form>
    </div>
</div>

<?php include('reg_log_script.php') ?>
