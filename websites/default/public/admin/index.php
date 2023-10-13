<?php
// Include the SessionCheck.php file for session management.
require 'SessionCheck.php';

// Set the page title.
$title = 'Admin Home';

// Check if the 'post' form has been submitted.
if (isset($_POST['post'])) {
    // Get the admin's name from the session.
    $staff_name = $_SESSION['adminname'];

    // Get the content from the form.
    $context = trim($_POST['context']);

    // Get the uploaded image details.
    $image = $_FILES['uploadfile']['name'];
    $tempname = $_FILES['uploadfile']['tmp_name'];

    // Define the folder where the image will be saved.
    $folder = "../images/stories/" . $image;

    // Move the uploaded image to the specified folder.
    move_uploaded_file($tempname, $folder);

    // Create an array to hold the data for the new story.
    $criteria = [
        'staff_name' => $staff_name,
        'context' => $context,
        'image' => $image
    ];

    // Insert the new story into the 'stories' table.
    $allQueries->insert('stories', $criteria);

    // Show a JavaScript alert to indicate successful posting.
    echo '<script>alert("Story posted successfully!")</script>';
}

// Include the heading.php file to render the page header.
require 'heading.php';
?>
<!-- Display a welcome message with the admin's name -->
<h2 style="text-transform: capitalize; text-align: center;">
    <?php
    echo "Welcome, " . $_SESSION['adminname'];
    ?>
</h2>

<!-- Form for posting a new story -->
<form method="POST" enctype="multipart/form-data">
    <div style="display: flex; flex-direction: row; justify-content: space-evenly;">
        <!-- Text area for entering the story content -->
        <textarea style="height: 80px; width: 450px;" placeholder="Announce Something" required name="context"></textarea>
        <!-- Image upload button -->
        <label for="story_img"><img src="/images/picupload.jpg" style="height: 80px; margin-left: 20px; cursor: pointer;"></label>
        <!-- Hidden input for file upload -->
        <input hidden type="file" name="uploadfile" accept="image/*" id="story_img">
    </div>
    <!-- Submit button for posting the story -->
    <input style="width: 20%;" type="submit" name="post" value="POST">
</form>

<!-- Include a script to display existing stories -->
<?php
include('../show_stories.php');
?>
</section>
<?php require '../footer.php' ?>
