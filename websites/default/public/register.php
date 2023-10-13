<?php
require 'AutoloadClass.php';
$title = 'Register';
require 'heading.php';

// calling AllQueries class with autoload function...
$allQueries = new AllQueries;
$emailExists = false;
$weakPassword = false;
// selecting all data from admins
$admins = $allQueries->selectAll('admins');
// selecting all data from users
$users = $allQueries->selectAll('users');
if(isset($_POST['submit'])) {
    $full_name = trim($_POST['full_name']);
    $email = strtolower(trim($_POST['email']));

    //validating password strength...
    $password = trim($_POST['password']); //removing unwanted spaces from password
    $uppercase = preg_match('@[A-Z]@', $password); // returns whether a match (uppercase letter) was found in a string
    $lowercase = preg_match('@[a-z]@', $password); // returns whether a match (lowercase letter) was found in a string
    $number    = preg_match('@[0-9]@', $password); // returns whether a match (numeric value) was found in a string
    $specialchars = preg_match('@[^\w]@', $password); // returns whether a match (special character) was found in a string

    // if password and confirm password matches
    $confirm_password = trim($_POST['confirm_password']);
    // if whitespace is detected between names
    if (preg_match('/\s/', $full_name) === 1) {
        foreach($admins as $admin){
            // checking if email matches with emails from admins table
            if($email == strtolower($admin['email'])){
                $emailExists = true;
            }
        }
        foreach($users as $user){
            // checking if email matches with emails from users table
            if($email == strtolower($user['email'])){
                $emailExists = true;
            }
        }
        // if the email does not exist in both users and admins tables
        if (!$emailExists){
        // if password is not strong
            if(!$uppercase || !$lowercase || !$number || !$specialchars || strlen($password) < 6) {
                echo '<script>alert("Password is too weak!\nHint: Use combination of at least one uppercase, lowercase, numeric value, special character and password should be minimum of 6 characters")</script>';
            }
            // if password is strong with all the criterias met...
            else{
                // if password and confirm password matches
                if ($password === $confirm_password){
                    // array to insert user's data in database
                    $criteria = [
                        'full_name' => $full_name,
                        'email' => $email,
                        'password' => sha1($password)
                    ];
                    // inserting users data in database
                    $allQueries->insert('users', $criteria);
                    // message prompt and redirecting
                    echo '<script>alert("Registration Successful\nYou can now log in!")
                    window.location.href="login.php"</script>';
                }
                else{
                    // if password and confirm password do not match
                    echo '<script>alert("Passwords do not match!\nPlease try again")</script>';
                }
            }
        }
        else{
            // if email already exists
            echo '<script>alert("Email already exists!\nPlease enter another email address and try again")</script>';
        }
    } 
    else{
        // if the name is invalid
        echo '<script>alert("Name is invalid!\nPlease enter valid name and try again")</script>';
    }
}
else{
    $full_name = '';
    $email = '';
}

?>
<div class="login-body">
    <div class="form_login" id="register">
        <form method="POST">
            <h2>Register</h2>
            <div class="cred_input">
                <!-- user icon  -->
                <span class="form_icon">
                    <ion-icon name="person"></ion-icon>
                </span>
                <!-- full name input -->
                <input type="text" id="full_name" name="full_name" autocomplete="off" required autofocus value=<?=$full_name?>>
                <label for="full_name">Full Name</label>
            </div>
            <div class="cred_input">
                <!-- email icon  -->
                <span class="form_icon">
                    <ion-icon name="mail"></ion-icon>
                </span>
                <!-- email input -->
                <input type="email" id="email" name="email" required autocomplete="off" value=<?=$email?>>
                <label for="email">Email</label>
            </div>
            <div class="cred_input">
                <!-- lock closed icon  -->
                <span class="form_icon">
                    <ion-icon name="lock-closed"></ion-icon>
                </span>
                <!-- password input -->
                <input type="password" id="password1" required name="password" >
                <label for="password1">Password</label>
            </div>
            <div class="cred_input">
                <span class="form_icon">
                    <!-- eye button which when clicked, will show the password -->
                    <ion-icon name="eye-outline" class="show-password2"></ion-icon>
                    <!-- eye close button which when clicked, will hide the password -->
                    <ion-icon name="eye-off-outline" class="hide-password2" style="display: none;"></ion-icon>
                    <ion-icon name="lock-closed"></ion-icon>
                </span>
                <!-- confirm password input -->
                <input type="password" id="password2" name="confirm_password" required>
                <label for="password2">Confirm Password</label>
            </div>
            <!-- register button -->
            <button type="submit" name="submit">Register</button>
            <div class="reg_link">
                <!-- login link -->
                <p>Already have an account? <a href="login.php">Login</a>
            </div>
            <!-- homepage link -->
            <div class="home_pg">
                <a href="/">Back to Home</a>
            </div>
        </form>
    </div>
</div>
<?php include('reg_log_script.php') ?>