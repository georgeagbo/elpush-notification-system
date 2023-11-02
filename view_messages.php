<?php
// Start the PHP session
session_start();

// Check if the user is not logged in, and redirect to the login page if needed
if (!isset($_SESSION["user_phone"])) {
    header("Location: index.php"); // Change "index.php" to your login page's filename
    exit;
}

// Replace these values with your actual database credentials
$servername = "localhost";
$username = "cetvetar_elpush";
$password = "Hadolk01@";
$database = "cetvetar_elpush"; // Corrected variable name

// Create a database connection
$conn = new mysqli($servername, $username, $password, $database); // Corrected variable name

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming you have a user ID in your session data
if (isset($_SESSION["user_phone"])) {
    $userId = $_SESSION["user_phone"];

    // Query to retrieve all messages for the user
    $sql = "SELECT * FROM notification_message WHERE user_id = $userId ORDER BY timestamp DESC";
    $result = $conn->query($sql);
} else {
    // Handle the case when the user is not logged in
    header("Location: index.php"); // Redirect to login page
    exit;
}

// ... (Your existing code for displaying the page header)

// Display the user's messages
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Display each message here, e.g., echo $row["message"];
    }
} else {
    echo "No messages to display.";
}

// ... (Your existing code for displaying the page footer)

// Close the database connection
$conn->close();
?>

