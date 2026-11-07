<?php
include "database.php";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = isset($_GET["registered"]) ? "Registration successful. You can now login." : "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = trim($_POST["student_id"]);
    $password = $_POST["password"];

    if ($student_id === "" || $password === "") {
        $error = "Please enter your Student ID and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {
                session_regenerate_id(true);
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["full_name"] = $user["full_name"];
                header("Location: index.php");
                exit;
            }
        }

        $error = "Incorrect Student ID or password.";
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack: Student Task and Study Planner | Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
<div class="auth-container">
    <a class="auth-logo" href="index.php" aria-label="StudyTrack home">
        <span class="logo-icon">S</span>
        <span>
            <strong>StudyTrack</strong>
            <small>Student Planner</small>
        </span>
    </a>

    <div class="auth-card">
        <p class="small-title">WELCOME BACK</p>
        <h1>Login</h1>
        <p class="auth-subtitle">Login to manage your school tasks and notes.</p>

        <?php if ($success): ?>
            <div class="success-message" role="status" aria-live="polite"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-message transient-message" role="alert" aria-live="assertive"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" id="loginForm" data-loading="Signing in…" novalidate>
            <div class="form-group">
                <label for="login_student_id">Student ID</label>
                <input type="text" name="student_id" id="login_student_id" autocomplete="username" required>
            </div>

            <div class="form-group">
                <label for="login_password">Password</label>
                <input type="password" name="password" id="login_password" autocomplete="current-password" required>
            </div>

            <div class="forgot-link">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>

            <button type="submit" class="primary-button full-button">Login</button>
        </form>

        <p class="auth-link">
            Don't have an account?
            <a href="register.php">Register</a>
        </p>
    </div>
</div>
<footer class="auth-footer">We confirm this is our own work and we are present today.</footer>
<script src="script.js"></script>
</body>
</html>
