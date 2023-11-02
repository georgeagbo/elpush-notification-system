<?php
// Start the PHP session (if not already started)
session_start();

// Check if the user is logged in
if (isset($_SESSION["user_phone"])) {
    // If the user is logged in, destroy the session
    session_destroy();

    // Redirect the user to the login page
    header("Location: index.php"); // Change "index.php" to your login page's filename
    exit;
} else {
    // If the user is not logged in, redirect them to the login page as a fallback
    header("Location: index.php"); // Change "index.php" to your login page's filename
    exit;
}
?>

