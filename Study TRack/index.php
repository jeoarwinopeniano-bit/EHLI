<?php
session_start();

if (!isset($_SESSION["user_id"])): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack: Student Task and Study Planner</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="landing-page">
    <header class="landing-header">
        <a class="landing-brand" href="index.php" aria-label="StudyTrack home">
            <span class="logo-icon">S</span>
            <span><strong>StudyTrack</strong><small>Student Planner</small></span>
        </a>
        <nav class="landing-nav" aria-label="Main navigation">
            <a href="#services">Services</a>
            <a href="login.php">Sign in</a>
            <a class="primary-button" href="register.php">Create an account</a>
        </nav>
    </header>
    <main class="landing-main">
        <section class="landing-hero">
            <div class="landing-copy">
                <p class="landing-eyebrow"><span></span> YOUR STUDY PLANNER</p>
                <h1>Stay organized.<br>Make room to learn.</h1>
                <p class="landing-description">Bring assignments, deadlines, and class notes together in one calm, easy-to-use space. StudyTrack helps you know what’s next and keep moving forward.</p>
                <div class="landing-actions">
                    <a class="primary-button landing-cta" href="register.php">Create your account <span aria-hidden="true"></span></a>
                    <a class="landing-secondary" href="#services">See how it works</a>
                </div>
                <div class="landing-proof"><span class="proof-mark" aria-hidden="true">✓</span> A simpler way to plan your study week</div>
            </div>
            <figure class="landing-visual">
                <img src="image/Cozy.jpg" alt="Study books, a graduation cap, and a student planner">
                <figcaption><span class="visual-dot"></span> Your studies, all in one place</figcaption>
            </figure>
        </section>
        <section class="landing-services" id="services">
            <div class="services-heading">
                <div>
                    <p class="small-title">MADE FOR STUDENT LIFE</p>
                    <h2>Everything in its place.</h2>
                </div>
                <p class="services-intro">Spend less time keeping track of schoolwork and more time making progress.</p>
            </div>
            <div class="service-grid">
                <article class="service-item">
                    <span class="service-number">01</span>
                    <h3>Plan your tasks</h3>
                    <p>Add assignments and deadlines, then see what needs your attention next.</p>
                </article>
                <article class="service-item">
                    <span class="service-number">02</span>
                    <h3>Track your progress</h3>
                    <p>Mark work complete and keep your workload clear and manageable.</p>
                </article>
                <article class="service-item">
                    <span class="service-number">03</span>
                    <h3>Keep useful notes</h3>
                    <p>Save class notes in one place, ready when it’s time to study.</p>
                </article>
            </div>
        </section>
    </main>
    <footer class="landing-footer">We confirm this is our own work and we are present today.</footer></body>
</html>
<?php exit; endif;

require_once "database.php";
$user_id = (int) $_SESSION["user_id"];
$full_name = $_SESSION["full_name"];

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tasks WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totalTasks = $stmt->get_result()->fetch_assoc()["total"];
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tasks WHERE user_id = ? AND completed = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pendingTasks = $stmt->get_result()->fetch_assoc()["total"];
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tasks WHERE user_id = ? AND completed = 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$completedTasks = $stmt->get_result()->fetch_assoc()["total"];
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notes WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totalNotes = $stmt->get_result()->fetch_assoc()["total"];
$stmt->close();

$stmt = $conn->prepare(
    "SELECT title, description, deadline FROM tasks
     WHERE user_id = ? AND completed = 0
     ORDER BY deadline ASC LIMIT 5"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$upcomingTasks = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack: Student Task and Study Planner | Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="app-layout">
<aside class="sidebar">
    <div class="logo">
        <div class="logo-icon">S</div>
        <div>
            <h2>StudyTrack</h2>
            <p>Student Planner</p>
        </div>
    </div>

    <nav class="navigation">
        <a href="index.php" class="nav-link active"><span class="nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg></span>Dashboard</a>
        <a href="task/tasks.php" class="nav-link"><span class="nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg></span>Tasks</a>
        <a href="notes/notes.php" class="nav-link"><span class="nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zM14 3v5h5M8 12h8M8 16h8"/></svg></span>Notes</a>
    </nav>

    <div class="sidebar-bottom">
        <p>Signed in as</p>
        <strong><?php echo htmlspecialchars($full_name); ?></strong>
        <a href="logout.php" class="logout-link" data-logout-confirm>Logout</a>
    </div>
</aside>

<main class="main-content">
    <div class="top-section dashboard-top">
        <div>
            <p class="small-title">STUDENT DASHBOARD</p>
            <h1>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
            <p class="subtitle">Organize your school tasks, deadlines, and study notes.</p>
        </div>
        <a href="task/tasks.php" class="primary-button">+ Add Task</a>
    </div>

    <section class="welcome-card">
        <div>
            <p class="card-label">KEEP MOVING FORWARD</p>
            <h2>Stay organized and stay on track.</h2>
            <p>Manage your school activities in one simple place.</p>
        </div>
    </section>

    <section class="stats-grid">
        <div class="stat-card">
            <div><p>Total Tasks</p><h2><?php echo $totalTasks; ?></h2></div>
        </div>

        <div class="stat-card">
            <div><p>Pending Tasks</p><h2><?php echo $pendingTasks; ?></h2></div>
        </div>

        <div class="stat-card">
            <div><p>Completed</p><h2><?php echo $completedTasks; ?></h2></div>
        </div>

        <div class="stat-card">
            <div><p>Study Notes</p><h2><?php echo $totalNotes; ?></h2></div>
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <div>
                <p class="card-label">YOUR SCHEDULE</p>
                <h2>Upcoming Tasks</h2>
            </div>
            <a href="task/tasks.php" class="text-button">View All →</a>
        </div>

        <div class="task-list">
            <?php if ($upcomingTasks->num_rows > 0): ?>
                <?php while ($task = $upcomingTasks->fetch_assoc()): ?>
                    <div class="task-card">
                        <div class="task-info">
                            <div class="task-check"></div>
                            <div>
                                <h3><?php echo htmlspecialchars($task["title"]); ?></h3>
                                <p><?php echo htmlspecialchars($task["description"]); ?></p>
                            </div>
                        </div>
                        <div class="deadline">
                            <?php echo date("M d, Y", strtotime($task["deadline"])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No upcoming tasks</h3>
                    <p>You are all caught up!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer>We confirm this is our own work and we are present today.</footer>
</main>
<script src="script.js"></script>
</body>
</html>