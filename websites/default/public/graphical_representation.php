<?php
// Start a PHP session to manage user login state.
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Auction Items Graph</title>
    <!-- Include Chart.js library for creating graphs. -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
</head>
<body>
    <!-- link to return to the 'items.php' page. -->
    <a href="items.php">Go Back</a>
    <!-- create a canvas element to render the graph. -->
    <canvas id="itemChart" width="300" height="100" style="margin-top: 100px;"></canvas>

    <script>
        // Get the canvas context for drawing the graph.
        var ctx = document.getElementById('itemChart').getContext('2d');

        <?php
        try {
            // Establish a PDO database connection.
            $conn = new PDO('mysql:dbname=cars;host=db', 'student', 'student');
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Query to retrieve data based on user's login status.
            if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
                if (isset($_SESSION['adminname']) && $_SESSION['adminname']) {
                    // Query for administrators to count items by date.
                    $query = "SELECT DATE(postedOn) AS item_date, COUNT(*) AS item_count FROM auction_items GROUP BY DATE(postedOn) ORDER BY item_date";
                    $result = $conn->query($query);
                } else if (isset($_SESSION['username']) && $_SESSION['username']) {
                    // Query for regular users to count their posted items by date.
                    $userid = $_SESSION['userid'];
                    $query = "SELECT DATE(postedOn) AS item_date, COUNT(*) AS item_count FROM auction_items WHERE postedBy = :userid GROUP BY DATE(postedOn) ORDER BY item_date";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetchAll();
                }
            }

            // Initialize arrays to store dates and item counts.
            $dates = [];
            $itemCounts = [];

            // Populate arrays with query results.
            foreach ($result as $row) {
                $dates[] = $row['item_date'];
                $itemCounts[] = $row['item_count'];
            }
        } catch (PDOException $e) {
            // Handle database connection or query errors.
            echo "Error: " . $e->getMessage();
        }
        ?>

        // Pass PHP arrays to JavaScript for graph rendering.
        var dates = <?php echo json_encode($dates); ?>;
        var itemCounts = <?php echo json_encode($itemCounts); ?>;

        // Calculate the maximum item count for setting the y-axis range.
        var maxItemCount = Math.max(...itemCounts);
        maxItemCount += 5;

        // Create a Chart.js bar chart.
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Number of Items',
                    data: itemCounts,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: maxItemCount, // Set the maximum value for the y-axis
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                        },
                        title: {
                            display: true,
                            text: 'Number of Items'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
