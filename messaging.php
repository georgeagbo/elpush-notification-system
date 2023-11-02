<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Firebase Admin SDK initialization
require __DIR__.'/vendor/autoload.php'; // Include Firebase PHP SDK

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

// Firebase Service Account Initialization
$factory = (new Factory())->withServiceAccount(__DIR__.'/elpushupdate-firebase-adminsdk-tm17q-f2819fca9c.json');
$firebase = $factory->createMessaging();

// MySQL Database Connection
$servername = "localhost";
$username = "cetvetar_elpush";
$password = "Hadolk01@";
$database = "cetvetar_elpush"; // Corrected variable name

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch Data from the API
$apiUrl = "https://api.energidataservice.dk/dataset/Elspotprices?limit=5";

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Echo the API response for debugging purposes
echo "API Response: " . $response . PHP_EOL;

$data = json_decode($response, true);

// Log decoded API data
echo "Decoded API Data: " . json_encode($data) . PHP_EOL;

// Check if $data is not empty and if key 0 exists
if (!empty($data) && isset($data[0])) {
    $spotPriceEUR = $data[0]['SpotPriceEUR'];

    // Retrieve User Data from MySQL
    $query = "SELECT phone_no, min_value, max_value, firebase_user_id FROM users WHERE payment_status = 1";
    $result = mysqli_query($conn, $query);

    // Log before MySQL query
    echo "Before MySQL Query" . PHP_EOL;

    if ($result) {
        // Log after MySQL query
        echo "After MySQL Query. Number of rows: " . mysqli_num_rows($result) . PHP_EOL;

        foreach ($result as $row) {
            $userMinValue = $row['min_value'];
            $userMaxValue = $row['max_value'];
            $userPhoneNumber = $row['phone_no'];
            $firebaseUserId = $row['firebase_user_id'];

            if ($spotPriceEUR >= $userMinValue && $spotPriceEUR <= $userMaxValue) {
                // Log before sending notification
                echo "Before Sending Notification" . PHP_EOL;

                // Send Firebase Notification
                $message = CloudMessage::fromArray([
                    'notification' => [
                        'title' => 'Spot Price Alert',
                        'body' => "Spot PriceEUR is within your specified range: $spotPriceEUR",
                    ],
                    'token' => $firebaseUserId, // Use the Firebase ID from MySQL
                ]);

                $firebase->send($message);
            }
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // Handle the case where $data is empty or key 0 does not exist
    echo "Error fetching data from the API. Details: " . json_last_error_msg();
}

mysqli_close($conn);
?>
<html>
<body>
</body>
<script type="module">
  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.4.0/firebase-analytics.js";
  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  const firebaseConfig = {
    apiKey: "AIzaSyDnZQ8-dattyy527irrz0ayURkmMnI-phY",
    authDomain: "elpushupdate.firebaseapp.com",
    projectId: "elpushupdate",
    storageBucket: "elpushupdate.appspot.com",
    messagingSenderId: "174298696286",
    appId: "1:174298696286:web:0aefba4a0898a11e80c2cf",
    measurementId: "G-DDZNZM46QG"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
</script>
</html>
