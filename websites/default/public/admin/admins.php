<?php
// included session check page to check if the admin is logged in
require 'SessionCheck.php';
if (isset($_POST['submit'])) {
    $title = 'Delete Admin';
    require 'heading.php';
    // function to delete admin with id fetched from form.
    $allQueries->delete('admins', 'id', $_POST['id']);
    echo '<h2 style="text-align: center;">Admin deleted</h2>';
} else {
    $title = 'Admin(s)';
    require 'heading.php';

    // only admin having id 1 can access this page.
    if ($_SESSION['adminid'] === 1) {
?>
    <h2><?=$title?></h2>
    <a class="new" href="addadmin.php">Add new admin</a>
    <?php
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Username</th>';
    echo '<th style="width: 50%">E-mail</th>';
    echo '<th style="width: 5%">&nbsp;</th>';
    echo '<th style="width: 5%">&nbsp;</th>';
    echo '</tr>';
    // selecting all data from admins table
    $admins = $allQueries->selectAll('admins');
    foreach ($admins as $admin) {
        // showing all data from admins table
        echo '<tr style="text-align: center;">';
        echo '<td>' . $admin['full_name'] . '</td>';
        echo '<td>' . $admin['email'] . '</td>';
        // admin cannot edit or delete themself
        if($admin['id'] == 1){
            echo '<td>YOU</td>';
        }
        // but can edit or delete other admins
        else{
            // button to edit admin data
            echo '<td><a style="float: right" href="addadmin.php?id=' . $admin['id'] . '">Edit</a></td>';
            // form for deleting admin
            echo '<td>
                <form method="post">
                    <input type="hidden" name="id" value="' . $admin['id'] . '" />
                    <input type="submit" name="submit" value="Delete" />
                </form>
                </td>';
        }
        echo '</tr>';
    }
    echo '</thead>';
    echo '</table>';
    } 
    // message for unauthorized access...
    else {
        echo "You don't have permission to access this resource!";
    }
}
?>
</section>
<?php require '../footer.php' ?>
