<?php
include "database.php";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";
$step = isset($_GET["step"]) && $_GET["step"] === "password" ? 2 : 1;
$registration = $_SESSION["registration"] ?? [];
if ($step === 2 && empty($registration) && $_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: register.php"); exit; }
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $postedStep = (int) ($_POST["step"] ?? 1);
    if ($postedStep === 1) {
        $student_id = trim($_POST["student_id"] ?? ""); $full_name = trim($_POST["full_name"] ?? ""); $email = trim($_POST["email"] ?? "");
        if ($student_id === "" || $full_name === "" || $email === "") $error = "Please complete all fields.";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = "Please enter a valid email.";
        else {
            $check = $conn->prepare("SELECT id FROM users WHERE student_id = ? OR email = ?"); $check->bind_param("ss", $student_id, $email); $check->execute(); $exists = $check->get_result()->num_rows > 0; $check->close();
            if ($exists) $error = "Student ID or email is already registered.";
            else { $_SESSION["registration"] = ["student_id" => $student_id, "full_name" => $full_name, "email" => $email]; header("Location: register.php?step=password"); exit; }
        }
        $registration = ["student_id" => $student_id, "full_name" => $full_name, "email" => $email];
    } elseif ($postedStep === 2 && !empty($registration)) {
        $password = $_POST["password"] ?? ""; $confirm_password = $_POST["confirm_password"] ?? "";
        if ($password === "" || $confirm_password === "") $error = "Please enter and confirm your password.";
        elseif (strlen($password) < 8) $error = "Use a password with at least 8 characters.";
        elseif ($password !== $confirm_password) $error = "Passwords do not match. Please try again.";
        else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (student_id, full_name, email, password) VALUES (?, ?, ?, ?)"); $stmt->bind_param("ssss", $registration["student_id"], $registration["full_name"], $registration["email"], $hashed_password);
            if ($stmt->execute()) { unset($_SESSION["registration"]); header("Location: login.php?registered=1"); exit; }
            $error = "We couldn't create your account. Please try again."; $stmt->close();
        }
        $step = 2;
    } else { header("Location: register.php"); exit; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack: Student Task and Study Planner | Register</title>
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
        <p class="small-title">CREATE ACCOUNT · STEP <?php echo $step; ?> OF 2</p>
        <h1><?php echo $step === 1 ? "Your details" : "Create a password"; ?></h1>
        <p class="auth-subtitle"><?php echo $step === 1 ? "Start with your student and contact details." : "Choose a password for your account."; ?></p>

        <?php if ($error): ?>
            <div class="error-message transient-message" role="alert" aria-live="assertive"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" id="registerForm" data-loading="<?php echo $step === 1 ? "Checking details…" : "Creating account…"; ?>" novalidate>
            <input type="hidden" name="step" value="<?php echo $step; ?>">
            <?php if ($step === 1): ?>
                <div class="form-group"><label for="student_id">Student ID</label><input type="text" name="student_id" id="student_id" value="<?php echo htmlspecialchars($registration["student_id"] ?? ""); ?>" required></div>
                <div class="form-group"><label for="full_name">Full Name</label><input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($registration["full_name"] ?? ""); ?>" required></div>
                <div class="form-group"><label for="email">Email</label><input type="email" name="email" id="email" value="<?php echo htmlspecialchars($registration["email"] ?? ""); ?>" required></div>
                <button type="submit" class="primary-button full-button">Next</button>
            <?php else: ?>
                <div class="registration-summary"><strong><?php echo htmlspecialchars($registration["full_name"]); ?></strong><span><?php echo htmlspecialchars($registration["student_id"]); ?> · <?php echo htmlspecialchars($registration["email"]); ?></span></div>
                <div class="form-group"><label for="password">Password</label><input type="password" name="password" id="password" autocomplete="new-password" minlength="8" required></div>
                <div class="form-group"><label for="confirm_password">Confirm Password</label><input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password" minlength="8" required></div>
                <a class="back-link" href="register.php">← Back to your details</a>
                <button type="submit" class="primary-button full-button">Create Account</button>
            <?php endif; ?>
        </form>

        <p class="auth-link">
            Already have an account?
            <a href="login.php">Login</a>
        </p>
    </div>
</div>
<footer class="auth-footer">We confirm this is our own work and we are present today.</footer>
<script src="script.js"></script>
</body>
</html>
