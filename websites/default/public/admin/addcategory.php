<?php
// Initialized 'exists' flag as false
$exists = false;
// Included session check
require 'SessionCheck.php';

// Checked if 'id' was set in the query parameters
if (isset($_GET['id'])) {
    // If 'id' was set, updated category mode, so the title was set to 'Update Category'
    $title = 'Updated Category';
} else {
    // If 'id' was not set, added category mode, so the title was set to 'Add Category'
    $title = 'Added Category';
}

// Included the heading file
require 'heading.php';

// Checked if the form was submitted (submit button was clicked)
if (isset($_POST['submit'])) {
    $categoryName = trim($_POST['name']);
    $criteria = [
        'name' => $categoryName
    ];
    // Retrieved all categories from the database
    $categories = $allQueries->selectAll('categories');
    // Checked if the category name already exists
    foreach ($categories as $category) {
        if ($category['name'] == $categoryName) {
            $exists = true;
        }
    }
    // Checked if 'id' was set (update mode)
    if (isset($_GET['id'])) {
        $idArr = ['id' => $_GET['id']];
        $categToUpdate = $allQueries->find('categories', 'id', $_GET['id']);
        // If the category exists and the name is different, displayed an alert and redirected
        if ($exists && $categToUpdate['name'] != $categoryName) {
            echo '<script>alert("Category already existed!");';
            echo 'window.location.href = "categories.php";</script>';
        }
        // Otherwise, updated the category record and displayed a success alert
        else {
            $updateCrt = array_merge($idArr, $criteria);
            $allQueries->update('categories', $updateCrt, 'id');
            echo '<script>alert("Category updated successfully!");';
            echo 'window.location.href = "categories.php";</script>';
        }
    } else {
        // If the category doesn't exist, inserted the new category and displayed a success alert
        if (!$exists) {
            $allQueries->insert('categories', $criteria);
            echo '<script>alert("Category added successfully");';
            echo 'window.location.href = "categories.php";</script>';
        } else {
            // If the category already exists, displayed an alert
            echo '<script>alert("Category already exists");';
            echo 'window.location.href = "categories.php";</script>';
        }
    }
} else {
    if (isset($_GET['id'])) {
        $category = $allQueries->find('categories', 'id', $_GET['id']);
    }
?>
    <h2><?= $title ?></h2>
    <form method="POST">
        <label>Name</label><input type="text" autofocus name="name" <?php if (isset($_GET['id'])) {
            echo 'value = "' . $category['name'] . '"';
        } ?>/>
        <input type="submit" name="submit" value="<?php echo isset($_GET['id']) ? 'Update Category' : 'Add Category'; ?>" />
    </form>
<?php
}
?>
</section>
<?php require '../footer.php' ?>
