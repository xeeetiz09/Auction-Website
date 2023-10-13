<!DOCTYPE html>
<html>

<head>
    <!-- Include the stylesheet and jQuery library -->
    <link rel="stylesheet" href="../styles.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <!-- Set the page title with PHP -->
    <title>FAH - <?php echo $title ?></title>
</head>

<body>
    <!-- Page header -->
    <header>
        <section>
            <!-- FAH logo -->
            <img src="../images/logo.jpg" />
            <?php
            // Check if an admin is logged in to show the navigation menu
            if (isset($_SESSION['adminid'])) {
            ?>
                <!-- Navigation menu for admins -->
                <nav>
                    <ul>
                        <!-- Home link -->
                        <li><a href="index.php">Home</a></li>

                        <?php
                        // Check if the admin has super admin privileges
                        if ($_SESSION['adminid'] === 1) {
                            // Show the Admin link for super admins
                            echo '<li><a href="admins.php">Admin</a></li>';
                        }
                        ?>
                        <!-- Auctions link -->
                        <li><a href="auctions.php">Auctions</a></li>
                        <li><a href="search.php">Search Items</a></li>
                        <!-- Items Showroom link -->
                        <li><a href="showroom.php">Items Showroom</a></li>
                        <!-- Messages link -->
                        <li><a href="contact.php">Messages</a></li>
                        <!-- Log Out link -->
                        <li><a href="/logout.php">Log Out</a></li>
                    </ul>
                </nav>
            <?php
            }
            ?>
        </section>
    </header>

    <!-- Random banner image -->
    <img src="../images/randombanner.php" />
    <!-- Main content section for admin pages -->
    <main class="admin">

        <?php
        // Check if an admin is logged in to show the left-side navigation menu
        if (isset($_SESSION['adminid'])) {
        ?>

            <!-- Left-side navigation menu for admin -->
            <section class="left">
                <ul>
                    <?php
                    // Fetch all categories from the database
                    $categories = $allQueries->selectAll('categories');
                    echo '<ul>';
                    // Categories link
                    echo '<li><a href="categories.php">Categories</a></li>';
                    // Link to Registered Users page
                    echo '<li><a href="registered_users.php">Registered Users</a></li>';
                    // Link to Sales Record page
                    echo '<li><a href="sales_record.php">Sales Record</a></li>';
                    // Generate links for each category
                    foreach ($categories as $category) {
                        echo '<li><a href="showroom.php?id=' . $category['id'] . '">' . $category['name'] . '</a></li>';
                    }
                    echo '</ul>';
                    ?>
                </ul>
            </section>

        <?php
        }
        ?>
        <!-- Right-side content section -->
        <section class="right">
