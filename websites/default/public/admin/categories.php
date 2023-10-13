<?php
// included session check page to check if the admin is logged in
require 'SessionCheck.php';
if (isset($_POST['submit'])) {
    $title = 'Delete Category';
    require 'heading.php';
    // function to delete auctions with id fetched from form.
    $allQueries->delete('categories', 'id', $_POST['id']);
    // prompting message
    echo '<h2 style="text-align: center;">Category deleted</h2>';
    // link to go back to categories page
    echo '<a href="categories.php">GO BACK</a>';
} else {
    $title = 'Categories';
    require 'heading.php';
?>
    <h2><?=$title?></h2>
    <a class="new" href="addcategory.php">Add New Category</a>
    <?php
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Name</th>';
    echo '<th style="width: 5%">&nbsp;</th>';
    echo '<th style="width: 5%">&nbsp;</th>';
    echo '</tr>';
    // function to select all data from categories table.
    $categories = $allQueries->selectAll('categories');
    foreach ($categories as $category) {
        // showing all data from categories table
        echo '<tr>';
        echo '<td>' . $category['name'] . '</td>';
        // button to edit category
        echo '<td><a style="float: right" href="addcategory.php?id=' . $category['id'] . '">Edit</a></td>';
        // button to delete category
        echo '<td>
                <form method="post">
                    <input type="hidden" name="id" value="' . $category['id'] . '" />
                    <input type="submit" name="submit" value="Delete" />
                </form>
              </td>';
        echo '</tr>';
    }
    echo '</thead>';
    echo '</table>';
}
?>
</section>
<!-- requiring footer page -->
<?php require '../footer.php' ?>
