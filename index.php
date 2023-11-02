<?php
session_start(); // Start the PHP session (if not already started)

// Check if the user is already logged in, redirect to dashboard if true
if (isset($_SESSION["user_phone"])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $phone = $_POST["phone"];
    $code = $_POST["code"];

    // SQL query to check if the user exists
    $sql = "SELECT phone_no FROM users WHERE phone_no = ? AND passcode = ?";

    // Prepare and execute the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $phone, hash("sha256", $code));
        $stmt->execute();
        $stmt->store_result();

        // If the user exists, store their phone number in a session variable
        if ($stmt->num_rows == 1) {
            $_SESSION["user_phone"] = $phone;
            header("Location: dashboard.php");
            exit;
        } else {
            $loginError = "Invalid phone number or code. Please try again.";
        }
        $stmt->close();
    } else {
        $loginError = "Database error. Please try again later.";
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elpush Login Panel</title>
    <link rel="stylesheet" href="style/styles.css">
    <!-- Include Bootstrap CSS and JavaScript -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .login-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .checkbox-label {
            font-size: 18px;
        }

        .bottom-links {
            text-align: center;
            margin-top: 10px;
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
            <div class="col-md-12">
                <div class="card login-form">
                    <div class="card-header bg-primary text-white text-center"><img src="images/logo.png" align="left"><h3>Elpush Login Panel</h3></div>
                    <div class="card-body">
						<?php
                        if (isset($loginError)) {
                            echo '<div class="alert alert-danger">' . $loginError . '</div>';
                        }
                        ?>
                        <form action="index.php" method="post">
                            <div class="form-group">
                                <label for="phone">Phone Number:</label>
                                <input type="tel" id="phone" name="phone" class="form-control form-control-lg" placeholder="Enter your phone number" required>
                            </div>
                            <div class="form-group">
                                <label for="code">6-Digit Code:</label>
                                <input type="password" id="code" name="code" class="form-control form-control-lg" pattern="\d{6}" placeholder="Enter your 6-digit code" required>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember">
                                    <label class="form-check-label checkbox-label" for="remember">Remember Password</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
                        </form>
                        <div class="bottom-links">
                            <a href="#">Forgot Password</a>
                            <a href="register.php">Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
