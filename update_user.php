<?php
session_start();

// Replace these values with your actual database credentials
$servername = "localhost";
$username = "cetvetar_elpush";
$password = "Hadolk01@";
$database = "cetvetar_elpush"; // Corrected variable name

// Create a database connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from the session
    $userId = $_SESSION["user_phone"];

    // Get form data
    $locations = $_POST["location"]; // Assuming "location" is an array
    $minValue = $_POST["min_range"];
    $maxValue = $_POST["max_range"];

    // Update the users table with the new data
    $sqlUpdateUser = "UPDATE users SET min_value = ?, max_value = ? WHERE phone_no = ?";
    
    $stmtUpdateUser = $conn->prepare($sqlUpdateUser);
    $stmtUpdateUser->bind_param("sss", $minValue, $maxValue, $userId);

    if ($stmtUpdateUser->execute()) {
        // Now, let's update the user_locations table
        // Clear existing locations for the user
        $sqlDeleteUserLocations = "DELETE FROM user_locations WHERE user_id = ?";
        $stmtDeleteUserLocations = $conn->prepare($sqlDeleteUserLocations);
        $stmtDeleteUserLocations->bind_param("s", $userId);
        $stmtDeleteUserLocations->execute();

        foreach ($locations as $location) {
            // Retrieve the location ID from the database
            $sqlRetrieveLocationId = "SELECT id FROM locations WHERE name = ?";
            $stmtRetrieveLocationId = $conn->prepare($sqlRetrieveLocationId);
            $stmtRetrieveLocationId->bind_param("s", $location);
            $stmtRetrieveLocationId->execute();
            $stmtRetrieveLocationId->store_result();

            if ($stmtRetrieveLocationId->num_rows > 0) {
                $stmtRetrieveLocationId->bind_result($locationId);
                $stmtRetrieveLocationId->fetch();

                // Insert into user_locations table
                $sqlInsertUserLocation = "INSERT INTO user_locations (user_id, location_id) VALUES (?, ?)";
                $stmtInsertUserLocation = $conn->prepare($sqlInsertUserLocation);
                $stmtInsertUserLocation->bind_param("ss", $userId, $locationId);
                $stmtInsertUserLocation->execute();
            }
        }

        // Set a success message in the session
        $_SESSION["success_message"] = "Notification settings updated successfully!";
        header("Location: dashboard.php");
        exit;
    } else {
        // Set an error message in the session
        $_SESSION["error_message"] = "Error updating notification settings: " . $conn->error;

        // Log the SQL query for debugging purposes
        error_log("SQL Error: " . $sqlUpdateUser);
        error_log("SQL Error Details: " . $stmtUpdateUser->error);

        header("Location: dashboard.php");
        exit;
    }
}
?>
