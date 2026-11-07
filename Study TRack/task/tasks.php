<?php
include "../database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT * FROM tasks WHERE user_id = ? ORDER BY completed ASC, deadline ASC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tasks = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack: Student Task and Study Planner | Tasks</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="app-layout">
<aside class="sidebar">
    <div class="logo">
        <div class="logo-icon">S</div>
        <div><h2>StudyTrack</h2><p>Student Planner</p></div>
    </div>

    <nav class="navigation">
        <a href="../index.php" class="nav-link"><span class="nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg></span>Dashboard</a>
        <a href="../task/tasks.php" class="nav-link active"><span class="nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg></span>Tasks</a>
        <a href="../notes/notes.php" class="nav-link"><span class="nav-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zM14 3v5h5M8 12h8M8 16h8"/></svg></span>Notes</a>
    </nav>

    <div class="sidebar-bottom">
        <p>Signed in as</p>
        <strong><?php echo htmlspecialchars($_SESSION["full_name"]); ?></strong>
        <a href="../logout.php" class="logout-link" data-logout-confirm>Logout</a>
    </div>
</aside>

<main class="main-content">
    <div class="top-section">
        <div>
            <p class="small-title">TASK MANAGEMENT</p>
            <h1>My Tasks</h1>
            <p class="subtitle">Add and manage your school activities.</p>
        </div>
    </div>

    <?php
$statusMessages = ["saved" => "Task added successfully!", "completed" => "Task completed!", "deleted" => "Task deleted successfully!", "error" => "We could not complete that task action. Please try again."];
$status = $_GET["status"] ?? "";
if (isset($statusMessages[$status])): ?>
    <div class="<?php echo $status === "error" ? "error-message" : "success-message"; ?> transient-message" role="<?php echo $status === "error" ? "alert" : "status"; ?>" aria-live="<?php echo $status === "error" ? "assertive" : "polite"; ?>"><?php echo htmlspecialchars($statusMessages[$status]); ?></div>
<?php endif; ?>
<section class="form-card">
        <div class="section-heading">
            <div><p class="card-label">NEW TASK</p><h2>Add a Task</h2></div>
        </div>

        <form action="add_task.php" method="POST" id="taskForm" data-loading="Adding task…">
            <div class="form-group">
                <label for="title">Task Title</label>
                <input type="text" name="title" id="title" placeholder="Enter task title" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" placeholder="Enter task description" required></textarea>
            </div>

            <div class="form-group">
                <label for="deadline">Deadline</label>
                <input type="date" name="deadline" id="deadline" required>
            </div>

            <button type="submit" class="primary-button">Add Task</button>
        </form>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <div><p class="card-label">YOUR TASKS</p><h2>Task List</h2></div>
        </div>

        <div class="task-list">
            <?php if ($tasks->num_rows > 0): ?>
                <?php while ($task = $tasks->fetch_assoc()): ?>
                    <div class="task-card <?php echo $task["completed"] ? "completed-task" : ""; ?>">
                        <div class="task-info">
                            <div class="task-check"><?php echo $task["completed"] ? "✓" : ""; ?></div>
                            <div>
                                <h3><?php echo htmlspecialchars($task["title"]); ?></h3>
                                <p><?php echo htmlspecialchars($task["description"]); ?></p>
                                <div class="task-date">
                                    <span class="task-status <?php echo $task["completed"] ? "status-complete" : "status-pending"; ?>"><?php echo $task["completed"] ? "Completed" : "Pending"; ?></span> · Deadline: <?php echo date("M d, Y", strtotime($task["deadline"])); ?>
                                </div>
                            </div>
                        </div>

                        <div class="task-actions">
                            <?php if (!$task["completed"]): ?>
                                <form action="complete_task.php" method="POST" data-loading="Saving…">
                                    <input type="hidden" name="id" value="<?php echo $task["id"]; ?>">
                                    <button type="submit" class="complete-button">Complete</button>
                                </form>
                            <?php endif; ?>

                            <form action="delete_task.php" method="POST" data-confirm="Are you sure you want to delete this task?" data-loading="Deleting…">
                                <input type="hidden" name="id" value="<?php echo $task["id"]; ?>">
                                <button type="submit" class="delete-button">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No tasks yet</h3>
                    <p>No tasks yet. Add one to get started!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer>We confirm this is our own work and we are present today.</footer>
</main>
<script src="../script.js"></script>
</body>
</html>