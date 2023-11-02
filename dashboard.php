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
    
    // Query to retrieve the user's email from the database
    $sql = "SELECT email FROM users WHERE phone_no = $userId"; // Adjust the table and column names as needed
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userEmail = $row["email"];
    } else {
        $userEmail = "User Email Not Found"; // Provide a default value or error message
    }
} else {
    $userEmail = "User ID Not Found"; // Provide a default value or error message
}

// Assuming you have a user ID in your session data
if (isset($_SESSION["user_phone"])) {
    $userId = $_SESSION["user_phone"];

    // Query to count the number of unread messages for the user
    $sql = "SELECT COUNT(*) as unread_count FROM notification_message WHERE user_id = $userId AND is_read = 0";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $unreadCount = $row["unread_count"];
    } else {
        $unreadCount = 0;
    }
} else {
    $unreadCount = 0;
}
$successMessage = isset($_SESSION["success_message"]) ? $_SESSION["success_message"] : "";

// Check if there is an error message in the session
$errorMessage = isset($_SESSION["error_message"]) ? $_SESSION["error_message"] : "";

// Clear the session messages so they don't show again on page refresh
unset($_SESSION["success_message"]);
unset($_SESSION["error_message"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style/styles.css">
    <!-- Include Bootstrap CSS and JavaScript -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .slider-container {
            position: relative;
        }

        .slider {
            position: absolute;
            top: 50%;
            width: 70%;
            transform: translateY(-50%);
        }

        .bottom-links {
            text-align: center;
            margin-top: 20px;
        }

        .bottom-links a {
            margin-right: 20px;
            text-decoration: none;
            font-size: 16px;
        }

        input[type="number"] {
            width: 70%;
        }

        /* Use a more specific selector for the select element */
        .form-group.slider-container select.custom-select {
            width: 50%;
        }
		.location-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .location-checkbox {
        display: flex;
        align-items: center;
        background-color: #D5F1FF;
        padding: 12px; /* Increased padding for a larger text box */
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .location-checkbox:hover {
        background-color: #009BE6;
    }

    .location-checkbox input {
        margin-right: 5px;
    }

    label {
        color: #333;
    }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <img src="images/logo.png" align="left">
                        <h3>Dashboard</h3>
                    </div>
                    <div class="card-body">
                        <h4>Welcome, <?php echo $userEmail; ?>!</h4>
                        <p>You are logged in to your dashboard.</p>

                        <?php
                        // Display success message if available
                        if ($successMessage) {
                            echo '<div class="alert alert-success" role="alert">' . $successMessage . '</div>';
                        }

                        // Display error message if available
                        if ($errorMessage) {
                            echo '<div class="alert alert-danger" role="alert">' . $errorMessage . '</div>';
                        }
                        ?>
                        <div class="alert alert-success" role="alert">
                            You have <?php echo $unreadCount; ?> new messages.
                        </div>

                        <!-- Add buttons for View Detailed Data, Payments, and Profile -->
                        <button id="view-messages-btn" class="btn btn-info">View Messages</button>
                        <button id="view-detailed-data-btn" class="btn btn-success">View Detailed Data</button>
                        <button id="payments-btn" class="btn btn-warning">Payments</button>
                        <button id="profile-btn" class="btn btn-info">Profile</button>

                        <div id="message-container"></div>

                        <form action="update_user.php" method="post">
                           <div class="form-group">
							<label>Select the Preferred Location(s) for Notification:</label>
							<div class="location-container" id="location-container"></div>
							</div>

                            <div class="form-group slider-container">
                                <label for="min-range">Minimum Range:</label><br />
                                <input type="number" id="min-range" name="min_range" class="form-control" min="0" max="500" step="0.01" placeholder="Min" required>
                                <input type="range" class="form-control-range slider" id="min-slider" min="0" max="500" step="0.01">
                            </div>
                            <div class="form-group slider-container">
                                <label for="max-range">Maximum Range:</label>
                                <input type="number" id="max-range" name="max_range" class="form-control" min="0" max="500" step="0.01" placeholder="Max" required>
                                <input type="range" class="form-control-range slider" id="max-slider" min="0" max="500" step="0.01">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Notification</button>
                        </form>
						<a href="logout.php" class="btn btn-danger mt-3">Logout</a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <script>
	    // JavaScript to populate the location drop-down menu
    // JavaScript to populate the location buttons
    $(document).ready(function () {
    $.ajax({
        url: 'https://api.energidataservice.dk/dataset/Elspotprices?limit=5',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            const locationContainer = document.getElementById('location-container');

            data.records.forEach(function (item) {
                const checkboxContainer = document.createElement('div');
                checkboxContainer.classList.add('location-checkbox');

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'location[]'; // Note the square brackets for an array
                checkbox.value = item.PriceArea;

                const label = document.createElement('label');
                label.textContent = item.PriceArea;

                checkboxContainer.appendChild(checkbox);
                checkboxContainer.appendChild(label);
                locationContainer.appendChild(checkboxContainer);
            });
        },
        error: function (error) {
            console.error('Error fetching data:', error);
        }
    });
});    // JavaScript to handle on/off button behavior
    $(document).on('click', '.location-checkbox', function () {
        // Toggle the class to change the button appearance
        $(this).toggleClass('btn-on');
    });

        // JavaScript to synchronize the sliders with the text input fields
        const minRangeInput = document.getElementById('min-range');
        const maxRangeInput = document.getElementById('max-range');
        const minSlider = document.getElementById('min-slider');
        const maxSlider = document.getElementById('max-slider');

        // When the sliders change, update the corresponding text inputs
        minSlider.addEventListener('input', () => {
            minRangeInput.value = parseFloat(minSlider.value).toFixed(2);
        });

        maxSlider.addEventListener('input', () => {
            maxRangeInput.value = parseFloat(maxSlider.value).toFixed(2);
        });

        // When the text inputs change, update the corresponding sliders
        minRangeInput.addEventListener('input', () => {
            minSlider.value = parseFloat(minRangeInput.value).toFixed(2);
        });

        maxRangeInput.addEventListener('input', () => {
            maxSlider.value = parseFloat(maxRangeInput.value).toFixed(2);
        });

        // Function to load messages
        function loadMessages() {
            $.ajax({
                url: 'view_messages.php',
                method: 'GET',
                dataType: 'html',
                success: function (data) {
                    $('#message-container').html(data);
                },
                error: function (error) {
                    console.error('Error loading messages:', error);
                }
            });
        }

        // Function to load detailed data
        function loadDetailedData() {
            $.ajax({
                url: 'detailed_data.php',
                method: 'GET',
                dataType: 'html',
                success: function (data) {
                    $('#message-container').html(data);
                },
                error: function (error) {
                    console.error('Error loading detailed data:', error);
                }
            });
        }

        // Function to load payments
        function loadPayments() {
            $.ajax({
                url: 'payments.php',
                method: 'GET',
                dataType: 'html',
                success: function (data) {
                    $('#message-container').html(data);
                },
                error: function (error) {
                    console.error('Error loading payments:', error);
                }
            });
        }

        // Function to load profile
        function loadProfile() {
            $.ajax({
                url: 'profile.php',
                method: 'GET',
                dataType: 'html',
                success: function (data) {
                    $('#message-container').html(data);
                },
                error: function (error) {
                    console.error('Error loading profile:', error);
                }
            });
        }

        // Call the loadMessages function when the "View Messages" button is clicked
        $('#view-messages-btn').click(function () {
            loadMessages();
        });

        // Call the loadDetailedData function when the "View Detailed Data" button is clicked
        $('#view-detailed-data-btn').click(function () {
            loadDetailedData();
        });

        // Call the loadPayments function when the "Payments" button is clicked
        $('#payments-btn').click(function () {
            loadPayments();
        });

        // Call the loadProfile function when the "Profile" button is clicked
        $('#profile-btn').click(function () {
            loadProfile();
        });
    </script>
</body>
</html>
