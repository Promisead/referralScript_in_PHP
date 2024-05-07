<?php
session_start();

// Check if the user has a registration cookie set
if (isset($_COOKIE['registration_completed'])) {
    // Redirect user or display message indicating they've already registered
    header("Location: already_registered.php"); // You need to create this file
    exit();
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = ""; // Your database password
$dbname = "referral_website"; // Your database name

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $referrer_code = $_POST["referrer_code"]; // New input for referral code

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate referral code
    $referral_code = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO users (email, password, referral_code, referrer_id) VALUES (?, ?, ?, ?)");

    // Check if $stmt is null
    if ($stmt === false) {
        die("Error: " . $conn->error);
    }

    // Check if a referral code is provided
    if (!empty($referrer_code)) {
        // Query to find referrer's user ID
        $stmt_referrer = $conn->prepare("SELECT id FROM users WHERE referral_code = ?");

        // Check if $stmt_referrer is null
        if ($stmt_referrer === false) {
            die("Error: " . $conn->error);
        }

        $stmt_referrer->bind_param("s", $referrer_code);
        $stmt_referrer->execute();
        $stmt_referrer->store_result();

        if ($stmt_referrer->num_rows > 0) {
            $stmt_referrer->bind_result($referrer_id);
            $stmt_referrer->fetch();
        } else {
            // Handle invalid referral code
            // Redirect or display an error message
        }
    } else {
        $referrer_id = null; // No referrer
    }

    // Bind parameters
    $stmt->bind_param("sssi", $email, $hashed_password, $referral_code, $referrer_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Set a cookie to indicate registration completion
        setcookie('registration_completed', 'true', time() + (86400 * 30), "/"); // Cookie expires in 30 days

        // Check if a referrer exists and credit the referrer's account
        if (!empty($referrer_id)) {
            // Credit the referrer's account with the reward amount (e.g., 5000)
            // Update the referrer's account balance in the database
        }

        // Redirect to login page
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statements
    $stmt->close();
    if (!empty($stmt_referrer)) {
        $stmt_referrer->close();
    }
}

// Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Referral Website Registration</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        /* Additional custom styling */
        .registration-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="registration-container">
            <h2 class="text-center">Register</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required />
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required />
                </div>
                <div class="form-group">
                    <label for="referrer_code">Referral Code (Optional):</label>
                    <input type="text" class="form-control" id="referrer_code" name="referrer_code" />
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
            <p>Already have an accoount? <a href="login.php">login</a></p>
        </div>
    </div>

    <!-- Add Bootstrap JS (optional) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>