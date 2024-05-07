<?php
// dashboard.php

session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "referral_website";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user's information
$email = $_SESSION['email'];
$stmt_user = $conn->prepare("SELECT id, email, referral_code FROM users WHERE email = ?");
$stmt_user->bind_param("s", $email);
$stmt_user->execute();
$stmt_user->store_result();

if ($stmt_user->num_rows > 0) {
    $stmt_user->bind_result($user_id, $user_email, $referral_code);
    $stmt_user->fetch();
} else {
    // Redirect if user not found
    header("Location: login.php");
    exit();
}

// Close statement
$stmt_user->close();
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function copyReferralCode() {
            var referralCodeInput = document.getElementById('referral-code');
            referralCodeInput.select();
            document.execCommand('copy');
            alert('Referral code copied to clipboard!');
        }

        function copyReferralLink() {
            var referralCodeInput = document.getElementById('referral-link');
            referralCodeInput.select();
            document.execCommand('copy');
            alert('Referral link copied to clipboard!');
        }
    </script>
</head>

<body>
    <div class="container">
        <h2 class="mt-5">Welcome to the Dashboard</h2>
        <p>Hello, <?php echo $user_email; ?>!</p>
        <div class="row">
            <div class="col-md-6">
                <p>Your Referral Code:</p>
                <div class="input-group mb-3">
                    <input type="text" id="referral-code" class="form-control" value="<?php echo $referral_code; ?>" readonly>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyReferralCode()">Copy</button>
                    </div>
                </div>
            </div>
        </div>
        <h3 class="mt-4">Successful Referrals:</h3>
        <ul class="list-group">
            <?php
            // Fetch information about successful referrals
            $conn = new mysqli($servername, $username, $password, $dbname);
            $stmt_referrals = $conn->prepare("SELECT referred_user.email FROM users AS referrer_user INNER JOIN users AS referred_user ON referrer_user.id = referred_user.referrer_id WHERE referrer_user.id = ?");
            $stmt_referrals->bind_param("i", $user_id);
            $stmt_referrals->execute();
            $stmt_referrals->store_result();

            if ($stmt_referrals->num_rows > 0) {
                $stmt_referrals->bind_result($referred_email);
                while ($stmt_referrals->fetch()) {
                    echo "<li class='list-group-item'>$referred_email</li>";
                }
            } else {
                echo "<li class='list-group-item'>No successful referrals yet.</li>";
            }

            // Close statement
            $stmt_referrals->close();
            $conn->close();
            ?>
        </ul>
        <div class="form-group mt-3">
            <label for="referral-link">Shareable Link:</label>
            <div class="input-group">
                <input type="text" id="referral-link" class="form-control" value="http://localhost/mzeeReloadly/referral/register.php?referral_code=<?php echo $referral_code; ?>" readonly>
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" onclick="copyReferralLink()">Copy</button>
                </div>
            </div>
        </div>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Add Bootstrap JS (optional) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>