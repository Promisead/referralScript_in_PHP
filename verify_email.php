<?php
session_start();

// Check if email parameter is set
if (!isset($_GET['email'])) {
    // Redirect to registration page if email parameter is missing
    header("Location: register.php");
    exit();
}

$email = $_GET['email'];

// Process verification form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = $_POST["verification_code"];
    $stored_code = ""; // Retrieve stored verification code associated with the email from the database

    // Check if the entered code matches the stored code
    if ($entered_code == $stored_code) {
        // Update the user's status to verified in the database
        // Redirect the user to a success page or the login page
        header("Location: verification_success.php");
        exit();
    } else {
        // Display error message if the verification code is incorrect
        $error_message = "Invalid verification code. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Email Verification</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        /* Additional custom styling */
        .verification-container {
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
        <div class="verification-container">
            <h2 class="text-center">Email Verification</h2>
            <?php if (isset($error_message)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <p>Please enter the verification code sent to <?php echo $email; ?>:</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?email=' . urlencode($email); ?>" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" name="verification_code" required />
                </div>
                <button type="submit" class="btn btn-primary btn-block">Verify</button>
            </form>
        </div>
    </div>

    <!-- Add Bootstrap JS (optional) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>