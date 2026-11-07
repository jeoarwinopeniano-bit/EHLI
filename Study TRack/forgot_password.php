<?php
include "database.php";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    if ($email === "") {
        $error = "Please enter your email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Your email is registered. Please contact the administrator to reset your password.";
        } else {
            $error = "Email address was not found.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack: Student Task and Study Planner | Forgot Password</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
<div class="auth-container">
    <div class="auth-logo">
        <div class="logo-icon">S</div>
        <div>
            <h2>StudyTrack</h2>
            <p>Student Planner</p>
        </div>
    </div>

    <div class="auth-card">
        <p class="small-title">ACCOUNT RECOVERY</p>
        <h1>Forgot Password?</h1>
        <p class="auth-subtitle">Enter your registered email address.</p>

        <?php if ($message): ?>
            <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" data-loading="Checking email…">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <button type="submit" class="primary-button full-button">Check Email</button>
        </form>

        <p class="auth-link">
            <a href="login.php">← Back to Login</a>
        </p>
    </div>
</div>
<footer class="auth-footer">We confirm this is our own work and we are present today.</footer>
<script src="script.js"></script>
</body>
</html>