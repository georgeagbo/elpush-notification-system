<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $phone = $_POST["phone"];
    $passcode = $_POST["passcode"];
    $email = $_POST["email"];

    // Hash the passcode (You should use a more secure hashing method)
    $hashedPasscode = hash("sha256", $passcode);

    // Database connection information
    $servername = "localhost";
	$username = "cetvetar_elpush";
	$password = "Hadolk01@";
	$database = "cetvetar_elpush"; // Corrected variable name

    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to insert data into the user table
    $sql = "INSERT INTO users (phone_no, passcode, email, payment_status) VALUES (?, ?, ?, 0)";

    // Prepare and execute the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $phone, $hashedPasscode, $email);
        if ($stmt->execute()) {
            $registrationSuccess = true;
        } else {
            $registrationError = "Registration failed. Please try again later.";
        }
        $stmt->close();
    } else {
        $registrationError = "Database error. Please try again later.";
    }

    // Close the database connection
    $conn->close();
}

// Generate a dynamic URL
$dynamicURL = "http://" . $_SERVER['HTTP_HOST'] . "/update/index.php";

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="style/styles.css">
    <!-- Include Bootstrap CSS and JavaScript -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .registration-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card registration-form">
                    <div class="card-header bg-primary text-white text-center"><img src="images/logo.png" align="left"><h3>Elpush Registration</h3></div>
                    <div class="card-body">
						<?php
                        if (isset($registrationSuccess) && $registrationSuccess) {
                            echo '<div class="alert alert-success">Registration successful! <a href="' . $dynamicURL . '">Click here to login</a></div>';
                        } elseif (isset($registrationError)) {
                            echo '<div class="alert alert-danger">' . $registrationError . '</div>';
                        }
                        ?>
                        <form action="register.php" method="post">
                            <div class="form-group">
                                <label for="phone">Phone Number:</label>
                                <input type="tel" id="phone" name="phone" class="form-control form-control-lg" placeholder="Enter your phone number" required>
                            </div>
                            <div class="form-group">
                                <label for="passcode">6-Digit Passcode:</label>
                                <input type="password" id="passcode" name="passcode" class="form-control form-control-lg" pattern="\d{6}" placeholder="Enter your 6-digit passcode" required>
                            </div>
                            <div class="form-group">
                                <label for="repeat-passcode">Repeat Passcode:</label>
                                <input type="password" id="repeat-passcode" name="repeat-passcode" class="form-control form-control-lg" pattern="\d{6}" placeholder="Repeat your 6-digit passcode" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address:</label>
                                <input type="email" id="email" name="email" class="form-control form-control-lg" placeholder="Enter your email address" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Register</button>
                        </form>
                        <div class="bottom-links">
                            <a href="index.php">Login</a>
                            <a href="#">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
